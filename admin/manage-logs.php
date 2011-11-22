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
require_once(dirname(__FILE__).'/../inc/admin/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager') | !$core->hasPermission('configuration')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.').' '.
			T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}
include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');

# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['nom']) && isset($_POST['path']) && isset($_POST['action'])) {
	# On recupere les infos
	$nom = trim($_POST['nom']);
	$path = trim($_POST['path']);
	$action = trim($_POST['action']);

	# On insert une nouvelle entree
	if($action=="del" && $nom!="all")
		unlink($path.'/'.$nom);
	elseif($action=="del" && $nom=="all"){
		$dir_handle = @opendir($path) or die("Unable to open $path");
		while ($file = readdir($dir_handle)){
			if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
				unlink($path.'/'.$file);
			}
		}
		closedir($dir_handle);
	}
}
?>

<div id="BP_page" class="page">
	<div class="inpage">

<fieldset><legend><?php echo T_('All the log files');?></legend>
		<div class="message">
			<p><?php echo T_('Management planet log files (Advanced users).');?></p>
		</div><br/>

<center><table class="table-log">
<thead>
<tr>
<th class="tc1 tcl" scope="col"><?php echo T_('Name');?></th>
<th class="tc2 tcr" scope="col" /><?php echo T_('Action');?></th>

<?php
$log_path = dirname(__FILE__)."/../logs";
//using the opendir function
$dir_handle = @opendir($log_path) or die("Unable to open $log_path");
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess")
		# Affichage de la ligne de tableau
		echo '<form method="POST">
			<tr>
			<input type="hidden" name="nom" value="'.$file.'"/>
			<input type="hidden" name="path" value="'.$log_path.'"/>
			<td class="tc1 tcl"><a href=../logs/'.$file.'>'.$file.'</a></td>
			<td class="tc2 tcr"><center>
				<input type="hidden" name="action" value="del">
				<input class="button br3px" type="submit" value="'.T_('Delete').'"/></center>
			</td>
			</tr>
			</form>';
}
closedir($dir_handle);

?>
</td>
</tr>
</thead>
</table>
</center>
<br />&nbsp;
<p>
<?php

echo '<center><form method="POST">
	<input type="hidden" name="nom" value="all"/>
	<input type="hidden" name="path" value="'.$log_path.'"/>
	<input type="hidden" name="action" value="del">
	<div class="button br3px"><input type="submit" class="reset" value="'.T_('Purge all the log files').'"/></div></form></center>';

echo '</fieldset>';
include(dirname(__FILE__).'/footer.php');
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
