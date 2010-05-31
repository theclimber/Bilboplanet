<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agr�gateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
* Blog : blog.bilboplanet.com
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
require_once(dirname(__FILE__).'/database.php');
require_once(dirname(__FILE__).'/lib/checkValidHTML.php');

function finished() {
	$log_file = fopen('../logs/cron_job.log', 'a');
	logMsg("The Cron is stopped and exited", $log_file);
}

function update($print=false) {
	$cron_file = dirname(__FILE__).'/cron_running.txt';
	$output = "";

	# Inclusion des fichiers necessaires
	require_once(dirname(__FILE__).'/lib/simplepie/simplepie.inc');

	# Duree de mise a jour
	$debut = explode(" ",microtime());
	$debut = $debut[1]+$debut[0]; 

	# Connexion a la base de donnees
	connectBD();

	# Requete permettant de recuperer la liste des flux a parser
	$sql = "SELECT flux.num_membre, flux.url_flux, site_membre,trust
		FROM flux, membre 
		WHERE membre.num_membre = flux.num_membre 
		AND statut_membre = '1'
		AND status_flux = '1'
		ORDER BY last_updated ASC
		LIMIT 30";
	$rqt_flux = mysql_query($sql) or die("Error with request $sql");

	# Ouverture du fichier de log
	$file = fopen('../logs/update-'.date("Y-m-d").'.log', 'a');

	# Affichage des logs dans la partie admin
	$output .= "<fieldset><legend>Log File</legend>
		<div class='message'><p>Manual Update Log</p></div>";

	# On parcour l'ensemble des flux 
	$cpt = 0;
	while ($liste = mysql_fetch_row($rqt_flux)) {
		# On verifie si on n'a pas demand� l'arr�t de l'algo
		if (file_exists(dirname(__FILE__).'/STOP')) {
			$log_msg = logMsg("STOP file detected, trying to shut down cron job", $file, 2, $print);
			if ($print) $output .= $log_msg;
			break;
		}

		$sql = "UPDATE `flux`
			SET `last_updated` = '".time()."'
			WHERE flux.url_flux = '".$liste[1]."'";
		$result = mysql_query($sql);
		
		$site_membre = $liste[2];
		# On construit l'url du flux
		$parse = @parse_url($liste[1]);
		if (!$parse['scheme']){
			$url_flux = $site_membre.$liste[1];
		}
		else {
			$url_flux = $liste[1];
		}

		# Si on est en mode debug
		if($log == "debug") {
			$log_msg = logMsg("Analyse du flux ".$url_flux, $file, 4, $print);
			if ($print) $output .= $log_msg;
		}

		# On cree un objet SimplePie et on ajuste les parametres de base
		$feed = new SimplePie();
		$feed->set_feed_url($url_flux);
		$feed->set_cache_location(dirname(__FILE__).'/../admin/cache');
		$feed->set_cache_duration($item_refresh);
		$feed->init();

		# Pour faire fonctionner les lecteurs flash, non recomande par simplepie
		$feed->strip_htmltags(false);

		# Si le flux ne contient pas  de donnee
		$item_nb = $feed->get_item_quantity();
		if ($feed->get_item_quantity() == 0) {

			# Affichage du message d'erreur
			$error = $feed->error();
			if (ereg($url_flux, $error)) {
				$log_msg = logMsg("Aucun article trouve ".$error, $file, 3, $print);
				if ($print) $output .= $log_msg;
			} else {
				$log_msg = logMsg("Aucun article trouve sur $url_flux: ".$error, $file, 3, $print);
				if ($print) $output .= $log_msg;
			}

		} else {

			# On traite chaque item du flux
			$items = $feed->get_items();
			$item_permalink = '';
			$content = '';

			foreach ($items as $item) {
				# On ecrit dans le fichier que l'algorithme est en marche
				$fp = @fopen($cron_file,'wb');
				if ($fp === false) {
					throw new Exception(sprintf(__('Cannot write %s file.'),$cron_file));
				}
				fwrite($fp,time());
				fclose($fp);

				# On lance l'analyse de l'entr�e
				$item_permalink = $item->get_permalink();
				$content = strip_script($item->get_content());
				$description = $item->get_description();

				if (empty($content) && empty($description)) {
					$log_msg = logMsg("Pas de contenu sur $url_flux", $file, 3, $print);
					if ($print) $output .= $log_msg;
				} else {
					# On test si le decoupage s'est bien passe
					if(empty($item_permalink)) {

						# Sinon on affiche la vrai cause de l'erreur
						$log_msg = logMsg("Erreur de decoupage du lien ".$item_permalink, $file, 3, $print);
						if ($print) $output .= $log_msg;

						# Si on est en mode debug
						if($log == "debug") {
							$log_msg = logMsg("Url du site: ".$site_membre, $file, 4, $print);
							if ($print) $output .= $log_msg;
							$log_msg = logMsg("Url du permalink: ".$item_permalink, $file, 4, $print);
							if ($print) $output .= $log_msg;
						}

					} else {

						# On test si l'item est deja en base
						$trouve = 0;
						$sql = "SELECT article_titre, article_content, article_pub FROM article WHERE `article_url` = '".addslashes($item_permalink)."'";
						$rqt = mysql_query($sql) or die("Error with request $sql");
						$nb = mysql_num_rows($rqt);

						# Si il n'y pas d'item avec cette url, on insere
						if($nb == 0 && $item->get_date('U') < time()) {

							# On recupere les donnes de l'article
							$date = $item->get_date('U');
							if (!$date){
								$date = time();
							}
							$titre = $item->get_title();
							if (!empty($content)) {
								$contenu = $content;
							} else {
								$contenu = $description;
							}

							# On raccourci le titre si il est trop long
							if(strlen($titre) > 254) $titre = substr($titre, 0, 254);

							# On effectue les traitements avant insertion en base
							$titre = traitementEncodage($titre);
							$contenu = traitementEncodage($contenu);
							$sql = "INSERT INTO article VALUES ('','$liste[0]','$date','$titre','".addslashes($item_permalink)."','$contenu','".($liste[3] == 1 ? 1 : 2)."', '0')";
							$result = mysql_query($sql);

							if (!$result) {
								# On test si on a pas perdu la connexion a cause d'un temps trop long a parser le flux
								if (mysql_error() == 'MySQL server has gone away') {
									# On se reconnect a la base
									closeBD();
									connectBD();
								} else {
									# Sinon on affiche la vrai cause de l'erreur
									$log_msg = logMsg("Erreur sur la requete: $sql", $file, 3, $print);
									if ($print) $output .= $log_msg;
								}
							} else {
								# Sinon, si l'insertion de l'article c'est bien passee
								$log_msg = logMsg("Article ajoute: ".$item_permalink, $file, 1, $print);
								if ($print) $output .= $log_msg;

								$cpt++;
							}
						} # fin if(!trouve)

						# Si l'article est deja dans la base, on test si on doit le mettre a jour
						elseif($nb == 1) {
							$result = mysql_fetch_row($rqt);
							# Mysql virer les slashe apres insertion de la requete
							# Pour garder la coherence des donnees on les arjoute ici
							$titre2 = addslashes($result[0]);
							$contenu2 = addslashes($result[1]);
							$date2 = $result[2];

							# On recupere les informations de l'article
							$date = $item->get_date('U');
							$titre = $item->get_title();
							if (!empty($content)) {
								$contenu = $content;
							} else {
								$contenu = $description;
							}

							# On effectue les traitements avant insertion en base
							$titre = traitementEncodage($titre);
							$contenu = traitementEncodage($contenu);

							# On raccourci le titre si il est trop long
							if(strlen($titre) > 254) $titre = substr($titre, 0, 254);

							# Si l'article a ete modifie (soit la date, soit le titre, soit le contenu)
							if($date != $date2 && $date != '') {

								# On log si il y a eu des modifications trouvees
								$log_msg = logMsg("changement de date pour l'article: ".$item_permalink, $file, 2, $print);
								if ($print) $output .= $log_msg;

								# On met a jour l'article en base
								$sql = "UPDATE article, membre 
									SET article_pub = '$date'
									WHERE article.num_membre = membre.num_membre
									AND membre.site_membre = '".$site_membre."'
									AND article_url = '".addslashes($item_permalink)."'";
								$result = mysql_query($sql);

								# Si la mise a jour de l'article c'est mal passe
								if (!$result) {
									if (mysql_error() == 'MySQL server has gone away') {
										# On se reconnect a la base
										closeBD();
										connectBD();
									} else {
										# Sinon on affiche la vrai cause de l'erreur
										$log_msg = logMsg("Erreur sur la requete: ".$sql, $file, 3, $print);
										if ($print) $output .= $log_msg;
									}
									# Sinon, si la mise a jour de l'article c'est bien passee
								} else {
									# On informe que tout est ok
									$log_msg = logMsg("Date mise a jour: ".$item_permalink, $file, 1, $print);
									if ($print) $output .= $log_msg;
									$cpt++;
								}
							}
							if(($titre != "" && strcmp($titre, $titre2) != 0) || ($contenu != "" && strcmp($contenu, $contenu2) != 0)) {
								if(strcmp($titre, $titre2) != 0) {
									$log_msg = logMsg("Changement de titre pour l'article: ".$item_permalink, $file, 2, $print);
									if ($print) $output .= $log_msg;
								}
								if(strcmp($contenu, $contenu2) != 0) {
									$log_msg = logMsg("Changement du contenu pour l'article: ".$item_permalink, $file, 2, $print);
									if ($print) $output .= $log_msg;
								}

								# On met a jour l'article en base
								$sql = "UPDATE article, membre 
									SET article_titre = '$titre', article_content = '$contenu' 
									WHERE article.num_membre = membre.num_membre
									AND membre.site_membre = '".$site_membre."'
									AND article_url = '".addslashes($item_permalink)."'";
								$result = mysql_query($sql);

								# Si la mise a jour de l'article c'est mal passe
								if (!$result) {
									if (mysql_error() == 'MySQL server has gone away') {
										closeBD();
										connectBD();
									} else {
										$log_msg = logMsg("Erreur sur la requete: ".$sql, $file, 3, $print);
										if ($print) $output .= $log_msg;
									}
									# Sinon, si la mise a jour de l'article c'est bien passee
								} else {
									$log_msg = logMsg("Titre et contenu mis a jour: ".$item_permalink, $file, 1, $print);
									if ($print) $output .= $log_msg;
									$cpt++;
								}
							} # fin du if($date !=
						} # fin du if($trouve)
					} # fin empty($url ...
				} # fin du $item->get_content()
			} # fin du foreach
			# On fait un reset du foreach
			reset($items);
		} # fin $feed->error()
		$feed->__destruct();
		# Destruction de l'objet feed avant de passer a un autre
		unset($feed);
	} # fin du while

	# Femeture de la base
	closeBD();

	# Duree de la mise a jour
	$fin = explode(" ",microtime());
	$fin = $fin[1]+$fin[0];
	$temps_passe = round($fin-$debut,2);

	# Message indiquant la fin de la mise a jour
	$log_msg = logMsg("$cpt articles mis a jour en $temps_passe secondes", $file, 2, $print);
	if ($print) $output .= $log_msg;

	# Fermeture du fichier de log
	fclose($file); 

	# On detruit les fichiers de cache des pages web pour les actualiser
	$cache_dir = dirname(__FILE__).'/../admin/cache';
	$dir_handle = @opendir($cache_dir) or die("Unable to open $cache_dir");
	while ($file = readdir($dir_handle)){
		if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
			unlink($cache_dir.'/'.$file);
		}
	}
	closedir($dir_handle);
	
//	exec('cd '.dirname(__FILE__).'/../admin/cache && rm -f *.cache');

	# On met a jour la date d'update
	updateDateMaj();
	
	$output .= "</fieldset>";
	return $output;
}

# Procedure qui log un message a l'ecran et dans un fichier de log
# types:
# type = 0 : ''
# type = 1 : SUCCESS
# type = 2 : INFO
# type = 3 : ERROR
# type = 4 : DEBUG
function logMsg($message, $fichier, $type=0, $print=false) {
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
			break;
	}
	# On log dans le fichier
	fwrite($fichier, $date_log.$message_type.$message."\n");
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

function strip_script($string) {
	do
	$string = eregi_replace("<script[^>]*>.*</script[^>]*>", "", $string);
	while (eregi_replace("<script[^>]*>.*</script[^>]*>", "", $string)==1);
	do
	$string = eregi_replace("<script[^>]*>", "", $string);
	while (eregi_replace("<script[^>]*>", "", $string)==1);
	return $string;
}

?>
