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
include_once(dirname(__FILE__).'/../inc/admin/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager') | !$core->hasPermission('configuration')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}
include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
include_once(dirname(__FILE__).'/../inc/cron_fct.php');

$flash = '';
$update = false;
$index_update = $blog_settings->get('planet_index_update');

if(isset($_POST) && isset($_POST['submit'])) {
	if ($_POST['index_update'] == "on") {
		$blog_settings->put('planet_index_update','1', "boolean");
		$index_update = '1';
	}
	else {
		$blog_settings->put('planet_index_update','0', "boolean");
		$index_update = '0';
	}

	if (isset($_POST['action']) && !empty($_POST['action'])){
		if ($_POST['action'] == '3') {
			$fp = @fopen(dirname(__FILE__).'/../inc/STOP','wb');
			if ($fp === false) {
				throw new Exception(sprintf(__('Cannot write %s file.'),$fichier));
			}
			fwrite($fp,time());
			fclose($fp);
			$flash = T_("The automatical update is disabled ").$result;
			header("Location: ./gestion-update.php");
		}
		elseif ($_POST['action'] == '1') {
			if (get_cron_running())
				$error = T_('The update can not start : the process is already started (You can force update by deleting the /inc/cron_running.txt file)');
			else
				$flash = T_('The automatic update is enabled');
			unlink(dirname(__FILE__).'/../inc/STOP');
			header("Location: ./gestion-update.php");
		}
		else {
			$update = true;
			try{
				$update_logs = update($core, true);
				$flash = T_("Manual update ...");
			}
			catch(Exception $e){
				$error = sprintf(T_('Error while updating : %s'), $e->getMessage());
			}
		}
	}

}

?>

<div id="BP_page" class="page">
	<div class="inpage">
	
<?php 
if (!empty($flash))
	echo '<div class="flash notice">'.$flash.'</div>';
elseif (!empty($error))
	echo '<div class="flash error">'.$error.'</div>';
?>
<fieldset><legend><?php echo T_('Automatic update');?></legend>
	<div class="message">
		<p><?php echo T_('System configuration update.');?></p>
	</div><br />	
<?php
if (get_cron_running()) echo '<div id="BP_startupdate">'.T_('The update is running').'</div><br />';
else
	echo '<div id="BP_stopupdate">'.T_('The update is stopped').'</div><br />';
if (file_exists(dirname(__FILE__).'/../inc/STOP')) echo '<div id="BP_disableupdate">'.T_('The update is disabled').'</div><br />';
?>
<form method="POST">
	<label for="stop_update"><input id="stop_update" type="radio" name="action" value="3" /> <?php echo T_('Stop the update algorithm');?></label><br />
	<label for="start_update"><input id="start_update" type="radio" name="action" value="1" /> <?php echo T_('Start the update algorithm');?></label><br />
	<label for="manual_update"><input id="manual_update" type="radio" name="action" value="2" /> <?php echo T_('Start a manual update');?><br /></label><br />

<?php
$checked = '';
if ($index_update)
	$checked = "checked";

echo '<label for="enable_on_index"><input id="enable_on_index" type="checkbox" name="index_update" '.$checked.' /> '.T_('Enable update on loading of index page').'</label><br /><br />';
?>

<div class="button br3px"><input name="submit" type="submit" class="valide" value="<?php echo T_('Send');?>" /></div>
</form>
</fieldset>
<fieldset><legend><?=T_('Setup manual crontab');?></legend>
		<div class="message">
			<p><?php echo T_('How to do a manual update ?');?></p>
		</div><br />
		
<p><?php echo T_('You can setup a manual crontab update by calling automatically every X time the following page :'); ?><br/>
<b><?php echo $blog_settings->get('planet_url')."/inc/update_manual.php"; ?></b></p><br />
<p><?php echo T_('This will automatically launch the update and log it into the log files.'); ?></p>
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

else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('auth.php?came_from='.$page_url);
endif;
?>
