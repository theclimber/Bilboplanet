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
$flash = array();
$update = false;
$index_update = $blog_settings->get('planet_index_update');

if(isset($_POST) && isset($_POST['submit'])) {
	if (isset($_POST['index_update']) && $_POST['index_update'] == "on") {
		if (!$index_update) {
			$blog_settings->put('planet_index_update','1', "boolean");
			$flash[] = T_('Enable update on loading of index page: Enable');
					$index_update = '1';
		}
	}
	else {
		if ($index_update) {
			$blog_settings->put('planet_index_update','0', "boolean");
			$flash[] = T_('Enable update on loading of index page: Disable');
			$index_update = '0';
		}
	}

	if (isset($_POST['action']) && !empty($_POST['action'])){
		if ($_POST['action'] == '3') {
			$fp = @fopen(dirname(__FILE__).'/../inc/STOP','wb');
			if ($fp === false) {
				throw new Exception(sprintf(__('Cannot write %s file.'),$fichier));
			}
			fwrite($fp,time());
			fclose($fp);
			$flash[] = T_("The automatical update is disabled ").$result;
			header("Location: ./manage-update.php");
		}
		elseif ($_POST['action'] == '1') {
			if (get_cron_running())
				$error[] = T_('The update can not start : the process is already started (You can force update by deleting the /inc/cron_running.txt file)');
			else
				$flash[] = T_('The automatic update is enabled');
			unlink(dirname(__FILE__).'/../inc/STOP');
			header("Location: ./manage-update.php");
		}
		else {
			$update = true;
			try{
				$update_logs = update($core, true);
				$flash[] = T_("Manual update ...");
			}
			catch(Exception $e){
				$error[] = sprintf(T_('Error while updating : %s'), $e->getMessage());
			}
		}
	}

}

?>
<script type="text/javascript" src="meta/js/manage-update.js"></script>
<div id="BP_page" class="page">
	<div class="inpage">
	<div id="flash-log" style="display:none;">
		<div id="flash-msg"><!-- spanner --></div>
	</div>

<?php
if (!empty($flash)) {
	$msg = '<ul>';
	foreach ($flash as $value) {
		$msg .= '<li>'.$value.'</li>';
	}
	echo '<div id="post_flash" class="flash_notice" style="display:none;" >'.$msg.'</div>';
}
elseif (!empty($error)) {
	foreach ($error as $value) {
		$msg .= '<li>'.$value.'</li>';
	}
	echo '<div id="post_flash" class="flash_error" style="display:none;" >'.$value.'</div>';
}
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
<form id="form_manage-update" method="POST">
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
<fieldset><legend><?=T_('Setup feed update');?></legend>
		<div class="message">
			<p><?php echo T_('How to configure the update of the feeds ?');?></p>
		</div><br />
<div class="help">
<h2><?php echo T_('How does it work?'); ?></h2>
<p><?php echo T_("The update algorithm can work on several ways. The optimal solution that consumes less ressources and will give you the best results is clearly to setup a cron update manually into your server. But here you'll find a short explanation of the several methods :"); ?>
<ul>
	<li><?php echo T_("The first intuitive way is to use this page to update manually the feeds of your database by clicking on the 'Start a manual update' option in the menu above. This means that it will update all your feeds only once."); ?></li>
	<li><?php echo T_("The second solution is to click on the 'Start the update algorithm' in the form. This will enable a PHP script which will fetch your feeds. This can be done but needs some special PHP libraries on your web server that may not be installed. This solution can be used but we advise you to check sometimes if the update engine is still alive (it can be killed after a time)"); ?></li>
	<li><?php echo T_("Another intuitive way to configure the update of your feeds is by enabling update on index page loading. This means that all your feeds will be updated whenever people connect to your site. This is a good manner to deal with the update but can slightly slow down your site."); ?></li>
	<li><?php echo T_("The optimal solution as said before it to configure manually the crontab of your server to enable the update. To configure a cron manually you'll find some advice here under."); ?></li>
</ul></p>
</div>

<p><?php echo T_('You can setup a manual crontab update by calling automatically every X time the following page :'); ?><br/>

<b><?php
$token = $core->auth->userToken();
echo BP_PLANET_URL."/inc/update_manual.php?token=".$token; ?></b><br />
<?php echo T_('This will automatically launch the update and log it into the log files.'); ?></p>
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
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
