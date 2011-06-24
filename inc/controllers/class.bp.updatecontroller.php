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

class UpdateController extends AbstractController
{
	public function __construct(&$core) {
		$this->core =& $core;
		$this->con = $core->con;
		$this->prefix = $core->prefix;
	}

	public function run() {
		$log_file = dirname(__FILE__).'/../../logs/cron_job.log';
		$cache_dir = dirname(__FILE__).'/../../admin/cache/';

		logMsg("Actual time : ".time(), $log_file);
		if (file_exists(dirname(__FILE__).'/../STOP')) {
			logMsg("STOP file detected, cannot proceed to update", $log_file);
			exit;
		}

		logMsg("Start update script", $log_file);
		echo $this->update(true);

		$cache_dir = dirname(__FILE__).'/../../admin/cache';
		$dir_handle = @opendir($cache_dir) or die("Unable to open $cache_dir");
		while ($file = readdir($dir_handle)){
			if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
				unlink($cache_dir.'/'.$file);
			}
		}
		closedir($dir_handle);
		logMsg("Cleaning simplepie cache dir", $log_file);
		logMsg("Script ended\nMemory Usage : ".number_format(memory_get_usage())."\n", $log_file);
	}

	private function update($print=false) {
		global $blog_settings;
		$output = "";

		# Inclusion des fichiers necessaires
		require_once(dirname(__FILE__).'/../lib/simplepie/SimplePieAutoloader.php');

		# Requete permettant de recuperer la liste des flux a parser
		$sql = "SELECT feed_id
			FROM ".$this->prefix."feed, ".$this->prefix."site, ".$this->prefix."user
			WHERE
				".$this->prefix."feed.site_id = ".$this->prefix."site.site_id
				AND ".$this->prefix."feed.user_id = ".$this->prefix."user.user_id
				AND user_status = 1
				AND site_status = 1
				AND feed_status = 1
			ORDER BY feed_checked ASC
			LIMIT 50";
		$rs = $this->con->select($sql);

		# Affichage des logs dans la partie admin
		$output .= "<fieldset><legend>Log File</legend>
			<div class='message'><p>Manual Update Log</p></div>";


		# Duree de mise a jour
		$debut = explode(" ",microtime());
		$debut = $debut[1]+$debut[0];

		$cpt = 0;
		while ($rs->fetch()) {
			$feed = new bpFeed($this->con, $this->prefix, $rs->feed_id);
			$output .= $feed->fetch();
			$cpt += 1;
		}

		# Duree de la mise a jour
		$fin = explode(" ",microtime());
		$fin = $fin[1]+$fin[0];
		$temps_passe = round($fin-$debut,2);
		$log_msg = logMsg("$cpt articles mis a jour en $temps_passe secondes", "", 2, $print);
		if ($print) $output .= $log_msg;

		$output .= "</fieldset>";

		# On detruit les fichiers de cache des pages web pour les actualiser
		$cache_dir = dirname(__FILE__).'/../../admin/cache';
		$dir_handle = @opendir($cache_dir) or die("Unable to open $cache_dir");
		while ($file = readdir($dir_handle)){
			if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
				unlink($cache_dir.'/'.$file);
			}
		}
		closedir($dir_handle);

		# On met a jour la date d'update
		$this->updateDateMaj();

		return $output;
	}


	# Fonction qui met a jour la date a laquelle le planet a subit un update
	private function updateDateMaj() {

		# Nom du fichier
		$fichier = dirname(__FILE__).'/../update.txt';

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
	private function getDateMaj() {
		$fichier = dirname(__FILE__).'/../update.txt';
		if (file_exists($fichier)) {
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
}
?>

