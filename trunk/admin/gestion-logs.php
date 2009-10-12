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
<?php include_once(dirname(__FILE__).'/head.php');
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/fonctions.php');

# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['nom']) && isset($_POST['path']) && isset($_POST['action'])) {
	# Fonction de securite
	securiteCheck();
	# On recupere les infos
	$nom = trim($_POST['nom']);
	$path = trim($_POST['path']);
	$action = trim($_POST['action']);

	# On insert une nouvelle entree
	if($action=="del" && $nom!="all")
		$result = exec("rm -f $path/$nom");
	elseif($action=="del" && $nom=="all")
		$result = exec("rm -f $path/*");
}
?>
	<p><?=T_('All the log files');?></p><br />
<table>
<tr><td><?=T_('Name');?></td><td><?=T_('Action');?></td><td></td></tr>
<?php
$log_path = dirname(__FILE__)."/../logs";
//using the opendir function
$dir_handle = @opendir($log_path) or die("Unable to open $log_path");
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess")
		# Affichage de la ligne de tableau
		echo '<form method="POST"><tr>
			<input type="hidden" name="nom" value="'.$file.'"/>
			<input type="hidden" name="path" value="'.$log_path.'"/>
			<td><a href=../logs/'.$file.'>'.$file.'</a></td>
			<td><input type="radio" name="action" value="del"> '.T_('Supprimer').'</td>
			<td><input type="submit" value="'.T_('Ok').'"/></td></tr></form>';
}
closedir($dir_handle);

?>
</table></p>
<br />&nbsp;
<p>
<?php 

echo '<center><form method="POST">
	<input type="hidden" name="nom" value="all"/>
	<input type="hidden" name="path" value="'.$log_path.'"/>
	<input type="hidden" name="action" value="del">
	<input type="submit" value="'.T_('Purge all the log files').'"/></form></center>';

include(dirname(__FILE__).'/footer.php'); ?>
