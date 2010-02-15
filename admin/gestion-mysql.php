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
require_once(dirname(__FILE__).'/../inc/i18n.php');
require_once(dirname(__FILE__).'/../inc/fonctions.php');

if (isset($_POST)){
	if ($_POST['importform']=='Import'){
		# On recupere les infos
		if(!is_uploaded_file($_FILES['file']['tmp_name']) ){
			$flash = array('type' => 'error', 'msg' => T_('Your file could not be downloaded'));
		}
		elseif ($_FILES["file"]["error"] > 0){
			$flash = array('type' => 'error', 'msg' => T_("Your file could not be imported").'<br />'.$_FILES["file"]["error"]);
		}
		elseif( $_FILES["file"]["type"] != 'application/x-gzip'){
			$flash = array('type' => 'error', 'msg' => T_("Your file doesn't have the right format : ").'<br />'.$_FILES["file"]["type"]);
		}
		else {
			$response = T_('Congratulations! You imported an old data configuration successfully')."<br/>";
			$response .= T_("File name: ") . $_FILES["file"]["name"] . "<br />";
			$response .= T_("Size: ") . ($_FILES["file"]["size"] / 1024)." ".T_("Kb")."<br />";
			$gzfile = file_get_contents($_FILES['file']['tmp_name'], FILE_USE_INCLUDE_PATH);
			$content = my_gzdecode($gzfile);
			$tables = json_decode($content, true);

			$flash = array('type' => 'notice', 'msg' => $response);
			connectBD();
			foreach ($tables as $table){
				if ($table['name'] == "config"){
					// do nothing
				}
				else { // on insere le contenu dans la table
					$truncate = "TRUNCATE TABLE `".$table['name']."`";
					//echo $truncate."<br/>";
					$result = mysql_query($truncate);
					if (!$result)
						$flash = array('type' => 'error', 'msg' => sprintf(T_('Error while truncating table %s : %s'),$table['name'], mysql_error()));
					else {
						$cols = $table['head'];
						foreach($table['content'] as $key => $value){
							$n = 0;
							$values = "'".$key."'";
							while ($n < count($cols)-1){
								$values .= ",'".$value[$n]."'";
								$n+=1;
							}
							$sql = "INSERT INTO ".$table['name']." VALUES (".$values.")";
							$result = mysql_query($sql);
							//echo $sql."<br/>";
							if (!$result)
								$flash = array('type' => 'error', 'msg' => sprintf(T_('Error while appending table %s : %s'),$table['name'], mysql_error()));
						}
					}
				}
			}
			closeBD();
		}
	}
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="BP_page" class="page">
	<div class="inpage">
	
<?php
if (!empty($flash)) {
	echo '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>';
}
?>
<fieldset><legend><?php echo T_('Export planet configuration');?></legend>
<br />

<div class="message"><?php echo T_('Please select the data you want to export'); ?></div>
<p><?php echo T_('This will export the data to a file that you\'ll be able to import later on another installation'); ?></p>
<br/>

<div id="export-log" style="display:none;">
	<h3><?php echo T_('Exported data');?></h3>
	<div id="export_res"><!-- spanner --></div>
</div>

<form id="exportform" action="manage-database.php" method="post">
<ul>
	<li><input type="checkbox" name="list[]" value="membre" /><?php echo T_('Members table'); ?></li>
	<li><input type="checkbox" name="list[]" value="flux" /><?php echo T_('Feeds table'); ?></li>
	<li><input type="checkbox" name="list[]" value="article" /><?php echo T_('Articles table'); ?></li>
	<li><input type="checkbox" name="list[]" value="votes" /><?php echo T_('Votes table'); ?></li>
	<li><input type="checkbox" name="config" value="config" /><?php echo T_('Configuration file'); ?></li>
</ul>
<br/>
<div class="button"><input type="submit" name="exportform" value="<?php echo T_("Export"); ?>"/></div>
</form>

</p>
<br />
</fieldset>
<fieldset><legend><?php echo T_('Import planet configuration');?></legend>
<br />
<div class="message">
<p><?php echo T_('Please select the file you want to restore.');?>
<br /><b><font color=red><?php echo T_('TAKE CARE !');?></font></b> <?=T_('If you apply the content of one of this file, this action can not be cancelled');?></p>
</div>
<br />

<form id="importform" enctype="multipart/form-data" method="post">
<p>
<?php echo T_('Select your backup file :'); ?> <input type="file" name="file" id="file" />
</p>
<br/>
<div class="button"><input type="submit" name="importform" value="<?php echo T_("Import"); ?>"/></div>
</form>
<br />
<p><i>
<?php echo T_('NOTE : to import a backup file, the file needs to have the *.json.gz extension !');?>
</i></p>
<script type="text/javascript" src="meta/js/import-export.js"></script>

<?php include(dirname(__FILE__).'/footer.php');?>
