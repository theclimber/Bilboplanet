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
require_once(dirname(__FILE__).'/prepend.php');
require_once(dirname(__FILE__).'/cron_fct.php');

$log_file = dirname(__FILE__).'/../logs/cron_job.log';

$cache_dir = dirname(__FILE__).'/../admin/cache/';
#ini_set('max_execution_time', 0);
#proc_nice(5);

logMsg("Actual time : ".time(), $log_file);
if (file_exists(dirname(__FILE__).'/STOP')) {
	logMsg("STOP file detected, cannot proceed to update", $log_file);
	exit;
}

logMsg("Start update script", $log_file);
echo update($core, true);

$cache_dir = dirname(__FILE__).'/../admin/cache';
$dir_handle = @opendir($cache_dir) or die("Unable to open $cache_dir");
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
		unlink($cache_dir.'/'.$file);
	}
}
closedir($dir_handle);
logMsg("Cleaning simplepie cache dir", $log_file);
logMsg("Script ended\nMemory Usage : ".number_format(memory_get_usage())."\n", $log_file);
?>
