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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/admin/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager') | !$core->hasPermission('configuration')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}

$planet_author_mail = $email = $blog_settings->get('author_mail');
$planet_author_site = $url = $blog_settings->get('author_site');
$planet_author = $author = $blog_settings->get('author');
$planet_author_jabber = $blog_settings->get('author_jabber');
$planet_author_im = $blog_settings->get('author_im');
$planet_author_about = $blog_settings->get('author_about');
$flash = array();

# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['submit'])) {
	# On recupere les infos
	$email = check_field(T_('Reference contact email'), trim($_POST['planet_author_mail']), 'email');
	$url = check_field(T_('Author Website'), trim($_POST['planet_author_site']), 'url');
	$author = check_field(T_('Contact Name'), trim($_POST['planet_author']), 'not_empty');
	$planet_author_jabber = trim($_POST['planet_author_jabber']);
	$planet_author_im = trim($_POST['planet_author_im']);
	$planet_author_about = htmlentities($_POST['planet_author_about'],ENT_QUOTES,mb_detect_encoding($_POST['planet_author_about']));

	if ($email['success'] && $url['success'] && $author['success']){
		$planet_author_mail = $email['value'];
		$planet_author_site = $url['value'];
		$planet_author = $author['value'];
		$blog_settings->put('author', $planet_author, "string");
		$blog_settings->put('author_mail', $planet_author_mail, "string");
		$blog_settings->put('author_site', $planet_author_site, "string");
		$blog_settings->put('author_jabber', $planet_author_jabber, "string");
		$blog_settings->put('author_im', $planet_author_im, "string");
		$blog_settings->put('author_about', $planet_author_about, "string");
		$flash['notice'][] = T_("Modification succeeded");
	}
	else {
		if(!$email['success']) {
			$flash['error'][] = htmlspecialchars($email['error']);
		}
		if(!$url['success']) {
			$flash['error'][] = htmlspecialchars($url['error']);
		}
		if(!$author['success']) {
			$flash['error'][] = htmlspecialchars($author['error']);
		}
	}
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>
<script type="text/javascript" src="meta/js/manage-useroption.js"></script>
<div id="BP_page" class="page">
	<div class="inpage">
    	<?php
		if (!empty($flash)) {
			$msg = '<ul>';
			foreach(array_keys($flash) as $key => $msg_type) {
				foreach(array_keys($flash[$msg_type]) as $key_msg) {
					$msg .= '<li>'.$flash[$msg_type][$key_msg].'</li>';
				}
			}
			$msg .= '</ul>';
			echo '<div id="post_flash" class="flash_'.$msg_type.'" style="display:none;">'.$msg.'</div>';
		}
		?>
<fieldset>
	<legend><?php echo T_('Users Options');?></legend>
	<div class="message">
		<p><?php echo T_('Configuring user settings.');?></p>
	</div><br />

	<form method="POST">
		<?php echo T_('Contact Name');?><br />
		<input type="text" name="planet_author" size="60" class="input" value="<?php echo $planet_author; ?>" /><br /><br />

		<?php echo T_('Reference contact email');?><br />
		<input type="text" name="planet_author_mail" size="60" class="input" value="<?php echo $planet_author_mail; ?>" /><br /><br />

		<?php echo T_('Author Website');?><br />
		<input type="text" name="planet_author_site" class="input" size="60" value="<?php echo $planet_author_site; ?>" /><br /><br />

		<?php echo T_('Jabber / GoogleTalk');?><br />
		<input type="text" name="planet_author_jabber" class="input" size="60" value="<?php echo $planet_author_jabber; ?>" /><br /><br />

		<?php echo T_('Other Instant Messaging');?><br />
		<input type="text" name="planet_author_im" class="input" size="60" value="<?php echo $planet_author_im; ?>" /><br /><br />

		<?php echo T_('About Me');?><br />
		<textarea type="text" name="planet_author_about" class="cadre_about" rows="10" /><?php echo $planet_author_about; ?></textarea><br /><br />

		<div class="button"><input type="submit" class="valide" name="submit" value="<?php echo T_('Apply'); ?>"/></div>
	</form>
</fieldset>

<?php
include(dirname(__FILE__).'/footer.php');
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
