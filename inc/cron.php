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
?>
<?php
require_once(dirname(__FILE__).'/prepend.php');
require_once(dirname(__FILE__).'/cron_fct.php');

$log_file = fopen(dirname(__FILE__).'/../logs/cron_job.log', 'a');
$cron_file = dirname(__FILE__).'/cron_running.txt';

$dodo_interval = 250; /* these are seconds */
logMsg("Cron execution interval set to ".$dodo_interval." seconds", $log_file);

logMsg("Set PHP execution time to 'no limit'", $log_file);
try {
	set_time_limit(0);
} catch (Exception $e) {
	logMsg('Caught exception: ',  $e->getMessage(), "\n", $log_file);
}

logMsg("Disable user abort", $log_file);
try {
	ignore_user_abort(1);
} catch (Exception $e) {
	logMsg('Caught exception: ',  $e->getMessage(), "\n", $log_file);
}

logMsg("Register the shutdown function", $log_file);
register_shutdown_function('finished');
$cache_dir = dirname(__FILE__).'/../admin/cache/';

# On augmenete le temps d'execution
@set_time_limit(0);
@ini_set('max_execution_time',0);

# On augmente l'allocation memoire
@ini_set('output_buffering',0);

# On ignore les arrets venant du client
ignore_user_abort(true);

$stop_requested=false;
while(1){
	logMsg("Actual time : ".time()."", $log_file);
	$next = time() + $dodo_interval;
	logMsg("next execution time : ".$next, $log_file);

	$dodo = time();
	while ($dodo <= $next){
		sleep(5);
		if (file_exists(dirname(__FILE__).'/STOP')) {
			logMsg("STOP file detected, trying to shut down cron job", $log_file);
			$stop_requested=true;
			break;
		}

		$fp = @fopen($cron_file,'wb');
		if ($fp === false) {
			throw new Exception(sprintf(__('Cannot write %s file.'),$fichier));
		}
		fwrite($fp,time());
		fclose($fp);
		$dodo = time();
	}
	if ($stop_requested)
		break;
	logMsg("Start update script", $log_file);
	update($core);

	$cache_dir = dirname(__FILE__).'/../admin/cache';
	$dir_handle = @opendir($cache_dir) or die("Unable to open $cache_dir");
	while ($file = readdir($dir_handle)){
		if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
			unlink($cache_dir.'/'.$file);
		}
	}
	closedir($dir_handle);
	logMsg("Cleaning cache dir ".$cache_dir, $log_file);
	logMsg("Script ended\nMemory Usage : ".number_format(memory_get_usage()), $log_file);
}
logMsg("Shutdown complete - Cron job stopped\n", $log_file);
?>
