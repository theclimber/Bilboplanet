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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/fonctions.php');
require_once(dirname(__FILE__).'/../inc/config.php');

$title=stripslashes(BP_TITLE);
$desc=stripslashes(BP_DESC);
$msg_info=stripslashes(BP_MSG_INFO);
$email=BP_AUTHOR_MAIL;
$votes=BP_VOTES;
$contact=BP_CONTACT_PAGE;
$theme=BP_THEME;
$lang=BP_LANG;
$flash='';
$inscription='';
# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['submit'])) {
	# Fonction de securite
	securiteCheck();

	# On recupere les infos
	$title = stripslashes(trim($_POST['title']));
	$desc = stripslashes(trim($_POST['desc']));
	$msg_info = stripslashes(trim($_POST['msg_info']));
	$email = trim($_POST['email']);
	$inscription = stripslashes(trim($_POST['inscription']));
	$theme = trim($_POST['theme']);
	$lang = trim($_POST['lang']);

	$file=dirname(__FILE__).'/../inc/config.php';
	$full_conf = file_get_contents($file);
	writeConfigValue('BP_TITLE', $title, $full_conf);
	writeConfigValue('BP_DESC', $desc, $full_conf);
	writeConfigValue('BP_AUTHOR_MAIL', $email, $full_conf);
	writeConfigValue('BP_MSG_INFO', $msg_info, $full_conf);
	writeConfigValue('BP_THEME', $theme, $full_conf);
	writeConfigValue('BP_LANG', $lang, $full_conf);
	if ($_REQUEST['show_contact'] == "on") writeConfigValue('BP_CONTACT_PAGE', '1', $full_conf);
	else writeConfigValue('BP_CONTACT_PAGE', '0', $full_conf);
	if ($_REQUEST['show_votes'] == "on") writeConfigValue('BP_VOTES', '1', $full_conf);
	else writeConfigValue('BP_VOTES', '0', $full_conf);
	chmod($file, 0775);
	$fp = @fopen($file,'wb');
	if ($fp === false) {
		throw new Exception(sprintf(__('Cannot write %s file.'),$file));
	}
	fwrite($fp,$full_conf);
	fclose($fp);
	chmod($file, 0775);


	$file='../inscription_contenu.php';
	chmod($file, 0775);
	$fp = @fopen($file,'wb');
	if ($fp === false) {
		throw new Exception(sprintf(__('Cannot write %s file.'),$file));
	}
	fwrite($fp,$inscription);
	fclose($fp);
	chmod($file, 0775);

	$votes=$_REQUEST['show_votes'];
	$contact=$_REQUEST['show_contact'];
	$flash = array('type' => 'notice', 'msg' => T_("Modification succeeded"));
}

function writeConfigValue($name, $val, &$str){
	$val = str_replace("'","\'",$val);
	$str = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1,\''.$val.'\');',$str);
}


include_once(dirname(__FILE__).'/head.php');
?>

<h2><?=T_('Options');?></h2>

<div id="cadre">
<?php if (!empty($flash))echo '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>'; ?>

<form method="POST">
<h3><?=T_('Title of the Planet');?></h3>
<input id="cadre_options" type="text" name="title" size="80" value="<?php echo $title; ?>" /><br /><br />

<h3><?=T_('Description of the Planet');?></h3>
<input id="cadre_options" type="text" name="desc" size="80" value="<?php echo $desc; ?>" /><br /><br />

<h3><?=T_('Reference contact email');?></h3>
<input id="cadre_options" type="text" name="email" size="80" value="<?php echo $email; ?>" /><br /><br />

<h3><?=T_('Information message (optional)');?></h3>
<textarea id="cadre_options" name="msg_info" rows=3>
<?php echo $msg_info; ?>
</textarea>
<br /><br />

<h3><?=T_('Subscription page content');?></h3>
<textarea id="cadre_options" name="inscription" rows='40'>
<?php 
$file=dirname(__FILE__).'/../inscription_contenu.php';
echo stripslashes(file_get_contents($file));
?>
</textarea><br /><br />

<h3><?=T_('Other options');?></h3>
<div>
<label for="show_contact"><input type="checkbox" name="show_contact" id="cadre_options" <?php if ($contact) echo "checked"; ?>><?=T_('Show the contact page');?></label><br/>
<label for="show_votes"><input type="checkbox" name="show_votes" id="cadre_options" <?php if ($votes) echo "checked"; ?>><?=T_('Enable voting');?></label><br/>
</div>
<h3><?=T_('Graphical theme');?></h3>
<div>
<select name="theme">
<?php
$theme_path = dirname(__FILE__)."/../themes/";
$dir_handle = @opendir($theme_path) or die("Unable to open $theme_path");
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess" && is_dir($theme_path.$file)){
		$selected = "";
		if ($file == $theme)
			$selected = "selected";
		echo '<option value="'.$file.'" "'.$selected.'>'.$file.'</option>'."\n";
	}
}
closedir($dir_handle);
?>
</select>
</div>
<h3><?=T_('Language of the Planet');?></h3>
<div>
<select name="lang">
<?php
$lang_path = dirname(__FILE__)."/../i18n/";
$dir_handle = @opendir($lang_path) or die("Unable to open $theme_path");
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess" && is_dir($lang_path.$file)){
		$selected = "";
		if ($file == $lang)
			$selected = "selected";
		echo '<option value="'.$file.'" "'.$selected.'>'.$file.'</option>'."\n";
	}
}
closedir($dir_handle);
?>
</select>
</div>
</div>
<br />
<center><input type='submit' name="submit" value="<?=T_('Apply');?>"/></center>
</form>

<?php include(dirname(__FILE__).'/footer.php'); ?>
