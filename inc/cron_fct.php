<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : http://chili.kiwais.com/projects/bilboplanet
* Blog : www.bilboplanet.com
*
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
***** END LICENSE BLOCK *****/
?>
<?php
require_once(dirname(__FILE__).'/lib/checkValidHTML.php');

function finished() {
	$log_file = dirname(__FILE__).'/../logs/cron_job.log';
	logMsg("The Cron is stopped and exited", $log_file);
}

function update($core, $print=false) {
	global $blog_settings;
	$output = "";

	# Inclusion des fichiers necessaires
#	require_once(dirname(__FILE__).'/lib/simplepie/simplepie.inc');

	# Requete permettant de recuperer la liste des flux a parser
	$sql = "SELECT
			".$core->prefix."feed.user_id as user_id,
			user_fullname,
			feed_id,
			feed_url,
			site_url,
			feed_trust,
			feed_comment,
			feed_checked
		FROM ".$core->prefix."feed, ".$core->prefix."site, ".$core->prefix."user
		WHERE
			".$core->prefix."feed.site_id = ".$core->prefix."site.site_id
			AND ".$core->prefix."feed.user_id = ".$core->prefix."user.user_id
			AND user_status = 1
			AND site_status = 1
			AND feed_status = 1
		ORDER BY feed_checked ASC
		LIMIT 50";
	$rs = $core->con->select($sql);

	# Affichage des logs dans la partie admin
	$output .= "<fieldset><legend>Log File</legend>
		<div class='message'><p>Manual Update Log</p></div>";
	$output .= getItemsFromFeeds($rs, $print);
	$output .= "</fieldset>";

	# On detruit les fichiers de cache des pages web pour les actualiser
	$cache_dir = dirname(__FILE__).'/../admin/cache';
	$dir_handle = @opendir($cache_dir) or die("Unable to open $cache_dir");
	while ($file = readdir($dir_handle)){
		if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
			unlink($cache_dir.'/'.$file);
		}
	}
	closedir($dir_handle);

	# On met a jour la date d'update
	updateDateMaj();

	return $output;
}

function getItemsFromFeeds ($rs, $print) {
	global $blog_settings, $core;
	$output = "";
	$cron_file = dirname(__FILE__).'/cron_running.txt';

	# Duree de mise a jour
	$debut = explode(" ",microtime());
	$debut = $debut[1]+$debut[0];

	$cpt = 0;
	while ($rs->fetch()) {
		# On verifie si on n'a pas demandé l'arrêt de l'algo
		if (file_exists(dirname(__FILE__).'/STOP')) {
			$log_msg = logMsg("STOP file detected, trying to shut down cron job", "", 2, $print);
			if ($print) $output .= $log_msg;
			break;
		}
//		echo $rs->feed_url."\n";

//		require_once(dirname(__FILE__).'/lib/simplepie/SimplePieAutoloader.php');
		require_once(dirname(__FILE__).'/lib/simplepie_1.3.compiled.php');
		# On cree un objet SimplePie et on ajuste les parametres de base
		$feed = new SimplePie();
		$feed->set_feed_url($rs->feed_url);
		$feed->set_cache_location(dirname(__FILE__).'/../admin/cache');
#$feed->enable_cache(false);
#		$feed->set_cache_duration($item_refresh);
		$feed->init();

		# Pour faire fonctionner les lecteurs flash, non recomande par simplepie
		$feed->strip_htmltags(false);

		# Si le flux ne contient pas  de donnee
		#$item_nb = $feed->get_item_quantity();
		$error = $feed->error();
		if (isset($error)) {

			# Affichage du message d'erreur
			if (ereg($rs->feed_url, $error)) {
				$log_msg = logMsg("Aucun article trouve ".$error, "", 3, $print);
			} else {
				$log_msg = logMsg("Aucun article trouve sur $rs->feed_url ($rs->user_id): ".$error, "", 3, $print);
			}
			if ($print) $output .= $log_msg;
		} else {

			$items = $feed->get_items();
			foreach ($items as $item) {
				#print $item->get_permalink().'<br>';
				#continue;

				# open log file and write activity down
				$fp = @fopen($cron_file,'wb');
				if ($fp === false) {
					throw new Exception(sprintf(__('Cannot write %s file.'),$cron_file));
				}
				fwrite($fp,time());
				fclose($fp);

				# Analyse the item
				#####################

				# Content
				$item_content = strip_script($item->get_content());
				if (empty($item_content)) {
					$item_content = $item->get_description();
				}
				$item_content = traitementEncodage($item_content);
				# Title
				$item_title = traitementEncodage($item->get_title());
				if(strlen($item_title) > 254) {
					$item_title = substr($item_title, 0, 254);
				}
				# Permalink
				$permalink = $item->get_permalink();

				$item_image = getFirstPostImageUrl($item->get_content());

				# Analyse the possible tags of the item
				##########################################
				# Get tags defined in the database for all posts of that feed
				$item_tags = array();

				# Get the tags on the feed
				$rs_feed_tag = $core->con->select("SELECT tag_id FROM ".$core->prefix."feed_tag
					WHERE feed_id = ".$rs->feed_id);
				while($rs_feed_tag->fetch()) {
					$item_tags[] = strtolower($rs_feed_tag->tag_id);
				}

				# Get the reserved tags
				$reserved_tags = array();
				$planet_tags = getArrayFromList($blog_settings->get('planet_reserved_tags'));
				if (is_array($planet_tags)) {
					foreach ($planet_tags as $tag) {
						$reserved_tags[] = strtolower($tag);
					}
				}

				# Get tags defined on the item in the feed
				$categs = $item->get_categories();
				if ($categs) {
					foreach ($categs as $category) {
						$label = strtolower($category->get_label());
						if (!in_array($label, $item_tags)
							&& !in_array($label, $reserved_tags)
							&& !is_int($label)
							&& strlen($label) > 1){
							$item_tags[] = $label;
						}
					}
				}

				# Find hashtags in title
				$hashtags = array();
				preg_match('/#([\\d\\w]+)/', $item->get_title(), $hashtags);
				foreach ($hashtags as $tag) {
					$tag = strtolower($tag);
					if (!in_array($tag, $item_tags)
						&& !in_array($tag, $reserved_tags)
						&& !is_int($tag)
						&& strlen($tag) > 1){
						$item_tags[] = $tag;
					}
				}

				# check if some existing tags are in the title
				foreach (explode(' ', $item_title) as $word) {
					$word = strtolower($word);
					$tagRq = $core->con->select('SELECT tag_id FROM '.$core->prefix.'post_tag WHERE tag_id = \''.$word."'");
					if ($tagRq->count() > 1
						&& !in_array($word, $item_tags)
						&& !in_array($word, $reserved_tags)
						&& !is_int($word)
						&& strlen($word) > 1) {
						$item_tags[] = $word;
					}
				}

				if (empty($item_content)) {
					$log_msg = logMsg("Pas de contenu sur $rs->feed_url", "", 3, $print);
				} elseif(empty($permalink)) {
					$log_msg = logMsg("Erreur de decoupage du lien ".$permalink, "", 3, $print);
				} else {
					$log_msg = insertPostToDatabase(
						$rs,
						$permalink,
						$item->get_date("U"),
						$item_title,
						$item_content,
						$item_tags,
						$item_image,
						$print,
						$rs->feed_id
					);
					$cpt++;
				} # fin du $item->get_content()
				if ($print) $output .= $log_msg;
			} # fin du foreach

			# Le flux a ete mis a jour, on le marque a la derniere date
			$cur = $core->con->openCursor($core->prefix.'feed');
			$cur->feed_checked = array('NOW()');
			$cur->update("WHERE feed_id = '$rs->feed_id'");
			$log_msg = logMsg("Le flux ".$rs->feed_url." est mis a jour", "", 2, $print);
			if ($print) $output .= $log_msg;

			# On fait un reset du foreach
			reset($items);

		} # fin $feed->error()
		# Destruction de l'objet feed avant de passer a un autre
		$feed->__destruct();
		unset($feed);

		if ($blog_settings->get('auto_feed_disabling')) {
			$toolong = time() - 86400*7; # seven days ago
			$check_sql = "SELECT feed_checked FROM ".$core->prefix."feed WHERE feed_id=".$rs->feed_id;
			$rs_check = $core->con->select($check_sql);
			$last_checked = mysqldatetime_to_timestamp($rs_check->f('feed_checked'));
			if ($last_checked < $toolong) {
				$diff = ($toolong - $last_checked)/60;
				$log_msg = logMsg("Le flux n'a plus ete mis a jour depuis $diff minutes. Il sera donc desactive : ".$rs->feed_url, "", 2, $print);
				if ($print) $output .= $log_msg;

				# if feed was in error for too long, let's disable it
				$cur = $core->con->openCursor($core->prefix.'feed');
				$cur->feed_status = 2;
				$cur->update("WHERE feed_id = '$rs->feed_id'");
			}
		}
	} # fin du while

	# Duree de la mise a jour
	$fin = explode(" ",microtime());
	$fin = $fin[1]+$fin[0];
	$temps_passe = round($fin-$debut,2);
	$log_msg = logMsg("$cpt articles mis a jour en $temps_passe secondes", "", 2, $print);
	if ($print) $output .= $log_msg;

	return $output;
}

function insertPostToDatabase ($rs, $item_permalink, $date, $item_title, $item_content, $item_tags, $item_image, $print, $feed_id) {
	global $log, $core;
	# Date
	if (!$date) {
		$item_date = date('Y-m-d H:i:s',time());
	} else {
		$item_date = date('Y-m-d H:i:s',$date);
	}

	# Check if item is already in the database
	$sql = "SELECT
			post_id,
			user_id,
			post_title,
			post_content,
			post_pubdate,
			post_image
		FROM ".$core->prefix."post
		WHERE post_permalink = '".$core->con->escape($item_permalink)."'";
	$rs2 = $core->con->select($sql);

	# There is no such permalink, we can insert the new item
	if($rs2->count() == 0 && $date < time()) {

		# Check if item is already in the database by title and by user
		$sql = "SELECT
				user_id,
				post_title,
				post_content,
				post_pubdate
			FROM ".$core->prefix."post
			WHERE user_id = '".$rs->user_id."'
				AND post_title = '".$item_title."'
				AND post_pubdate = '".$item_date."'";
		$rs4 = $core->con->select($sql);

		if ($rs4->count() == 0) {
			# Get ID
			$rs3 = $core->con->select(
				'SELECT MAX(post_id) '.
				'FROM '.$core->prefix.'post '
				);
			$next_post_id = (integer) $rs3->f(0) + 1;

			$image_url = savePostImage($next_post_id, $item_image);

			$cur = $core->con->openCursor($core->prefix.'post');
			$cur->post_id = $next_post_id;
			$cur->user_id = $rs->user_id;
			$cur->post_pubdate = $item_date;
			$cur->post_permalink = $core->con->escape($item_permalink);
			$cur->post_title = $item_title;
			$cur->post_content = $item_content;
			$cur->post_image = $image_url;
			$cur->post_status = $rs->feed_trust == 1 ? 1 : 2;
			$cur->post_comment = $rs->feed_comment;
			$cur->created = array(' NOW() ');
			$cur->modified = array(' NOW() ');
			$cur->insert();

			foreach ($item_tags as $tag) {
				$cur2 = $core->con->openCursor($core->prefix.'post_tag');
				$cur2->post_id = $next_post_id;
				$cur2->user_id = $rs->user_id;
				$cur2->tag_id = $tag;
				$cur2->insert();
			}

			postNewsOnSocialNetwork($item_title, $rs->user_fullname, $next_post_id);
			checkSharedLinkCount($next_post_id);

			return logMsg("Post added: ".$item_permalink, "", 1, $print);
		}
		elseif ($rs4->count() == 1) {
			# Update post permalink in database
			$cur = $core->con->openCursor($core->prefix.'post');
			$cur->post_permalink = $core->con->escape($item_permalink);
			$cur->modified = array('NOW()');
			$cur->update("WHERE ".$core->prefix."post.user_id = '".$rs->user_id."'
				AND ".$core->prefix."post.post_title = '".$item_title."'");
			# On informe que tout est ok
			return logMsg("Permalink updated : ".$item_permalink, "", 1, $print);
		}
		else {
			return logMsg("Several posts from the same author have the same title but not the same permalink : ".$item_permalink." (Do not know it we need to update or to add the idem)", "", 3, $print);
		}
	} # fin if(!found)

	# If post is already in database, check if update needed
	elseif($rs2->count() == 1) {
		$title2 = $rs2->f('post_title');
		$content2 = $rs2->f('post_content');
		$post_id = $rs2->f('post_id');
		$user_id = $rs2->f('user_id');

		# Update tags if needed
		$old_tags = array();
		$tagRq = $core->con->select('SELECT tag_id, user_id FROM '.$core->prefix.'post_tag WHERE post_id = '.$post_id);

		$tags_to_append = $item_tags; # par defaut TOUT
		$tags_to_remove = array(); # par defaut RIEN
		while ($tagRq->fetch()) {
			# Si le tag existe deja, ne pas l'ajouter
			$rm_i = -1;
			foreach ($tags_to_append as $key=>$value) {
				if ($value == $tagRq->tag_id) {
					$rm_i = $key;
				}
			}
			if ($rm_i >= 0) {
				unset($tags_to_append[$rm_i]);
			}
/*			if (in_array($tagRq->tag_id, $item_tags)) {
				$key = array_search($tagRq, $item_tags);
				unset($tags_to_append[$key]);
			}**/
			# Si le tag n'exitse plus, le supprimer
			if (!in_array($tagRq->tag_id, $item_tags) && $tagRq->user_id == 'root') {
				$tags_to_remove[] = $tagRq->tag_id;
			}
			$old_tags[] = $tagRq->tag_id;
		}
		//$tags_to_remove = array_diff($old_tags, $item_tags);
		//$tags_to_append = array_diff($item_tags, $old_tags);

		if(count($tags_to_remove) > 0) {
			foreach ($tags_to_remove as $tag) {
				$core->con->execute("DELETE FROM ".$core->prefix."post_tag
					WHERE tag_id ='".$core->con->escape($tag)."' AND post_id = ".$post_id);
			}
		}
		if(count($tags_to_append) > 0) {
			foreach ($tags_to_append as $tag) {
//				$tag = $core->con->escape($tag);
				$cur = $core->con->openCursor($core->prefix.'post_tag');
				$cur->tag_id = $tag;
				$cur->post_id = $post_id;
				$cur->user_id = 'root';
				$cur->created = array(' NOW() ');
				try {
					$cur->insert();
				} catch (Exception $e){
					print "<br>New tags :";
					print_r($item_tags);
					print "<br>Old tags :";
					print_r($old_tags);
					print "<br>to remove :";
					print_r($tags_to_remove);
					print "<br>to append:";
					print_r($tags_to_append);
					print "<br>post_id:".$post_id."<p>";
					print $e;
					exit;
				}
			}
		}

		# Si l'article a ete modifie (soit la date, soit le titre, soit le contenu)
		if($item_date != $rs2->f('post_pubdate') && !empty($date)) {

			# Update post in database
			$cur = $core->con->openCursor($core->prefix.'post');
			$cur->post_pubdate = $item_date;
			$cur->modified = array('NOW()');
			$cur->update("WHERE ".$core->prefix."post.post_permalink = '".$core->con->escape($item_permalink)."'");
			# On informe que tout est ok
			return logMsg("Date updated: ".$item_permalink, "", 1, $print);
		}
		if((!empty($item_title) && strcmp($item_title, $title2) != 0)
			|| (!empty($item_content) && strcmp($item_content, $content2) != 0)) {
			# Update post in database
			if(strcmp($item_title, $title2) != 0) {
				$cur = $core->con->openCursor($core->prefix.'post');
				$cur->modified = array('NOW()');
				$cur->post_title = $item_title;
				$cur->update("WHERE ".$core->prefix."post.post_permalink = '".$core->con->escape($item_permalink)."'");
				$log_msg = logMsg("Changement de titre pour l'article: ".$item_permalink, "", 2, $print);
				if ($log == "debug") {
					$log_msg .= logMsg("Old : ".$title2, "", 4, $print);
					$log_msg .= logMsg("New : ".$item_title, "", 4, $print);
				}
			}
			if(strcmp($item_content, $content2) != 0) {
				$image_url = savePostImage($rs2->f('post_id'), $item_image);
				$cur = $core->con->openCursor($core->prefix.'post');
				$cur->modified = array('NOW()');
				$cur->post_content = $item_content;
				$cur->post_image = $image_url;
				$cur->update("WHERE ".$core->prefix."post.post_permalink = '".$core->con->escape($item_permalink)."'");
				$log_msg = logMsg("Changement du contenu pour l'article: ".$item_permalink, "", 2, $print);
				if ($log == "debug") {
					$log_msg .= logMsg("Old : ".$content2, "", 4, $print);
					$log_msg .= logMsg("New : ".$item_content, "", 4, $print);
				}
			}
			return $log_msg;
		} # fin du if($date !=
	}
	return "";
}


function postNewsOnSocialNetwork($title, $author, $post_id) {
	global $blog_settings;
	$post_url = $blog_settings->get('planet_url').'/?post_id='.$post_id;
	$formating = $blog_settings->get('statusnet_post_format');
	$textlimit = $blog_settings->get('statusnet_textlimit');

//	$title_length = $textlimit - strlen($post_url) - strlen($formating);
//	$short_title = substr($title,0,$title_length)."...";

	$content = sprintfn($formating, array(
		"title" => $title,
		"author" => $author));
	$content_max_length = $textlimit - strlen($post_url) - 4;
	$short_message = substr($content,0,$content_max_length)."...";
	$status = $short_message.' '.$post_url;

	if ($blog_settings->get('statusnet_auto_post')) {
		postToStatusNet(
			$blog_settings->get('statusnet_host'),
			$blog_settings->get('statusnet_username'),
			$blog_settings->get('statusnet_password'),
			$status);
	}
}

# Procedure qui log un message a l'ecran et dans un fichier de log
# types:
# type = 0 : ''
# type = 1 : SUCCESS
# type = 2 : INFO
# type = 3 : ERROR
# type = 4 : DEBUG
function logMsg($message, $filename="", $type=0, $print=false) {
	# On recupere la date
	$print_style = '';
	$date_log = '['.date("Y-m-d").' '.date("H:i:s").'] ';
	switch($type){
		case 1:
			$message_type='SUCCESS : ';
			$print_style = "[<font color=\"green\">SUCCESS</font>] ";
			break;
		case 2:
			$message_type='INFO    : ';
			$print_style = "[<font color=\"blue\">INFO</font>] ";
			break;
		case 3:
			$message_type='ERROR   : ';
			$print_style = "[<font color=\"red\">ERROR</font>] ";
			break;
		case 4:
			$message_type='DEBUG   : ';
			$print_style = "[<font color=\"pink\">DEBUG</font>] ";
			break;
		default:
			$message_type='INFO    : ';
			$print_style = "[<font color=\"blue\">INFO</font>] ";
			break;
	}

	if ($filename == "") {
		$filename = dirname(__FILE__).'/../logs/update-'.date("Y-m-d").'.log';
	}

	// Assurons nous que le fichier est accessible en écriture
	if (is_string($filename)) {

		// Dans notre exemple, nous ouvrons le fichier $filename en mode d'ajout
		// Le pointeur de fichier est placé à la fin du fichier
		// c'est là que $somecontent sera placé
		if (!$handle = fopen($filename, 'a')) {
			echo "Impossible d'ouvrir le fichier ($filename)";
			exit;
		}

		// Ecrivons quelque chose dans notre fichier.
		if (fwrite($handle, $date_log.$message_type.$message."\n") === FALSE) {
			echo "Impossible d'ecrire dans le fichier ($filename)";
			exit;
		}
		fclose($handle);
	}
	else {
		echo "Le fichier $filename n'est pas accessible en écriture.";
	}


	/*
	if (!empty($file)) {
		fwrite($file, $date_log.$message_type.$message."\n");
		fclose($file);
	}*/
	# On log a l'ecran
	if ($print)
		return $print_style.$message."<br/>";
	else
		return $date_log.$message_type.$message."<br/>";
}


# Fonction qui effefctue un post traitement d'un article afin de l'enregistrer
# en base de donnees correctement
function traitementEncodage($chaine) {

	# On detecte l'encodage de la chaine
	$encodage = mb_detect_encoding($chaine);
	if($encodage == "ASCII") $encodage = "iso-8859-1"; # htmlentities ne connais pas l'ascii

	# On convertie tous les caracteres speciaux en code html
	$chaine = htmlentities($chaine,ENT_QUOTES,$encodage);
	$chaine = addslashes($chaine);

	# Fix le bug des articles relatif
	if(strpos($chaine, "!-- Generated by Simple Tags") > 0) {
		$chaine = substr($chaine, 0, strpos($chaine, "!-- Generated by Simple Tags")-8);
	}

	# On retourne le resultat
	return $chaine;
}

#-------------------------------------#
#   Fonctions pour les mises a jour   #
#-------------------------------------#
function getFirstPostImageUrl($post_content) {
	$contenttograbimagefrom = $post_content;
	$firstImage = "";
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $contenttograbimagefrom, $ContentImages);
	$firstImage = $ContentImages[1] [0]; // To grab the first image
	return $firstImage;
}
function savePostImage($post_id,$file_url) {
	global $blog_settings;
	//echo "<br/>saving image ..." . $file_url. " ----";
	if ($file_url != '') {
		$post_icon_folder = 'data/images';
		$folder = dirname(__FILE__).'/../'.$post_icon_folder;

		// check destination folder
		if (!is_dir($folder)) {
			print T_('The folder data/images does not exists !');
			return '';
		}
		if (!is_writable($folder)) {
			print T_('The folder data/images must writable !');
			return '';
		}
		$tmp_folder = dirname(__FILE__).'/../data/tmp';
		// check destination folder
		if (!is_dir($tmp_folder)) {
			print T_('The folder data/tmp does not exists !');
			return '';
		}
		if (!is_writable($tmp_folder)) {
			print T_('The folder data/tmp must writable !');
			return '';
		}

		$file_extension = null;
		if (endsWith(strtolower($file_url), '.png')) {
			$file_extension = '.png';
		} elseif (endsWith(strtolower($file_url), '.gif')) {
			$file_extension = '.gif';
		} elseif (endsWith(strtolower($file_url), '.jpg') || endsWith(strtolower($file_url), '.jpeg')) {
			$file_extension = '.jpg';
		} else {
			print T_('Unknown file extension');
			return '';
		}

		#copy to post_id.png
		$tmp_file = $tmp_folder.'/post'.$post_id.'-'.time().'-tmp'.$file_extension;
		if (!is_file($tmp_file)) {
			unlink($tmp_file);
		}
		file_put_contents($tmp_file, file_get_contents($file_url));
		if (!is_file($tmp_file)) {
			print T_('File not found');
			return '';
		}

		#resize image
		$imgsize = getimagesize($tmp_file);
		// check the image size
		$allowed_ratio = 0.40;
		if ($imgsize[0]/$imgsize[1] < $allowed_ratio
			|| $imgsize[1]/$imgsize[0] < $allowed_ratio) {
			unlink($tmp_file);
			print T_('Bad image ratio');
			return '';
		}

		$image = null;
		switch($file_extension) {
		case '.jpg': $image = imagecreatefromjpeg($tmp_file); break;
		case '.jpeg': $image = imagecreatefromjpeg($tmp_file); break;
		case '.png': $image = imagecreatefrompng($tmp_file); break;
		case '.gif': $image = imagecreatefromgif($tmp_file); break;
		}
		if ($image == null) {
			unlink($tmp_file);
			print T_('Unable to create image object');
			return '';
		}

		$width = 250; // defined width
		$height = ( ($imgsize[1] * (($width)/$imgsize[0]))); // relative height
		$final_image = imagecreatetruecolor($width , $height)
			or $error[] = T_('Error when creating final image');
		imagecopyresampled($final_image ,$image , 0,0, 0,0, $width, $height, $imgsize[0],$imgsize[1])
			or $error[] = T_('Error while resizing final image');
		imagedestroy($image)
			or $error[] = T_('Error while deleting temporary image');

		$filename = 'post'.$post_id.'-'.time().$file_extension;
		$file_url = $blog_settings->get('planet_url').'/data/images/'.$filename;
		$file_fullpath = $folder.'/'.strtolower($filename);

		if (is_file($file_fullpath)) {
			unlink($file_fullpath);
		}

		// save image to folder
		if ($file_extension == '.jpg') {
			$save = imagejpeg($final_image , $file_fullpath, 100);
		}
		if ($file_extension == '.png') {
			$save = imagepng($final_image , $file_fullpath, 0);
		}
		if ($file_extension == '.gif') {
			$save = imagegif($final_image, $file_fullpath);
		}

		unlink($tmp_file);
		if ($save) {
			#return image URL
			return $file_url;
		} else {
			unlink($file_fullpath);
			print T_('Problem during saving process');
			return '';
		}
	}
}

# Fonction qui met a jour la date a laquelle le planet a subit un update
function updateDateMaj() {

	# Nom du fichier
	$fichier = dirname(__FILE__).'/update.txt';

	# On recupere la date au format timestamp
	$date = time();
	$date_log = '['.date("Y-m-d").' '.date("H:i:s").'] ';

	# Ouverture du fichier en ecriture/creation
	$fp = @fopen($fichier,'wb');
	if ($fp === false) {
		throw new Exception(sprintf(__('Cannot write %s file.'),$fichier));
	}
	fwrite($fp,$date."\nLast update time : ".$date_log);
	fclose($fp);
}

# Fonction qui recuepere la date de mise a jour du planet
function getDateMaj() {

	# Nom du fichier
	$fichier = dirname(__FILE__).'/update.txt';

	# On test si le fichier est present
	if (file_exists($fichier)) {

		# Ouverture du fichier en lecture
		$file = fopen($fichier, "r");

		# Ecriture du timestamp
		$date = trim(fgets($file, 255));

		# Fermeture du fichier
		fclose($file);

	} else {

		# On informe que le fichier est introuvable
		echo "Error: file not found";
		echo "Creation d'un nouveau fichier";
		# On recupere la date au format timestamp
		$date = time();
		# Ouverture du fichier en ecriture/creation
		$fp = @fopen($fichier,'wb');
		if ($fp === false) {
			throw new Exception(sprintf(__('Cannot write %s file.'),$fichier));
		}
		fwrite($fp,$date);
		fclose($fp);
	}

	# On retourne la date de maj au bon format
	return date("d-m-Y", $date).'&nbsp;&agrave;&nbsp;'.date("H:i", $date);
}

# Fonction qui transforme des urls d'image relative en absolue d'un item
function completeUrl($item, $site) {

	# Tansfromation
	$modif = ereg_replace("^([^/]*)(//)?([^/]*)/.*$","\\1\\2\\3",$site."/");
	$item = ereg_replace("<img src=\"/","<img src=\"$modif/",$item);

	# On retourne le resultat
	return $item;
}

# Fonction qui teste si une url est accessible
function checkUrl($url) {
	# Activation de l'option au niveau de la configuration de php
	ini_set('allow_url_fopen', '1');

	# Ouverture / Fermeture de l'url a distance
	return  @fclose(@fopen($url, 'r'));
}


?>
