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
require_once(dirname(__FILE__).'/../inc/config.php');

$email=BP_AUTHOR_MAIL;
$planet_author=BP_AUTHOR;
$planet_author_site=BP_AUTHOR_SITE;
$flash='';

# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['submit'])) {
	# Fonction de securite
	securiteCheck();

	# On recupere les infos
	$email = trim($_POST['email']);
	$planet_author = trim($_POST['planet_author']);
	$planet_author_site = trim($_POST['planet_author_site']);
	
	$file=dirname(__FILE__).'/../inc/config.php';
	$full_conf = file_get_contents($file);
	writeConfigValue('BP_AUTHOR_MAIL', $email, $full_conf);
	writeConfigValue('BP_AUTHOR', $planet_author, $full_conf);
	writeConfigValue('BP_AUTHOR_SITE', $planet_author_site, $full_conf);
	chmod($file, 0775);
	$fp = @fopen($file,'wb');
	if ($fp === false) {
		throw new Exception(sprintf(__('Cannot write %s file.'),$file));
	}
	fwrite($fp,$full_conf);
	fclose($fp);
	chmod($file, 0775);
		$flash = array('type' => 'notice', 'msg' => T_("Modification succeeded"));
}

function writeConfigValue($name, $val, &$str){
	$val = str_replace("'","\'",$val);
	$str = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1,\''.$val.'\');',$str);
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="BP_page" class="page">
	<div class="inpage">
	
<?php if (!empty($flash))echo '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>'; ?>

<fieldset><legend><?=T_('Users Options');?></legend>
		<div class="message">
			<p><?=T_('Configuring user settings.');?></p>
		</div><br />

<form method="POST">

<?=T_('Contact Name');?><br />
<input type="text" name="planet_author" size="60" class="input" value="<?php echo $planet_author; ?>" /><br /><br />

<?=T_('Reference contact email');?><br />
<input type="text" name="email" size="60" class="input" value="<?php echo $email; ?>" /><br /><br />

<?=T_('Author Website');?><br />
<input type="text" name="planet_author_site" class="input" size="60" value="<?php echo $planet_author_site; ?>" /><br /><br />

<?=T_('Jabber / GoogleTalk');?><br />
<input type="text" name="planet_author_jabber" class="input" size="60" value="<?php echo $planet_author_jabber; ?>" /><br /><br />

<?=T_('Other Instant Messaging');?><br />
<input type="text" name="planet_author_im" class="input" size="60" value="<?php echo $planet_author_im; ?>" /><br /><br />

<?=T_('About Me');?><br />
<textarea type="text" name="planet_author_about" class="cadre_about" rows="10" value="<?php echo $planet_author_about; ?>" /></textarea><br /><br />

<?=T_('New Password');?><br />
<input type="text" name="planet_author_password" class="input" size="30" value="<?php echo $planet_author_password; ?>" /><br /><br />
<input type="text" name="planet_author_password" class="input" size="30" value="<?php echo $planet_author_password; ?>" />&nbsp;<?=T_('retype your new password')?><br /><br />

<div class="button"><input type='submit' class="valide" name="submit" value="<?=T_('Apply');?>"/></div>
</form>
</fieldset>

<?php include(dirname(__FILE__).'/footer.php'); ?>
