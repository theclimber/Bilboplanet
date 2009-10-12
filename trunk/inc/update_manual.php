<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.org
* Website : www.bilboplanet.org
* Tracker : redmine.bilboplanet.org
* Blog : blog.bilboplanet.org
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
require_once(dirname(__FILE__).'/cron_fct.php');

$log_file = fopen(dirname(__FILE__).'/../logs/cron_job.log', 'a');

$cache_dir = dirname(__FILE__).'/../admin/cache/';

logMsg("Actual time : ".time(), $log_file);
if (file_exists(dirname(__FILE__).'/STOP')) {
	logMsg("STOP file detected, cannot proceed to update", $log_file);
	exit;
}

logMsg("Start update script", $log_file);
update();

$result = exec('cd '.$cache_dir.' && rm -vf *');
logMsg("Cleaning simplepie cache dir", $log_file);
logMsg("Script ended\nMemory Usage : ".number_format(memory_get_usage())."\n", $log_file);
?>
