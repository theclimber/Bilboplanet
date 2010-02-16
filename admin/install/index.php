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
require_once(dirname(__FILE__).'/i18n.php');

$rc_path = dirname(__FILE__).'/../../inc/config.php';

if (!is_file($rc_path)) {
	$summary = T_('No configuration file');
	$message = T_('There is no configuration file, you need to create one. Therefor you can use the wizard to generate a configuration file. Click "Next" to go to the setup page.');
	$message2 = '<a href="wizard.php"><div class="next">'.T_('Next').' &rarr;</div></a>';
	$code = 0;
	include dirname(__FILE__).'/../../inc/core_error.php';
	exit;
}

require dirname(__FILE__).'/../../inc/prepend.php';
require dirname(__FILE__).'/check.php';
require $rc_path;


$can_install = true;
$err = '';

# Check if bilboplanet is already installed
$schema = dbSchema::init($core->con);
if (in_array($core->prefix.'flux',$schema->getTables())) {
	$can_install = false;
	$err = T_('The Bilboplanet is already installed.');
}

# Check system capabilites
if (!dcSystemCheck($core->con,$_e)) {
	$can_install = false;
	$err = T_('The Bilboplanet can not be installed').'<ul><li>'.implode('</li><li>',$_e).'</li></ul>';
}

# Get information and perform install
$u_email = $u_firstname = $u_name = $u_login = $u_pwd = '';
$mail_sent = false;

function writeConfigValue($name,$val,&$str)
{
	$val = str_replace("'","\'",$val);
	$str = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1,\''.$val.'\');',$str);
}


if ($can_install && !empty($_POST))
{
	$u_email = !empty($_POST['u_email']) ? $_POST['u_email'] : null;
	$u_firstname = !empty($_POST['u_firstname']) ? $_POST['u_firstname'] : null;
	$u_name = !empty($_POST['u_name']) ? $_POST['u_name'] : null;
	$u_login = !empty($_POST['u_login']) ? $_POST['u_login'] : null;
	$u_pwd = !empty($_POST['u_pwd']) ? $_POST['u_pwd'] : null;
	$u_pwd2 = !empty($_POST['u_pwd2']) ? $_POST['u_pwd2'] : null;
	$u_site = !empty($_POST['u_site']) ? $_POST['u_site'] : null;
	$p_url = !empty($_POST['p_url']) ? $_POST['p_url'] : null;
	$p_desc = !empty($_POST['p_desc']) ? $_POST['p_desc'] : null;
	$p_title = !empty($_POST['p_title']) ? $_POST['p_title'] : null;
	$p_lang = !empty($_POST['p_lang']) ? $_POST['p_lang'] : null;
	define('BP_PROT_PATH',dirname(__FILE__).'/../../.htpasswd');
	
	try
	{
		# Check user information
		if (empty($u_login)) {
			throw new Exception(T_('Please fill the username in.'));
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

		# Finish configuring config.php
		$config_in = dirname(__FILE__).'/../../inc/config.php';
		$full_conf = file_get_contents($config_in);

		writeConfigValue('BP_AUTHOR',"$u_firstname $u_name",$full_conf);
		writeConfigValue('BP_AUTHOR_MAIL',$u_email,$full_conf);
		writeConfigValue('BP_AUTHOR_SITE',$u_site,$full_conf);
		writeConfigValue('BP_USER',$u_login,$full_conf);
		writeConfigValue('BP_PWD',md5(trim($u_pwd)),$full_conf);
		writeConfigValue('BP_TITLE',$p_title,$full_conf);
		writeConfigValue('BP_URL',$p_url,$full_conf);
		writeConfigValue('BP_DESC',$p_desc,$full_conf);
		writeConfigValue('BP_LANG',$p_lang,$full_conf);

		$fp = @fopen($config_in,'wb');
		if ($fp === false) {
			throw new Exception(sprintf(T_('Unable to write %s file.'),$config_in));
		}
		fwrite($fp,$full_conf);
		fclose($fp);
		chmod($config_in, 0775);


		# Does .protected exist?
		$protected = BP_PROT_PATH;
		if (is_file($protected)) {
			throw new Exception(sprintf(T_('File %s already exists.'),$protected));
		}
		
		# Can we write .protected
		if (!is_writable(dirname(BP_PROT_PATH))) {
			throw new Exception(sprintf(T_('Unable to write %s file.'),BP_PROT_PATH));
		}
		
		# Creates .protected file
		$user_login = (string) $u_login;
		$user_passwd = crypt(trim($u_pwd),base64_encode(CRYPT_STD_DES));
		
		$string = $user_login.':'.$user_passwd ;

		$string2 = "AuthUserFile ".BP_PROT_PATH."
AuthGroupFile /dev/null
AuthName \"Restricted Area...\"
AuthType Basic
<limit GET POST>
require valid-user
</Limit>";

		# Create the .protected file
		$fp = @fopen(BP_PROT_PATH,'wb');
		if ($fp === false) {
			throw new Exception(sprintf(T_('Unable to write %s file.'),BP_PROT_PATH));
		}
		fwrite($fp,$string);
		fclose($fp);
		chmod(BP_PROT_PATH, 0666);

		# Create the .htaccess file
		$access = dirname(__FILE__).'/../../admin/.htaccess';
		$fp = @fopen($access,'wb');
		if ($fp === false) {
			unlink(BP_PROT_PATH);
			throw new Exception(sprintf(T_('Unable to write %s file.'),$access));
		}
		fwrite($fp,$string2);
		fclose($fp);
		chmod($access, 0666);

		//http::redirect('index.php?wiz=1');

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
		createTables(BP_DBHOST,BP_DBNAME,BP_DBUSER,BP_DBPASSWORD);


		$step = 1;
	}
	catch (Exception $e)
	{
		$err = $e->getMessage();
	}
}
function createTables($host,$name,$user,$pass)
{
  # Connexion au serveur MySQL
  mysql_connect($host, $user, $pass) or die("Connexion error to the MySQL server !");

  # Selection de la base
  mysql_select_db($name) or die("Connexion error to the database -> $name");

  # On insert une nouvelle entree
  $create_table1 = "CREATE TABLE IF NOT EXISTS `article` (
  `num_article` int(10) NOT NULL auto_increment,
  `num_membre` varchar(32) character set utf8 collate utf8_bin NOT NULL default '',
  `article_pub` int(15) default NULL,
  `article_titre` varchar(255) default NULL,
  `article_url` varchar(255) default NULL,
  `article_content` longtext,
  `article_statut` int(1) NOT NULL default '1',
  `article_score` int(20) NOT NULL default '0',
  FULLTEXT (`article_titre`, `article_content`),
  PRIMARY KEY  (`num_article`)
);";
  $create_table2 = "CREATE TABLE IF NOT EXISTS `flux` (
  `num_flux` int(5) NOT NULL auto_increment,
  `url_flux` char(255) default '',
  `num_membre` int(5) NOT NULL,
  `last_updated` int(11) NOT NULL default '0',
  `status_flux` int(1) NOT NULL default '1',
  PRIMARY KEY  (`num_flux`,`num_membre`)
);";
  $create_table3 = "CREATE TABLE IF NOT EXISTS `membre` (
  `num_membre` int(5) NOT NULL auto_increment,
  `nom_membre` char(50) default '',
  `email_membre` char(50) NOT NULL default '',
  `site_membre` char(255) default '',
  `statut_membre` int(1) default NULL,
  PRIMARY KEY  (`num_membre`)
);";
  $create_table4 = "CREATE TABLE IF NOT EXISTS `votes` (
  `num_article` int(10) NOT NULL auto_increment,
  `vote_ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`num_article`,`vote_ip`)
);";
  $result = mysql_query($create_table1) or die("Error with request $create_table1");
  $result = mysql_query($create_table2) or die("Error with request $create_table2");
  $result = mysql_query($create_table3) or die("Error with request $create_table3");
  $result = mysql_query($create_table4) or die("Error with request $create_table4");

  # Femeture de la base
  mysql_close();
}

if (!isset($step)) {
	$step = 0;
}
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
xml:lang="en" lang="en">
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
  <script type="text/javascript" src="../js/jquery/jquery.js"></script>
  <script type="text/javascript">
  //<![CDATA[
  $(function() {
    var login_re = new RegExp('[^A-Za-z0-9@._-]+','g');
    $('#u_firstname').keyup(function() {
      var login = this.value.toLowerCase().replace(login_re,'').substring(0,32);
	 $('#u_login').val(login);
    });
    $('#u_login').keyup(function() {
      $(this).val(this.value.replace(login_re,''));
    });
    
    $('#u_login').parent().after($('<input type="hidden" name="u_date" value="' + Date().toLocaleString() + '" />'));
  });
  //]]>
  </script>
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

/*if (!is_writable(DC_TPL_CACHE)) {
	echo '<div class="error"><p>'.sprintf(T_('Cache directory %s is not writable.'),DC_TPL_CACHE).'</p></div>';
}*/

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
$p_lang = array();
$p_lang['en'] = 'en';
while ($file = readdir($dir_handle)){
	if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess" && is_dir($lang_path.$file)){
		$mo_dir = $lang_path.$file.'/LC_MESSAGES/';
		if (is_dir($mo_dir) && is_file($mo_dir.'bilbo.mo')){
			$p_lang[$file] = $file;
		}
	}
}
closedir($dir_handle);

	echo
	'<h2>'.T_('Information on the BilboPlanet').'</h2>'.
	
	'<p>'.T_('Thank you for taking somes minutes to answer those questions to help to the configuration of the bilboplanet').'</p>'.
	
	'<form id="install-form" action="index.php" method="post">'.
	'<fieldset><legend><strong>'.T_('Information of the user').'</strong></legend>'.
	'<label>'.T_('Firstname').' '.
	form::field('u_firstname',30,255,html::escapeHTML($u_firstname)).'</label>
	<span class="description">'.T_('Enter your firstname or nickname').'</span>'.
	'<label>'.T_('Name').' '.
	form::field('u_name',30,255,html::escapeHTML($u_name)).'</label>
	<span class="description">'.T_('Enter your name (optional)').'</span>'.
	'<label>'.T_('Email').' '.
	form::field('u_email',30,255,html::escapeHTML($u_email)).'</label>
	<span class="description">'.T_('Enter your email address').'</span>'.
	'<label>'.T_('Website of the user').' '.
	form::field('u_site',30,255,html::escapeHTML($u_site)).'</label>
	<span class="description">'.T_('Entre address of your website or blog (optional)').'</span>'.
	'</fieldset><br/>'.

	'<fieldset><legend><strong>'.T_('Information on the Planet').'</strong></legend>'.
	'<label>'.T_('URL of the Planet').' '.
	form::field('p_url',30,255,html::escapeHTML($p_url)).'</label>
	<span class="description">'.T_('ex: http://www.example.com or http://planet.example.com').'</span>'.
	'<label>'.T_('Title of the Planet').' '.
	form::field('p_title',30,255,html::escapeHTML($p_title)).'</label>
	<span class="description">'.T_('Fill the title of your planet').'</span>'.
	'<label>'.T_('Description').' '.
	form::field('p_desc',30,255,html::escapeHTML($p_desc)).'</label>
	<span class="description">'.T_('Give a description of your planet').'</span>'.
	'<label>'.T_('Planet Language').' '.
	form::combo('p_lang',$p_lang).'</label>
	<span class="description">'.T_('Choose your langage').'</span>'.
	'</fieldset><br/>'.
	
	'<fieldset><legend><strong>'.T_('Administration username and password').'</strong></legend>'.
	'<label class="required" title="'.T_('Required field').'">'.T_('Username').' '.
	form::field('u_login',30,32,html::escapeHTML($u_login)).'</label>
	<span class="description"'.T_('Enter your username').'</span>'.
	'<label class="required" title="'.T_('Required field').'">'.T_('Password').' '.
	form::password('u_pwd',30,255).'</label>
	<span class="description"'.T_('Enter your password').'</span>'.
	'<label class="required" title="'.T_('Required field').'">'.T_('Confirm your password').' '.
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
	'<li>'.T_('Password:').' <strong>'.html::escapeHTML($u_pwd).'</strong></li>'.
	'</ul>'.
	
	'<h3>'.T_('Your Bilboplanet').'</h3>'.
	'<ul>'.
	'<li>'.T_('URL of the BilboPlanet:').' <strong>'.html::escapeHTML($bpPath).'</strong></li>'.
	'<li>'.T_('Administration interface:').' <strong>'.html::escapeHTML($adminPath).'</strong></li>'.
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
