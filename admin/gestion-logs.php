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

<div id="BP_page" class="page">
	<div class="inpage">
	
<fieldset><legend><?=T_('All the log files');?></legend>
		<div class="message">
			<p><?=T_('Management planet log files (Advanced users).');?></p>
		</div><br/>
		
<center><table class="table-log">
<thead>
<tr>
<th class="tc1 tcl" scope="col"><?=T_('Name');?></th>
<th class="tc2 tcr" scope="col" /><?=T_('Action');?></th>

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
	<div class="button"><input type="submit" class="reset" value="'.T_('Purge all the log files').'"/></div></form></center>';

echo '</fieldset>';
include(dirname(__FILE__).'/footer.php'); ?>
