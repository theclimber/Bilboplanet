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
# On efface les fichiers de cache du site
$cache_dir = dirname(__FILE__).'/cache/';
$files = "";

# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['action'])) {
	# On recupere les infos
	$action = trim($_POST['action']);

	//using the opendir function
	$dir_handle = @opendir($cache_dir) or die("Unable to open $cache_dir");
	while ($file = readdir($dir_handle)){
		if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
			unlink($cache_dir.'/'.$file);
			$files .= 'remove '.$file."\n";
		}
	}
	closedir($dir_handle);
	# Message d'information
	$flash = T_('The cache files were correctly deleted.');
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');

echo '<div id="BP_page" class="page">
	 <div class="inpage">';

if (!empty($flash)) echo '<div class="flash notice">'.$flash.'</div>';

echo '<fieldset><legend>'.T_('Manage cache').'</legend>
		<div class="message">
			<p>'.T_('Remove the cache files').'</p>
		</div><br />';
echo '<center><form method="POST">
	<input type="hidden" name="action" value="del">
	<div class="button"><input type="submit" class="reset" value="'.T_('Delete cache').'"/></div></form></center>';

if ($_POST['action']) {
	echo T_('The following files were deleted :')."<br /><pre>";
	echo $files;
	echo "</pre>";
}


echo '</fieldset>';

include(dirname(__FILE__).'/footer.php');
?>
