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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/fonctions.php');

# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['nom']) && isset($_POST['path'])) {

	# On recupere les infos
	$nom = trim($_POST['nom']);
	$path = trim($_POST['path']);

	# Connection a la base

	# On insert une nouvelle entree
	if(isset($_POST) && isset($_POST['import']) && !empty($_POST['import'])){
		$result = exec("gunzip < $path/$nom | mysql -h $db_host -u $db_login --password=$db_passw $db_name");
		$result = exec("gunzip < $path/$nom | mysql5 -h $db_host -u $db_login --password=$db_passw $db_name");
		$flash = array('type' => 'notice', 'msg' => T_('File successfully imported'));
	}
	else{
		$result = exec("rm -f $path/$nom");
		$flash = array('type' => 'notice', 'msg' => sprintf(T_('File %s successfully deleted'),$nom));
	}

	if(!$result) {
		$flash = array('type' => 'error', 'msg' => T_('Error while trying to modify database informations'));
	}
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="BP_page" class="page">
	<div class="inpage">
	
<?php if (!empty($flash))echo '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>'; ?>


<fieldset><legend><?=T_('Export database');?></legend>


	
<?php

# On augmenete le temps d'execution
@set_time_limit(0);
@ini_set('max_execution_time',0);

# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/fonctions.php');

# On recupere la date du jour
$date = date("Y-m-d");
$snapshot_path = "mysql/snapshot";
$snapshot_file = "$snapshot_path/$db_name.$date.sql";

# Creation du dossier qui va stocker les dumps si il n'existe pas
if(!is_dir($snapshot_path)) mkdir($snapshot_path, 0700);

# On efface tous les dumps qui existe
exec("rm -f $snapshot_path/*");

# On fait un dump de la base
exec("mysqldump -h $db_host -u $db_login --password=$db_passw $db_name > $snapshot_file");
exec("mysqldump5 -h $db_host -u $db_login --password=$db_passw $db_name > $snapshot_file");

# On compresse le fichier si le dump c'est bien passe
if(is_file($snapshot_file)) $compress = exec("gzip -f $snapshot_file");


# On affcihe un message
if(is_file("$snapshot_file.gz")) {
echo "			<div class='message'>";
echo "<p>".sprintf(T_("The current database is accessible here (in the directory %s)"),$snapshot_path)."</p>";
echo "<p>".sprintf(T_("When you reload this page, a new snapchot of your current database will be generated"));
echo "</div><br />";
			
	echo "<center>
	<table class='table-mysql'>
	<thead>
		<tr>
			<th class='tc1 tcl' scope='col'>".T_('Name')."</th>
			<th class='tc2' scope='col'>".T_('Size')."</th>
		</tr>
	</thead>";
	//using the opendir function
	$dir_handle = @opendir($snapshot_path) or die("Unable to open $snapshot_path");
	while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store")
		echo"<tr>
		<td>
		<a href='mysql/snapshot/$file'>$file</a></td>
		<td>". filesize($file) ."</td></tr>";
	}
	echo "</table></center>";
	closedir($dir_handle);

} else {

echo "<div class='message'>";
echo "<p>" .sprintf(T_("The actual database is accessible here (in the directory %s)"),$snapshot_path)."</p>";
echo "</div>";
}
?>
</p>
<br />
<hr />
<br />
<div class="message">
<p><?=T_('Other backup of the database in the directory /admin/mysql/backup.');?>
<br /><b><font color=red><?=T_('TAKE CARE !');?></font></b> <?=T_('If you apply the content of one of this file, this action can not be cancelled');?></p>
</div>
<br />
<center>
<table class="table-mysql table-mysql-600px">
	<thead>
		<tr>
			<th class="tc3 tcl" scope="col"><?=T_('Name');?></th>
			<th class='tc4 tcr' scope='col'><?=T_('Action');?></th>
		</tr>
	</thead>
<?php
$backup_path = "mysql/backup";
//using the opendir function
$dir_handle = @opendir($backup_path) or die("Unable to open $backup_path");
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store")
		# Affichage de la ligne de tableau
		echo '<form method="POST"><tr>
			<input type="hidden" name="nom" value="'.$file.'"/>
			<input type="hidden" name="path" value="'.$backup_path.'"/>
			<td><a href=mysql/backup/'.$file.'>'.$file.'</a></td>
			<td><center>
			<input type="submit" class="button br3px" name="import" value="'.T_('Import').'">
			<input type="submit" class="button br3px"name="del" value="'.T_('Delete').'"> 
			</center></td>';
}
closedir($dir_handle);

?>
</table></center>
<br />
<p><i>
<?=T_('NOTE : to import a database file, the file needs to have the *.sql.gz extension !');?>
</i></p>
<?php include(dirname(__FILE__).'/footer.php');?>
