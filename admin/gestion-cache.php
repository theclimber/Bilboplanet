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
<?php include_once(dirname(__FILE__).'/head.php'); ?>
<?php
# On efface les fichiers de cache du site
$cache_dir = dirname(__FILE__).'/cache/';

# Message d'information
$flash = T_('The cache files were correctly deleted.');
if (!empty($flash)) echo '<div class="flash notice">'.$flash.'</div>';

echo T_('The following files were deleted :')."<br /><pre>";
//using the opendir function
$dir_handle = @opendir($cache_dir) or die("Unable to open $cache_dir");
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess"){
		$result = exec('cd '.$cache_dir.' && rm -vf '.$file);
		if ($result) echo $result."\n";
	}
}
closedir($dir_handle);
echo "</pre>";

include(dirname(__FILE__).'/footer.php');
?>
