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
?><?php
/* ------------------------------------------------------------------------------------------- */
#  ClearBricks classes auto-loader
if (@is_dir('/usr/lib/clearbricks')) {
	define('CLEARBRICKS_PATH','/usr/lib/clearbricks');
} elseif (is_dir(dirname(__FILE__).'/clearbricks')) {
	define('CLEARBRICKS_PATH',dirname(__FILE__).'/clearbricks');
} elseif (isset($_SERVER['CLEARBRICKS_PATH']) && is_dir($_SERVER['CLEARBRICKS_PATH'])) {
	define('CLEARBRICKS_PATH',$_SERVER['CLEARBRICKS_PATH']);
}

if (!defined('CLEARBRICKS_PATH') || !is_dir(CLEARBRICKS_PATH)) {
	exit('No clearbricks path defined');
}

require CLEARBRICKS_PATH.'/_common.php';
$__autoload['bpCore']				= dirname(__FILE__).'/core/class.bp.core.php';
$__autoload['bpAuth']				= dirname(__FILE__).'/core/class.bp.auth.php';
$__autoload['dcError']				= dirname(__FILE__).'/core/class.dc.error.php';
$__autoload['dcModules']			= dirname(__FILE__).'/core/class.dc.modules.php';
$__autoload['bpSettings']			= dirname(__FILE__).'/core/class.bp.settings.php';
$__autoload['dcRestServer']			= dirname(__FILE__).'/core/class.dc.rest.php';

require_once(dirname(__FILE__).'/lib/gettext/gettext.inc');
require_once(dirname(__FILE__).'/fonctions.php');

// Import Hyla Tpl lib
#require_once dirname(__FILE__).'/lib/hyla_tpl/hyla_tpl.class.php';
$__autoload['Hyla_Tpl']				= dirname(__FILE__).'/lib/hyla_tpl/hyla_tpl.class.php';

mb_internal_encoding('UTF-8');

# Setting default timezone
dt::setTZ('UTC');

# Default locale value
$locale = '';

if (isset($_SERVER['BP_CONFIG_PATH'])) {
	define('BP_CONFIG_PATH',$_SERVER['BP_CONFIG_PATH']);
} elseif (isset($_SERVER['REDIRECT_BP_CONFIG_PATH'])) {
	define('BP_CONFIG_PATH',$_SERVER['REDIRECT_BP_CONFIG_PATH']);
} elseif (!defined('BP_CONFIG_PATH')) {
	define('BP_CONFIG_PATH',dirname(__FILE__).'/config.php');
}

if (!is_file(BP_CONFIG_PATH))
{
	$install_path = "admin/install/";
	$length = strlen($install_path);
	if (substr($_SERVER['REQUEST_URI'], -$length) !== $install_path) {

		$root_url = preg_replace('%\?.*%','',$_SERVER['REQUEST_URI']);
		$url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$root_url.$install_path;
//		print $url;
		header("Location: ".$url,TRUE,302);
	} else {
		print T_('Impossible to detect configuration file : ').BP_CONFIG_PATH;
	}
	exit();
}

require BP_CONFIG_PATH;
if (BP_DBENCRYPTED_PASSWORD == 1) {
	$dbpassword = base64_decode(BP_DBPASSWORD);
}
else {
	$dbpassword = BP_DBPASSWORD;
}

try {
	$core = new bpCore(BP_DBDRIVER,BP_DBHOST,BP_DBNAME,BP_DBUSER,$dbpassword,BP_DBPREFIX,BP_DBPERSIST);
} catch (Exception $e) {
	__error($e->getMessage()
		,$e->getCode() == 0 ?
		'<p>This either means that the username and password information in '.
		'your <strong>config.php</strong> file is incorrect or we can\'t contact '.
		'the database server at "<em>'.BP_DBHOST.'</em>". This could mean your '.
		'host\'s database server is down.</p> '.
		'<ul><li>Are you sure you have the correct username and password?</li>'.
		'<li>Are you sure that you have typed the correct hostname?</li>'.
		'<li>Are you sure that the database server is running?</li></ul>'.
		'<p>If you\'re unsure what these terms mean you should probably contact '.
		'your host. If you still need help you can always visit the '.
		'<a href="http://www.bilboplanet.com/forum/">Bilboplanet Support Forums</a>.</p>'
		: ''
		,20);
}

if (!isset($locale)) {
	$locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	if (!isset($locale)) {
		$locale = 'en';
	}
}

# Check for SQL injection attacks
if (isset($_GET)) {
	foreach ($_GET as $k => $v) {
		if (preg_match('[\']', $v)) {
			print "Bad request ! Please contact site administrator.";
			exit;
		} else {
			$_GET[$k] = $core->con->escape($v);
		}
	}
}

# Check if setting table exist
$schema = dbSchema::init($core->con);
if (in_array($core->prefix.'setting', $schema->getTables())) {

	$blog_settings = new bpSettings($core, 'root');
	$user_settings = null;

	/* Set backward compatibility */
	if (!defined('BP_PLANET_URL')) {
		define('BP_PLANET_URL',$blog_settings->get('planet_url'));
	}

	# Set timezone
	$timezone_default = $blog_settings->get('planet_timezone');
	if (!empty($timezone_default))
		dt::setTZ($timezone_default);
		#date_default_timezone_set($timezone_default);

	# Set Locale
	$locale = $blog_settings->get('planet_lang');

	# Set log level
	$log = $blog_settings->get('planet_log');

	# Add the global values needed in template
	if (is_file(dirname(__FILE__).'/../themes/'.$blog_settings->get('planet_theme').'/index.tpl')) {
		$core->tpl->importFile('index','index.tpl', dirname(__FILE__).'/../themes/'.$blog_settings->get('planet_theme'));
		$core->tpl->setVar('planet', array(
			"url"	=>	BP_PLANET_URL,
			"theme"	=>	$blog_settings->get('planet_theme'),
			"title"	=>	$blog_settings->get('planet_title'),
			"desc"	=>	$blog_settings->get('planet_desc'),
			"keywords"	=>	$blog_settings->get('planet_keywords'),
			"desc_meta"	=>	$blog_settings->get('planet_desc_meta'),
			"msg_info" => $blog_settings->get('planet_msg_info'),
		));
	} else {
		print sprintf(T_("ERROR : The theme '%s' that is configured is not existing in the theme folder."), $blog_settings->get('planet_theme'));
		exit;
	}


	if ($core->auth->sessionExists()) {
		# If we have a session we launch it now
		try {
			if (!$core->auth->checkSession())
			{
				# Avoid loop caused by old cookie
				$p = $core->session->getCookieParameters(false,-600);
				$p[3] = '/';
				call_user_func_array('setcookie',$p);

				http::redirect(BP_PLANET_URL.'/auth.php');
				//http::redirect('auth.php');
			} else {
				$user_settings = new bpSettings($core, $core->auth->userID());
				$rs_lang = $core->con->select(
					"SELECT user_lang FROM ".$core->prefix."user
					WHERE user_id='".$core->auth->userID()."'");
				$locale = $rs_lang->f('user_lang');
			}
		} catch (Exception $e) {
			__error(T_('Database error')
				,T_('There seems to be no Session table in your database. Is Bilboplanet completly installed?')
				,20);
		}
	}

	# Logout
	if (isset($_GET['logout'])) {
		$core->session->destroy();
		if (isset($_COOKIE['bp_admin'])) {
			unset($_COOKIE['bp_admin']);
			setcookie('bp_admin',false,-600,'','');
		}
		if (!empty($_GET['logout'])) {
			http::redirect($_GET['logout']);
		}
		else {
			http::redirect(BP_PLANET_URL);
		}
		exit;
	}
}



# Definition of the language
$textdomain="bilbo";
if (isset($_GET['locale']) && !empty($_GET['locale']))
	$locale = $_GET['locale'];
putenv('LANGUAGE='.$locale);
#putenv('LANG='.$locale);
putenv('LC_ALL='.$locale);
putenv('LC_MESSAGES='.$locale);
#T_setlocale('LANGUAGE='.$locale);
T_setlocale(LC_ALL, $locale);
T_setlocale(LC_CTYPE, $locale);

$locales_dir = dirname(__FILE__).'/../i18n';
T_bindtextdomain($textdomain, $locales_dir);
T_bind_textdomain_codeset($textdomain, 'UTF-8');
T_textdomain($textdomain);

function __error($summary,$message,$code=0)
{
	# Error codes
	# 10 : no config file
	# 20 : database issue
	# 30 : blog is not defined
	# 40 : template files creation
	# 50 : no default theme
	# 60 : template processing error

	if (defined('BP_ERRORFILE') && is_file(BP_ERRORFILE)) {
		include BP_ERRORFILE;
	} else {
		include dirname(__FILE__).'/core_error.php';
	}
	exit;
}
?>
