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

$rc_path = dirname(__FILE__).'/../../inc/config.php';

if (!is_file($rc_path)) {
	require_once(dirname(__FILE__).'/i18n.php');
	$summary = T_('No configuration file');
	$message = T_('There is no configuration file, you need to create one. Therefor you can use the wizard to generate a configuration file. Click "Next" to go to the setup page.');
	$message2 = '<a href="wizard.php"><div class="next">'.T_('Next').'</div></a>';
	$code = 0;
	include dirname(__FILE__).'/../../inc/core_error.php';
	exit;
}

require_once dirname(__FILE__).'/../../inc/prepend.php';
require_once dirname(__FILE__).'/check.php';

$can_install = true;
$err = '';

# Check if bilboplanet is already installed
$schema = dbSchema::init($core->con);
if (in_array($core->prefix.'feed',$schema->getTables())) {
	$can_install = false;
	$err = T_('The Bilboplanet is already installed.');
	$err .= "<br/>".sprintf(T_('If you are upgrading to a newer version of the Bilboplanet, please upgrade your database by running the %s/inc/upgrade_db.php script'), BP_PLANET_URL);
}

# Check system capabilites
if ($can_install && !dcSystemCheck($core->con,$_e)) {
	$can_install = false;
	$err = T_('The Bilboplanet can not be installed').'<ul><li>'.implode('</li><li>',$_e).'</li></ul>';
}

# Get information and perform install
$u_email = $u_fullname = $u_login = $u_pwd = '';
$mail_sent = false;
$subscription_content = htmlentities(stripslashes('<h2>Description</h2>

<p><br/>Le <a href="#"><strong>Nom du Planet</strong></a> est un planet visant à regrouper un ensemble de flux RSS de divers sites/blogs.
<br/><br/></p>

<h2>La charte de fonctionnement</h2>
<ul>
	<li>1. ...</li>
	<li>2. ...</li>
	<li><strong>3. ...</strong></li>
</ul>
<br/><br/></p>'), ENT_QUOTES, 'UTF-8');

if ($can_install && !empty($_POST))
{
	$u_email = !empty($_POST['u_email']) ? $_POST['u_email'] : null;
	$u_fullname = !empty($_POST['u_fullname']) ? $_POST['u_fullname'] : null;
	$u_login = !empty($_POST['u_login']) ? $_POST['u_login'] : null;
	$u_pwd = !empty($_POST['u_pwd']) ? $_POST['u_pwd'] : null;
	$u_pwd2 = !empty($_POST['u_pwd2']) ? $_POST['u_pwd2'] : null;
	$u_site = !empty($_POST['u_site']) ? $_POST['u_site'] : null;
	$p_desc = !empty($_POST['p_desc']) ? htmlentities(stripslashes($_POST['p_desc']), ENT_QUOTES, 'UTF-8') : null;
	$p_title = !empty($_POST['p_title']) ? htmlentities(stripslashes($_POST['p_title']), ENT_QUOTES, 'UTF-8') : null;
	$p_lang = !empty($_POST['p_lang']) ? $_POST['p_lang'] : null;
	$p_comm = !empty($_POST['p_comm']) ? true : false;
	define('BP_PROT_PATH',dirname(__FILE__).'/../../.htpasswd');

	# Version of the planet
	$version_file = dirname(__FILE__).'/VERSION';
	$fp = @fopen($version_file, 'rb');
	if ($fp === false) {
		throw new Exception(sprintf(T_('Unable to read %s file.'),$version_file));
	}
	$p_version = fread($fp, 10);
	fclose($fp);

	try
	{
		# Check user information
		if (empty($p_title)) {
			throw new Exception(T_('Please fill the title in.'));
		}
		if (empty($u_email)) {
			throw new Exception(T_('Please fill the user email in.'));
		}
		if (empty($u_login)) {
			throw new Exception(T_('Please fill the username in.'));
		}
		if(!preg_match('/^[0-9A-Za-z_\-]+$/',$u_login)) {
			throw new Exception(T_('Please do not use special chars in user_id field. Allowed chars are only a-z, A-Z and 0-9'));
		}

		if (!preg_match('/^[A-Za-z0-9@._-]{2,}$/',$u_login)) {
			throw new Exception(T_('The username has to be formed of minimum 2 characters with letters of numbers'));
		}
		if ($u_email && !text::isEmail($u_email)) {
			throw new Exception(T_('Invalid email address'));
		}
		if (empty($u_pwd)) {
			throw new Exception(T_('No password given'));
		}
		if ($u_pwd != $u_pwd2) {
			throw new Exception(T_("The passwords don't match"));
		}
		if (strlen($u_pwd) < 6) {
			throw new Exception(T_('The password have to contain minimum 6 characters.'));
		}

		# Try to guess timezone
		$default_tz = 'Europe/Paris';
		if (!empty($_POST['u_date']) && function_exists('timezone_open'))
		{
			if (preg_match('/\((.+)\)$/',$_POST['u_date'],$_tz)) {
				$_tz = $_tz[1];
				$_tz = @timezone_open($_tz);
				if ($_tz instanceof DateTimeZone) {
					$_tz = @timezone_name_get($_tz);
					if ($_tz) {
						$default_tz = $_tz;
					}
				}
				unset($_tz);
			}
		}

		# Create schema
		$_s = new dbStruct($core->con,$core->prefix);
		require dirname(__FILE__).'/../../inc/dbschema/db-schema.php';

		$si = new dbStruct($core->con,$core->prefix);
		$changes = $si->synchronize($_s);

		# Create user
		$cur = $core->con->openCursor($core->prefix.'user');
		$cur->user_id = (string) $u_login;
		$cur->user_fullname = (string) $u_fullname;
		$cur->user_email = (string) $u_email;
		$cur->user_pwd = crypt::hmac('BP_MASTER_KEY',$u_pwd);
		$cur->user_token = generateUserToken($u_fullname,$u_email,$u_pwd);
		$cur->user_lang = $p_lang;
		$cur->created = array('NOW()');
		$cur->modified = array('NOW()');
		$cur->insert();

		if (!empty($u_site)) {
			# Get next ID
			$rs3 = $core->con->select(
				'SELECT MAX(site_id) '.
				'FROM '.$core->prefix.'site '
				);
			$next_site_id = (integer) $rs3->f(0) + 1;
			$cur = $core->con->openCursor($core->prefix.'site');
			$cur->site_id = $next_site_id;
			$cur->user_id = $u_login;
			$cur->site_name = 'Author site';
			$cur->site_url = $u_site;
			$cur->site_status = 1;
			$cur->created = array(' NOW() ');
			$cur->modified = array(' NOW() ');
			$cur->insert();
		}

		# Create main tribe
		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_id = 'root';
		$cur->user_id = 'root';
		$cur->tribe_name = (string) $p_title;
		$cur->visibility = 1;
		$cur->created = array('NOW()');
		$cur->modified = array('NOW()');
		$cur->insert();

		$core->setUserRole($u_login, 'god');

		$root_url = preg_replace('%/admin/install/index.php$%','',$_SERVER['REQUEST_URI']);
		$planet_url = http::getHost().$root_url;

		$blog_settings = new bpSettings($core,'root');

		$blog_settings->put('author', "$u_fullname", "string");
		$blog_settings->put('author_mail', $u_email, "string");
		$blog_settings->put('author_id', $u_login, "string");
		$blog_settings->put('author_site', $u_site, "string");
		$blog_settings->put('author_jabber', "Your Jabber", "string");
		$blog_settings->put('author_im', "MSN Messenger, Yahoo Messenger and Other", "string");
		$blog_settings->put('author_about', "About me", "string");
		$blog_settings->put('planet_title', $p_title, "string");
		$blog_settings->put('planet_url', $planet_url, "string");
		$blog_settings->put('planet_desc', $p_desc, "string");
		$blog_settings->put('planet_lang', $p_lang, "string");
		if($p_lang == "ar") {
			$blog_settings->put('planet_rtl', '1', "boolean");
		}
		$blog_settings->put('planet_version', $p_version, "string");
		$blog_settings->put('planet_log', 'notice', "string");
		$blog_settings->put('planet_theme', 'default', "string");
		$blog_settings->put('planet_msg_info', 'BilboPlanet - An Open Source RSS feed aggregator written in PHP.', "string");
		$blog_settings->put('planet_meta', 'BilboPlanet - An Open Source RSS feed aggregator written in PHP.', "string");
		$blog_settings->put('planet_keywords', 'feed, flux, rss, bilboplanet, cms, agr&eacute;gateur, aggregator, planet, app, open source, free, linux, xml, development, web, php, mysql', "string");
		$blog_settings->put('planet_contact_page', '1', "boolean");
		$blog_settings->put('planet_vote', '1', "boolean");
		$blog_settings->put('planet_votes_limit', '-5', "integer");
		$blog_settings->put('planet_votes_system', 'yes-warn', "string");
		$blog_settings->put('planet_nb_post', '10', "integer");
		$blog_settings->put('planet_nb_art_mob', '10', "integer");
		$blog_settings->put('planet_nb_art_flux', '20', "integer");
		$blog_settings->put('planet_avatar', '1', "boolean");
		$blog_settings->put('planet_index_update', '1', "boolean");
		$blog_settings->put('planet_moderation', '1', "boolean");
		$blog_settings->put('planet_subscription', '1', "boolean");
		$blog_settings->put('planet_subscription_content', $subscription_content, "string");
		$blog_settings->put('planet_subscription_accept', "Default text when accept subscription", "string");
		$blog_settings->put('planet_subscription_refuse', "Default text when refuse subscription", "string");
		$blog_settings->put('planet_mail_error','1', "boolean");
		$blog_settings->put('planet_homepage','portal', "string");
		$blog_settings->put('planet_share_count',json_encode(array('twitter','identica')), "string");

		# Advanced configuration
		$blog_settings->put('planet_timezone',$default_tz, "string");
		$blog_settings->put('planet_maint','0', "boolean");
		$blog_settings->put('auto_feed_disabling','0', "boolean");
		$blog_settings->put('internal_links','1', "boolean");
		$blog_settings->put('allow_feed_modification','1', "boolean");
		$blog_settings->put('allow_post_modification','1', "boolean");
		$blog_settings->put('allow_tagging_everything','1', "boolean");
		$blog_settings->put('accept_public_tagged_feed','0', "boolean");
		$blog_settings->put('accept_user_tagged_feed','1', "boolean");
		$blog_settings->put('allow_uncensored_feed','1', "boolean");
		$blog_settings->put('show_similar_posts','1', "boolean");
		$blog_settings->put('planet_shaarli','1', "boolean");
		$blog_settings->put('planet_joined_community', '0', "boolean");

		if ($p_comm) { // user enabled the checkbox
			joinBilboplanetCommunity($planet_url,$p_title,$p_desc,$u_fullname,$u_email);
		}

		# Create planet salt :
		$base_string = sha1(time().$u_fullname.$u_email.$u_pwd.'~'.microtime(TRUE).$default_tz);
		$salt = substr($base_string, rand(0, strlen($base_string)), 32);
		$blog_settings->put('planet_salt',$salt, "string");

		$step = 1;
	}
	catch (Exception $e)
	{
		$err = $e->getMessage();
	}
}

if (!isset($step)) {
	$step = 0;
}
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<meta name="ROBOTS" content="NOARCHIVE,NOINDEX,NOFOLLOW" />
	<meta name="GOOGLEBOT" content="NOSNIPPET" />
	<link rel="icon" type="image/ico" href="../../favicon.png" />
	<link rel="stylesheet" type="text/css" href="meta/css/install.css" media="all" />
	<title><?php echo T_('Installation of the Bilboplanet');?></title>
	<script type="text/javascript" src="../meta/js/jquery-1.4.2.min.js"></script>
</head>

<body>
<div id="header_ext">
	<div id="header">
		<div id="logo">
			<h1>Bilboplanet</h1>
		</div>
	</div>
</div>
<div id="content">
<?php
echo '<h2>'.T_('Installation of the Bilboplanet').'</h2>';

if (!empty($err)) {
	echo '<div class="error"><p><strong>'.T_('Errors:').'</strong>'.$err.'</p></div>';
}

if (!empty($_GET['wiz'])) {
	echo '<p class="message">'.T_('The configuration file was successfully created.').'</p>';
}

if ($can_install && $step == 0)
{
$lang_path = dirname(__FILE__)."/../../i18n/";
$dir_handle = @opendir($lang_path) or die("Unable to open $lang_path");
$lang_array = array();
$lang_array['en'] = 'en';
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess" && is_dir($lang_path.$file)){
		$mo_dir = $lang_path.$file.'/LC_MESSAGES/';
		if (is_dir($mo_dir) && is_file($mo_dir.'bilbo.mo')){
			$lang_array[$file] = $file;
		}
	}
}
closedir($dir_handle);

	echo
	'<h2>'.T_('Information on the BilboPlanet').'</h2>'.

	'<p>'.T_('Thank you for taking somes minutes to answer those questions to help to the configuration of the bilboplanet.').'</p>'.
	'<p><span class="red">*&nbsp;</span>'.T_('Fields marked with an asterisk are obligatory').'</p>'.

	'<form id="install-form" action="index.php" method="post">'.
	'<fieldset><legend><strong>'.T_('Information of the user').'</strong></legend>'.
	'<label>'.T_('Fullname').'<span class="red"> * </span>'.
	form::field('u_fullname',30,255,html::escapeHTML($u_fullname)).'</label>
	<span class="description">'.T_('Enter your fullname or the name you want to be displayed').'</span>'.
	'<label>'.T_('Email').'<span class="red"> * </span>'.
	form::field('u_email',30,255,html::escapeHTML($u_email)).'</label>
	<span class="description">'.T_('Enter your email address').'</span>'.
	'<label>'.T_('Website of the user').' '.
	form::field('u_site',30,255,html::escapeHTML($u_site)).'</label>
	<span class="description">'.T_('Entre address of your website or blog (optional)').'</span>'.
	'</fieldset><br/>'.

	'<fieldset><legend><strong>'.T_('Information on the Planet').'</strong></legend>'.
	'<label>'.T_('Title of the Planet').'<span class="red"> * </span>'.
	form::field('p_title',30,255,html_entity_decode(addslashes(html::escapeHTML($p_title)))).'</label>
	<span class="description">'.T_('Fill the title of your planet').'</span>'.
	'<label>'.T_('Description').' '.
	form::field('p_desc',30,255,html_entity_decode(addslashes(html::escapeHTML($p_desc)))).'</label>
	<span class="description">'.T_('Give a description of your planet').'</span>'.
	'<label>'.T_('Planet Language').' '.
	form::combo('p_lang',$lang_array, $p_lang).'</label>
	<span class="description">'.T_('Choose your langage').'</span>'.
	'<label>'.T_('Join the Bilboplanet community').' '.
	form::checkbox('p_comm',true,true).'</label>
	<span class="description">'.T_('This will notify the developpers of the Bilboplanet that you are using their work. Your planet will be added to the community and you will be informed when new important features will be released. You will also be able to ask for new features to the development team.').'</span>'.
	'</fieldset><br/>'.

	'<fieldset><legend><strong>'.T_('Administration username and password').'</strong></legend>'.
	'<label class="required" title="'.T_('Required field').'">'.T_('Username').'<span class="red"> * </span>'.
	form::field('u_login',30,32,html::escapeHTML($u_login)).'</label>
	<span class="description">'.T_('Enter your username').'</span>'.
	'<label class="required" title="'.T_('Required field').'">'.T_('Password').'<span class="red"> * </span>'.
	form::password('u_pwd',30,255).'</label>
	<span class="description">'.T_('Enter your password').'</span>'.
	'<label class="required" title="'.T_('Required field').'">'.T_('Confirm your password').'<span class="red"> * </span>'.
	form::password('u_pwd2',30,255).'</label>
	<span class="description">'.T_('Re-enter your password for verification').'</span>'.
	'</fieldset><br/>'.

	'<input class="save" type="submit" value="'.T_('Save').'" />'.
	'</form>';
}
elseif ($can_install && $step == 1)
{
	$bpPath = explode("admin/install/index.php",http::getSelfURI());
	$bpPath = $bpPath[0];
	$adminPath = explode("install/index.php",http::getSelfURI());
	$adminPath = $adminPath[0];

	echo
	'<p class="message"><strong>'.T_('Install succeeded').'</strong></p>'.

	'<p>'.T_('The Bilboplanet was successfully installed. Here you can find a resume of the configuration').'</p>'.

	'<h3>'.T_('Your account').'</h3>'.
	'<ul>'.
	'<li>'.T_('Username:').' <strong>'.html::escapeHTML($u_login).'</strong></li>'.
//	'<li>'.T_('Password:').' <strong>'.html::escapeHTML($u_pwd).'</strong></li>'.
	'</ul>'.

	'<h3>'.T_('Your Bilboplanet').'</h3>'.
	'<ul>'.
	'<li>'.T_('URL of the BilboPlanet:').' <a href="'.html::escapeHTML($bpPath).'">
		<strong>'.html::escapeHTML($bpPath).'</strong></a></li>'.
	'<li>'.T_('Administration interface:').' <a href="'.html::escapeHTML($adminPath).'">
		<strong>'.html::escapeHTML($adminPath).'</strong></a></li>'.
	'</ul>'.

	'<form action="'.html::escapeHTML($adminPath).'" method="post">'.
	'<p><input class="save" type="submit" value="'.T_('Go to the administration interface').'" />'.
	'</p>'.
	'</form>';
}
?>
</div>
</body>
</html>
