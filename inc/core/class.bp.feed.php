<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
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
?><?php
class bpFeed extends bpObject
{
	private $con;		///< <b>connection</b> Database connection object
	private $table;
	private $prefix;
	private $feed_id;
	private $site;
	private $tags = array();
	private $feed_url;
	private $feed_name;
	private $feed_trust;
	private $feed_checked;
	private $feed_status;
	private $feed_comment;

	public function __construct(&$con, $prefix, $feed_id)
	{
		$this->con =& $con;
		$this->table = $prefix.'feed';
		$this->prefix = $prefix;
		$this->feed_id = $feed_id;
		$this->getFeed();
	}

	private function getFeed()
	{
		$select = "feed_url,
			site_id,
			feed_trust,
			feed_comment,
			feed_checked,
			feed_name,
			feed_status";
		$tables = $this->table;
		$where_clause = "feed_id = ".$this->feed_id;

		$strReq = "SELECT ".$select."
			FROM ".$this->table."
			WHERE ".$where_clause;

		try {
			$rs = $this->con->select($strReq);
		} catch (Exception $e) {
			throw new Exception(T_('Unable to retrieve feed:').' '.$this->con->error(), E_USER_ERROR);
		}
		if ($rs->count() == 0) {
			throw new Exception(T_('This feed does not exist'));
		}

		$this->feed_url			= $rs->f('feed_url');
		$this->feed_name		= $rs->f('feed_name');
		$this->feed_trust		= $rs->f('feed_trust') ? true : false;
		$this->feed_comment		= $rs->f('feed_comment') ? true : false;
		$this->feed_status		= $rs->f('feed_status') ? true : false;
		$this->feed_checked		= $rs->f('feed_checked');
		$this->site				= new bpSite($this->con, $this->prefix, $rs->f('site_id'));

		$sql_tags = "SELECT * FROM ".$this->prefix."feed_tag WHERE feed_id = ".$this->feed_id;
		$rs2 = $this->con->select($sql_tags);
		while ($rs2->fetch()) {
			$this->tags[] = $rs2->tag_id;
		}
		return true;
	}

	private function getLastChecked($format) {
		return  $this->getDateFormat($format,$this->feed_checked);
	}

	private function isActive() {
		return $this->feed_status;
	}

	private function canComment() {
		return $this->feed_comment;
	}

	private function canTrust() {
		return $this->feed_trust;
	}

	private function updateLastChecked() {
		# Update the number of viewed times
		$cur = $this->con->openCursor($this->table);
		$cur->feed_checked = array('NOW()');
		$cur->update("WHERE feed_id = '".$this->feed_id."'");
	}

	private function getUrl() {
		return $this->feed_url;
	}

	private function getName() {
		return $this->feed_name;
	}

	private function getSite() {
		return $this->site;
	}

	private function getId() {
		return $this->feed_id;
	}

	private function getTags() {
		return $this->tags;
	}

	public function fetch() {
		global $blog_settings;
		$print = true;
		$output = "";
		$cron_file = dirname(__FILE__).'/../cron_running.txt';
		# On verifie si on n'a pas demandé l'arrêt de l'algo
		if (file_exists(dirname(__FILE__).'/../STOP')) {
			$log_msg = logMsg("STOP file detected, trying to shut down cron job", "", 2, $print);
			if ($print) $output .= $log_msg;
			break;
		}

		# On cree un objet SimplePie et on ajuste les parametres de base
		require_once(dirname(__FILE__).'/../lib/simplepie/SimplePieAutoloader.php');
		$feed = new SimplePie();
		$feed->set_feed_url($this->getUrl());
		$feed->set_cache_location(dirname(__FILE__).'/../../admin/cache');
		$feed->init();

		# Pour faire fonctionner les lecteurs flash, non recomande par simplepie
		$feed->strip_htmltags(false);

		# Si le flux ne contient pas  de donnee
		$item_nb = $feed->get_item_quantity();
		if ($feed->get_item_quantity() == 0) {
			# Affichage du message d'erreur
			$log_msg = logMsg("Aucun article trouve sur ".$this->getUrl()." : ".$feed->error(), "", 3, $print);
			if ($print) $output .= $log_msg;
		} else {
			$items = $feed->get_items();
			foreach ($items as $item) {
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
				$item_content = $this->strip_script($item->get_content());
				if (empty($item_content)) {
					$item_content = $item->get_description();
				}
				$item_content = $this->traitementEncodage($item_content);
				# Title
				$item_title = $this->traitementEncodage($item->get_title());
				if(strlen($item_title) > 254) {
					$item_title = substr($item_title, 0, 254);
				}
				# Permalink
				$item_permalink = $item->get_permalink();

				# Analyse the possible tags of the item
				##########################################
				# Get tags defined in the database for all posts of that feed
				$item_tags = $this->tags;

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
							&& !in_array($label, $reserved_tags)){
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
						&& !in_array($tag, $reserved_tags)){
						$item_tags[] = $tag;
					}
				}

				# check if some existing tags are in the title
				foreach (explode(' ', $item_title) as $word) {
					$word = strtolower($word);
					$tagRq = $this->con->select('SELECT tag_id FROM '.$this->prefix.'post_tag WHERE tag_id = "'.$word.'"');
					if ($tagRq->count() > 1
						&& !in_array($word, $item_tags)
						&& !in_array($word, $reserved_tags)) {
						$item_tags[] = $word;
					}
				}

				if (empty($item_content)) {
				$log_msg = logMsg("Pas de contenu sur $this->getUrl()", "", 3, $print);
				} elseif(empty($item_permalink)) {
					$log_msg = logMsg("Erreur de decoupage du lien ".$item_permalink, "", 3, $print);
				} else {
					$log_msg = $this->insertPostToDatabase(
						$item_permalink,
						$item->get_date("U"),
						$item_title,
						$item_content,
						$item_tags,
						$print
					);
				} # fin du $item->get_content()
				if ($print) $output .= $log_msg;
			} # fin du foreach

			# Le flux a ete mis a jour, on le marque a la derniere date
			$cur = $this->con->openCursor($this->table);
			$cur->feed_checked = array('NOW()');
			$cur->update("WHERE feed_id = '$this->feed_id'");

			# On fait un reset du foreach
			reset($items);

		} # fin $feed->error()
		# Destruction de l'objet feed avant de passer a un autre
		$feed->__destruct();
		unset($feed);

		if ($blog_settings->get('auto_feed_disabling')) {
			$toolong = time() - 86400*5; # five days ago
			if (mysqldatetime_to_timestamp($this->getLastChecked()) < $toolong) {
				# if feed was in error for too long, let's disable it
				$cur = $this->con->openCursor($this->table);
				$cur->feed_status = 2;
				$cur->update("WHERE feed_id = '$this->getId()'");
			}
		}

	}

	private function insertPostToDatabase ($item_permalink, $date, $item_title, $item_content, $item_tags, $print) {
		global $log;
		# Date
		if (!$date) {
			$item_date = date('Y-m-d H:i:s',time());
		} else {
			$item_date = date('Y-m-d H:i:s',$date);
		}

		# Check if item is already in the database
		$sql = "SELECT
				user_id,
				post_id,
				post_title,
				post_content,
				post_pubdate
			FROM ".$this->prefix."post
			WHERE `post_permalink` = '".addslashes($item_permalink)."'";
		$rs2 = $this->con->select($sql);

		# There is no such permalink, we can insert the new item
		if($rs2->count() == 0 && $date < time()) {

			# Check if item is already in the database by title , by pubdate and by user
			$sql = "SELECT post_id
				FROM ".$this->prefix."post
				WHERE user_id = '".$this->getSite()->getAuthor()->getId()."'
					AND post_title = '".$item_title."'
					AND post_pubdate = '".$item_date."'";
			$rs4 = $this->con->select($sql);

			if ($rs4->count() == 0) {
				# Get ID
				$rs3 = $this->con->select(
					'SELECT MAX(post_id) '.
					'FROM '.$this->prefix.'post '
					);
				$next_post_id = (integer) $rs3->f(0) + 1;

				$cur = $this->con->openCursor($this->prefix.'post');
				$cur->post_id = $next_post_id;
				$cur->user_id = $this->getSite()->getAuthor()->getId();
				$cur->post_pubdate = $item_date;
				$cur->post_permalink = addslashes($item_permalink);
				$cur->post_title = $item_title;
				$cur->post_content = $item_content;
				$cur->post_status = $this->canTrust() ? 1 : 2;
				$cur->post_comment = $this->canComment() ? 1 : 0;
				$cur->created = array(' NOW() ');
				$cur->modified = array(' NOW() ');
				$cur->insert();

				foreach ($item_tags as $tag) {
					$cur2 = $this->con->openCursor($this->prefix.'post_tag');
					$cur2->post_id = $next_post_id;
					$cur2->tag_id = $tag;
					$cur2->insert();
				}

				$this->postNewsOnSocialNetwork($item_title, $this->getSite()->getAuthor()->getFullname(), $next_post_id);

				return logMsg("Post added: ".$item_permalink, "", 1, $print);
			}
			elseif ($rs4->count() == 1) {
				# Update post permalink in database
				$cur = $this->con->openCursor($this->prefix.'post');
				$cur->post_permalink = addslashes($item_permalink);
				$cur->modified = array('NOW()');
				$cur->update("WHERE post_id = ".$rs4->f('post_id'));
				# On informe que tout est ok
				return logMsg("Permalink updated : ".$item_permalink, "", 1, $print);
			}
			else {
				return logMsg("Several posts from the same author have the same title but not the same permalink : ".$item_permalink." (Do not know it we need to update or to add the idem)", "", 3, $print);
			}
		} # fin if(!found)

		# If post is already in database, check if update needed
		elseif($rs2->count() == 1) {
			$post_id = $rs2->f('post_id');
			$title2 = $rs2->f('post_title');
			$content2 = $rs2->f('post_content');

			# Update tags if needed
			$old_tags = array();
			$tagRq = $this->con->select('SELECT tag_id FROM '.$this->prefix.'post_tag WHERE post_id = '.$post_id.'');
			while ($tagRq->fetch()) {
				$old_tags[] = $tagRq->tag_id;
			}
			$tags_to_remove = array_diff($old_tags, $item_tags);
			$tags_to_append = array_diff($item_tags, $old_tags);

			if(count($tags_to_remove) > 0) {
				foreach ($tags_to_remove as $tag) {
					$this->con->execute("DELETE FROM ".$this->prefix."post_tag
						WHERE tag_id ='$tag' AND post_id = ".$post_id);
				}
			}
			if(count($tags_to_append) > 0) {
				foreach ($tags_to_append as $tag) {
					$cur = $this->con->openCursor($this->prefix.'post_tag');
					$cur->tag_id = $tag;
					$cur->post_id = $post_id;
					$cur->user_id = $this->getSite()->getAuthor()->getId();
					$cur->created = array('NOW()');
					try {
						$cur->insert();
					} catch (Exception $e){
						print "<br>to remove :";
						print_r($tags_to_remove);
						print "<br>to append:";
						print_r($tags_to_append);
						print "<br>post_id:".$post_id."<p>";
						print $e;
					}
				}
			}

			# Si l'article a ete modifie (soit la date, soit le titre, soit le contenu)
			if($item_date != $rs2->f('post_pubdate') && !empty($date)) {
				# Update post in database
				$cur = $this->con->openCursor($this->prefix.'post');
				$cur->post_pubdate = $item_date;
				$cur->modified = array('NOW()');
				$cur->update("WHERE post_id=".$post_id);
				# On informe que tout est ok
				return logMsg("Date updated: ".$item_permalink, "", 1, $print);
			}
			if((!empty($item_title) && strcmp($item_title, $title2) != 0)
				|| (!empty($item_content) && strcmp($item_content, $content2) != 0)) {
				# Update post in database
				if(strcmp($item_title, $title2) != 0) {
					$cur = $this->con->openCursor($this->prefix.'post');
					$cur->modified = array('NOW()');
					$cur->post_title = $item_title;
					$cur->update("WHERE post_id = ".$post_id);
					$log_msg = logMsg("Changement de titre pour l'article: ".$item_permalink, "", 2, $print);
					if ($log == "debug") {
						$log_msg .= logMsg("Old : ".$title2, "", 4, $print);
						$log_msg .= logMsg("New : ".$item_title, "", 4, $print);
					}
				}
				if(strcmp($item_content, $content2) != 0) {
					$cur = $this->con->openCursor($this->prefix.'post');
					$cur->modified = array('NOW()');
					$cur->post_content = $item_content;
					$cur->update("WHERE post_id = ".$post_id);
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

	private function strip_script($string, $rm = 1) {
		if (!$rm) {
			$s = array("/<(\/script[^\>]*)>/", "/<(script[^\>]*)>/");
			$r = array("<$1></myScript>", "<myScript><$1>");
			//do
			$string = preg_replace($s, $r, $string);
			//while (preg_match($s, $string)!=0);
			return code_htmlentities($string, 'myScript', 'pre', 1);
		} else {

			$split1 = preg_split('(<script[^>]*>)', $string, -1);
			$result = array();

			# Pour chaque element on test si on trouve une fin de balise
			foreach ($split1 as $el) {
				$split2 = preg_split('(<\/script[^>]*>)', $el, -1);
				if (count($split2) == 2) {
					# si la longueur du tableau est de 2, c'est qu'il y avait une balise
					# l'element avec une valise est le premier des deux
					$content_text = '';
					//$result[] = '<'.$tag2.'>'.$content_text.'</'.$tag2.'>';
					$result[] = $split2[1];
				}
				else {
					# s'il n'y a pas de balise, alors on ajoute simplement l'element au tableau
					$result[] = $el;
				}
			}

			# Maintenant que le texte est transforme en tableau, on l'affiche element par element
			$output = "";
			foreach($result as $el) {
				$output .= $el;
			}
			return $output;
		}
	}

	# Fonction qui effefctue un post traitement d'un article afin de l'enregistrer
	# en base de donnees correctement
	private function traitementEncodage($chaine) {

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
}
?>
