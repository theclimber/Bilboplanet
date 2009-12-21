<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
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
include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
include_once(dirname(__FILE__).'/../inc/cron_fct.php');

$flash = '';
$update = false;
if(isset($_POST)) {
	$config_file=dirname(__FILE__).'/../inc/config.php';
	$full_conf = file_get_contents($config_file);
	if ($_POST['index_update'] == "on") {
		writeConfigValue('BP_INDEX_UPDATE', '1', $full_conf);
	}
	else {
		writeConfigValue('BP_INDEX_UPDATE', '0', $full_conf);
	}
	chmod($file, 0775);
	$fp = @fopen($config_file,'wb');
	if ($fp === false) {
		throw new Exception(sprintf(__('Cannot write %s file.'),$config_file));
	}
	fwrite($fp,$full_conf);
	fclose($fp);
	chmod($config_file, 0775);

	if (isset($_POST['action']) && !empty($_POST['action'])){
		if ($_POST['action'] == '3') {
			$result = exec("touch ../inc/STOP");
			$flash = T_("The automatical update is disabled ").$result;
			header("Location: ./gestion-update.php");
		}
		elseif ($_POST['action'] == '1') {
			if (get_cron_running())
				$error = T_('The update can not start : the process is already started (You can force update by deleting the /inc/cron_running.txt file)');
			else
				$flash = T_('The automatic update is enabled');
			$result = exec("rm ../inc/STOP");
			header("Location: ./gestion-update.php");
		}
		else {
			$update = true;
			try{
				$update_logs = update(true);
				$flash = T_("Manual update ...");
			}
			catch(Exception $e){
				$error = sprintf(T_('Error while updating : %s'), $e->getMessage());
			}
		}
	}
}
function writeConfigValue($name, $val, &$str){
	$val = str_replace("'","\'",$val);
	$str = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1,\''.$val.'\');',$str);
}

?>

<div id="BP_page" class="page">
	<div class="inpage">
	
<?php 
if (!empty($flash)) echo '<div class="flash notice">'.$flash.'</div>';
elseif (!empty($error)) echo '<div class="flash error">'.$error.'</div>';
?>

<fieldset><legend><?=T_('Automatic update');?></legend>
		<div class="message">
			<p><?=T_('System configuration update.');?></p>
		</div><br />
		


<?php
if (get_cron_running()) echo '<div id="BP_startupdate">'.T_('The update is running').'</div><br />';
else
	echo '<div id="BP_stopupdate">'.T_('The update is stopped').'</div><br />';
if (file_exists(dirname(__FILE__).'/../inc/STOP')) echo '<div id="BP_disableupdate">'.T_('The update is disabled').'</div><br />';
?>
<form method="POST">
	<input type="radio" name="action" value="3" /> <?=T_('Stop the update algorithm');?><br />
	<input type="radio" name="action" value="1" /> <?=T_('Start the update algorithm');?><br />
	<input type="radio" name="action" value="2" /> <?=T_('Start a manual update');?><br /><br />

<?php
$checked = "";
if (BP_INDEX_UPDATE == '1' || $_POST['index_update'] == "on") {
	$checked = "checked=true";
}
echo '<input type="checkbox" name="index_update" '.$checked.' /> '.T_('Enable update on loading of index page').'<br /><br />';
?>


<div class="button"><input type="submit" class="valide" value="<?=T_('Send');?>" /></div>
</form>
</fieldset>

<?php
if($update) {
	echo '<div class="update_logs">'.$update_logs.'</div>';
}
include(dirname(__FILE__).'/footer.php');
if(isset($_POST) && isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == '1') {
	if (!get_cron_running())
		require_once(dirname(__FILE__).'/../inc/cron.php');
}

include(dirname(__FILE__).'/footer.php');

?>
