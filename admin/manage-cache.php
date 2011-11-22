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
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}
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
	$flash = array( 'type' => 'notice', 'msg' => T_('The cache files were correctly deleted.'));
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>
<script type="text/javascript" src="meta/js/manage-cache.js"></script>
<div id="BP_page" class="page">
	 <div class="inpage">

<?php
if (!empty($flash)) {
	echo '<div id="post_flash" class="flash_'.$flash['type'].'" style="display:none;">'.$flash['msg'].'</div>';
}
echo '
<fieldset>
	<legend>'.T_('Manage cache').'</legend>
	<div class="message">
		<p>'.T_('Remove the cache files').'</p>
	</div>
	<br />
	<center>
		<form method="POST">
			<input type="hidden" name="action" value="del">
			<div class="button br3px"><input type="submit" class="reset" value="'.T_('Delete cache').'"/></div>
		</form>
	</center>';

if ($_POST['action']) {
	echo T_('The following files were deleted :')."<br /><pre>";
	echo $files;
	echo "</pre>";
}

echo '</fieldset>';

include(dirname(__FILE__).'/footer.php');
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
