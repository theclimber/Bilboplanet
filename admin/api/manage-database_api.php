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
if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# EXPORT DATABASE
##########################################################
	case 'export':
		$JSON_array = array();
		if (isset($_POST['list'])) {
			$tables = $_POST['list'];
			foreach ($tables as $table) {
				# Creation de la ligne de titres
				$schema = dbSchema::init($core->con);
				$header = array_keys($schema->getColumns($core->prefix.$table));
				$JSON_array[$table]['head'] = $header;
				$JSON_array[$table]['name'] = $table;

				# Creation du contenu
				$rs = $core->con->select("SELECT ".implode(",", $header)." FROM ".$core->prefix.$table."");
				while ($rs->fetch()) {
					$line = array();
					foreach ($header as $h) {
						$line[] = $rs->f($h);
					}
					$JSON_array[$table]['content'][$header[0]] = $line;
				}
			}

			ini_set("memory_limit",'256M');
			$content = json_encode($JSON_array);
			$compressed = gzencode($content, 9);

			# On recupere la date du jour, l'heure et la minute
			$date = date("Y-m-d_H-i",time());
			$snapshot_path = dirname(__FILE__)."/../cache";
			$filename = 'planet-export.'.$date.'.json.gz';
			$snapshot_file = $snapshot_path.'/'.$filename;
			@unlink($snapshot_file);
			$fp = @fopen($snapshot_file,'wb');
			if ($fp === false) {
				$output = '<div class="flash error">'.sprintf(T_('Unable to write %s file.'),$snapshot_file).'<br />'.T_('Please check you /admin/cache directory permissions').'</div>';
			}
			else {
				fwrite($fp,$compressed);
				fclose($fp);
				chmod($snapshot_file, 0777);

				$output = '<div class="flash notice">'.T_("A snapshot of your current database was successfully created. Please download the following file and put it in some safe place.").'</div>';
				$output .= '<p class="file"><a href="cache/'.$filename.'" target="_blank" type="octet-stream">'.$filename.'</a></p>';
			}
		}
		else {
			$output = '<div class="flash error">'.T_("Please select at least one table to export").'</div>';
		}
		print $output;
		break;

##########################################################
# DEFAULT RETURN
##########################################################
	default:
		print '<div class="flash error">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}
?>
