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
$planet_author_jabber=BP_AUTHER_JABBER;
$planet_author_im=BP_AUTHOR_IM;
$planet_author_about=stripslashes(BP_AUTHOR_ABOUT);
$flash='';

# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['submit'])) {
	# On recupere les infos
	$email = check_field('email',trim($_POST['email']),'email');
	$planet_author = trim($_POST['planet_author']);
	$planet_author_site = trim($_POST['planet_author_site']);
	$planet_author_jabber = trim($_POST['planet_author_jabber']);
	$planet_author_im = trim($_POST['planet_author_im']);
	$planet_author_about = stripslashes(trim($_POST['planet_author_about']));

	if ($email['success']){
		$file=dirname(__FILE__).'/../inc/config.php';
		$full_conf = file_get_contents($file);
		writeConfigValue('BP_AUTHOR_MAIL', $email['value'], $full_conf);
		writeConfigValue('BP_AUTHOR', $planet_author, $full_conf);
		writeConfigValue('BP_AUTHOR_SITE', $planet_author_site, $full_conf);
		writeConfigValue('BP_AUTHER_JABBER', $planet_author_jabber, $full_conf);
		writeConfigValue('BP_AUTHOR_IM', $planet_author_im, $full_conf);
		writeConfigValue('BP_AUTHOR_ABOUT', $planet_author_about, $full_conf);
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
	else {
		$flash = array('type' => 'error', 'msg' => T_("Please insert a valid email address"));
	}
}
// Changement de mot de passe
if(isset($_POST) && isset($_POST['submitPwd'])) {
	$login = check_field('login',$_POST['user_login'], 'not_empty');
	$password = check_field('password',$_POST['password'], 'not_empty');
	$password2 = check_field('password_confirm',$_POST['password_confirm'], 'not_empty');

	if ($password['success'] && $password2['success'] && $password == $password2) {
		if ($login['success']) {
			try {
				changePassword($login['value'], $password['value']);
				$flash = array('type' => 'notice', 'msg' => T_("Modification of the password succeeded"));
			} catch (Exception $e) {
				$flash = array('type' => 'error', 'msg' => sprintf(T_("Error : %s"),$e->getMessage()));
			}
		}
		else {
			$flash = array('type' => 'error', 'msg' => T_("Please fill the login in"));
		}
	}
	else {
		$flash = array('type' => 'error', 'msg' => T_("Please be sure the two passwords are equal"));
	}
}

function writeConfigValue($name, $val, &$str){
	$val = str_replace("'","\'",$val);
	$str = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1,\''.$val.'\');',$str);
}

function changePassword($u_login, $u_pwd) {
	define('BP_PROT_PATH',dirname(__FILE__).'/../.htpasswd');
	# Can we write .protected
	if (!is_writable(dirname(BP_PROT_PATH))) {
		throw new Exception(sprintf(T_('Unable to write %s file.'),BP_PROT_PATH));
	}
	
	# Creates .protected file
	$user_login = (string) $u_login;
	$user_passwd = crypt(trim($u_pwd),base64_encode(CRYPT_STD_DES));
	
	$string = $user_login.':'.$user_passwd ;

	# Create the .protected file
	$fp = @fopen(BP_PROT_PATH,'wb');
	if ($fp === false) {
		throw new Exception(sprintf(T_('Unable to write %s file.'),BP_PROT_PATH));
	}
	fwrite($fp,$string);
	fclose($fp);
	chmod(BP_PROT_PATH, 0666);
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="BP_page" class="page">
	<div class="inpage">
	
<?php if (!empty($flash)) echo '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>'; ?>

<fieldset><legend><?php echo T_('Users Options');?></legend>
		<div class="message">
			<p><?php echo T_('Configuring user settings.');?></p>
		</div><br />

<form method="POST">

<?php echo T_('Contact Name');?><br />
<input type="text" name="planet_author" size="60" class="input" value="<?php echo $planet_author; ?>" /><br /><br />

<?php echo T_('Reference contact email');?><br />
<input type="text" name="email" size="60" class="input" value="<?php echo $email; ?>" /><br /><br />

<?php echo T_('Author Website');?><br />
<input type="text" name="planet_author_site" class="input" size="60" value="<?php echo $planet_author_site; ?>" /><br /><br />

<?php echo T_('Jabber / GoogleTalk');?><br />
<input type="text" name="planet_author_jabber" class="input" size="60" value="<?php echo $planet_author_jabber; ?>" /><br /><br />

<?php echo T_('Other Instant Messaging');?><br />
<input type="text" name="planet_author_im" class="input" size="60" value="<?php echo $planet_author_im; ?>" /><br /><br />

<?php echo T_('About Me');?><br />
<textarea type="text" name="planet_author_about" class="cadre_about" rows="10" value="<?php echo $planet_author_about; ?>" /><?php echo $planet_author_about; ?></textarea><br /><br />


<div class="button"><input type='submit' class="valide" name="submit" value="<?php echo T_('Apply');?>"/></div>
</form>
</fieldset>

<div class="clear">&nbsp;<br/></div>

<fieldset><legend><?php echo T_('User login and password');?></legend>
		<div class="message">
			<p><?php echo T_('Change the login and the password of the admin interface.');?></p>
		</div><br />
<form method="POST">
<div>
<label for="user_login"><?php echo T_('Login');?></label>
<input type="text" name="user_login" class="input" size="30" value="" /><br/><br/>
<label for="password"><?php echo T_('New password');?></label>
<input type="text" name="password" class="input" size="30" value="" /><br/><br/>
<label for="password_confirm"><?php echo T_('Confirm password');?></label>
<input type="text" name="password_confirm" class="input" size="30" value="" /><br/><br/>
<div class="button"><input type='submit' class="valide" name="submitPwd" value="<?php echo T_('Apply');?>"/></div>
</form>

</fieldset>

<?php include(dirname(__FILE__).'/footer.php'); ?>
