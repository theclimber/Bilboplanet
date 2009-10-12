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
include_once(dirname(__FILE__).'/head.php');
require_once(dirname(__FILE__).'/../inc/cron_fct.php');
$flash = '';
$update = false;
if(isset($_POST) && isset($_POST['action']) && !empty($_POST['action'])) {
	if ($_POST['action'] == '3') {
		$result = exec("touch ../inc/STOP");
		$flash = T_("The automatical update is disabled ").$result;
	}
	elseif ($_POST['action'] == '1') {
		if (get_cron_running())
			$error = T_('The update can not start : the process is already started (You can force update by deleting the /inc/cron_running.txt file)');
		else
			$flash = T_('The automatic update is enabled');
		$result = exec("rm ../inc/STOP");
	}
	else {
		$flash = T_("Manual update ...");
		$update = true;
	}
}

?>
<h2><?=T_('Automatic update');?></h2>
<?php 
if (!empty($flash)) echo '<div class="flash notice">'.$flash.'</div>';
elseif (!empty($error)) echo '<div class="flash error">'.$error.'</div>';
if (get_cron_running()) echo '<p>'.T_('The update is running').'</p>';
else
	echo '<p>'.T_('The update is stopped').'</p>';
if (file_exists('../inc/STOP')) echo "<p>".T_('The update is disabled')."</p>";
?>
<form method="POST">
<table width="450">
	<tr>
	<td>
		<input type="radio" name="action" value="3" /> <?=T_('Stop the update algorithm');?><br />
		<input type="radio" name="action" value="1" /> <?=T_('Start the update algorithm');?><br />
		<input type="radio" name="action" value="2" /> <?=T_('Start a manual update');?>
	</td>
		<td><input type="submit" value="<?=T_('Send');?>" /></td></tr>
</table>
</form>

<?php
if ($update) update(true);
include(dirname(__FILE__).'/footer.php');
if(isset($_POST) && isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == '1') {
	if (!get_cron_running())
		require_once(dirname(__FILE__).'/../inc/cron.php');
}
?>
