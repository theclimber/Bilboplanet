<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
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


/* MISE EN MARCHE DE LA MACHINE :
                                      ,_-=(!7(7/zs_.
                                   .='  ' .`/,/!(=)Zm.
                     .._,,._..  ,-`- `,\ ` -` -`\\7//WW.
                ,v=~/.-,-\- -!|V-s.)iT-|s|\-.'   `///mK%.
              v!`i!-.e]-g`bT/i(/[=.Z/m)K(YNYi..   /-]i44M.
            v`/,`|v]-DvLcfZ/eV/iDLN\D/ZK@%8W[Z..   `/d!Z8m
           //,c\(2(X/NYNY8]ZZ/bZd\()/\7WY%WKKW)   -'|(][%4.
         ,\\i\c(e)WX@WKKZKDKWMZ8(b5/ZK8]Z7%ffVM,   -.Y!bNMi
         /-iit5N)KWG%%8%%%%W8%ZWM(8YZvD)XN(@.  [   \]!/GXW[
        / ))G8\NMN%W%%%%%%%%%%8KK@WZKYK*ZG5KMi,-   vi[NZGM[
       i\!(44Y8K%8%%%**~YZYZ@%%%%%4KWZ/PKN)ZDZ7   c=//WZK%!
      ,\v\YtMZW8W%%f`,`.t/bNZZK%%W%%ZXb*K(K5DZ   -c\\/KM48
      -|c5PbM4DDW%f  v./c\[tMY8W%PMW%D@KW)Gbf   -/(=ZZKM8[
      2(N8YXWK85@K   -'c|K4/KKK%@  V%@@WD8e~  .//ct)8ZK%8`
      =)b%]Nd)@KM[  !'\cG!iWYK%%|   !M@KZf    -c\))ZDKW%`
      YYKWZGNM4/Pb  '-VscP4]b@W%     'Mf`   -L\///KM(%W!
      !KKW4ZK/W7)Z. '/cttbY)DKW%     -`  .',\v)K(5KW%%f
      'W)KWKZZg)Z2/,!/L(-DYYb54%  ,,`, -\-/v(((KK5WW%f
       \M4NDDKZZ(e!/\7vNTtZd)8\Mi!\-,-/i-v((tKNGN%W%%
       'M8M88(Zd))///((|D\tDY\\KK-`/-i(=)KtNNN@W%%%@%[
        !8%@KW5KKN4///s(\Pd!ROBY8/=2(/4ZdzKD%K%%%M8@%%
         '%%%W%dGNtPK(c\/2\[Z(ttNYZ2NZW8W8K%%%%YKM%M%%.
           *%%W%GW5@/%!e]_tZdY()v)ZXMZW%W%%%*5Y]K%ZK%8[
            '*%%%%8%8WK\)[/ZmZ/Zi]!/M%%%%@f\ \Y/NNMK%%!
              'VM%%%%W%WN5Z/Gt5/b)((cV@f`  - |cZbMKW%%|
                 'V*M%%%WZ/ZG\t5((+)L\'-,,/  -)X(NWW%%
                      `~`MZ/DZGNZG5(((\,    ,t\\Z)KW%@
                         'M8K%8GN8\5(5///]i!v\K)85W%%f
                           YWWKKKKWZ8G54X/GGMeK@WM8%@
                            !M8%8%48WG@KWYbW%WWW%%%@
                              VM%WKWK%8K%%8WWWW%%%@`
                                ~*%%%%%%W%%%%%%%@~
                                   ~*MM%%%%%%@f`
                                       '''''
C'EST BON, CA TOURNE*/

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
$__autoload['dcError']				= dirname(__FILE__).'/core/class.bp.error.php';
$__autoload['dcModules']			= dirname(__FILE__).'/core/class.bp.modules.php';
$__autoload['bpSettings']			= dirname(__FILE__).'/core/class.bp.settings.php';
$__autoload['bpTribes']				= dirname(__FILE__).'/core/class.bp.tribes.php';
$__autoload['dcRestServer']			= dirname(__FILE__).'/core/class.bp.rest.php';
$__autoload['bpPost']				= dirname(__FILE__).'/core/class.bp.post.php';
$__autoload['bpUser']				= dirname(__FILE__).'/core/class.bp.user.php';
$__autoload['bpObject']				= dirname(__FILE__).'/core/class.bp.object.php';
$__autoload['bpFeed']				= dirname(__FILE__).'/core/class.bp.feed.php';
$__autoload['bpSite']				= dirname(__FILE__).'/core/class.bp.site.php';

require_once(dirname(__FILE__).'/lib/gettext/gettext.inc');
require_once(dirname(__FILE__).'/fonctions.php');

// Import Hyla Tpl lib
$__autoload['Hyla_Tpl']				= dirname(__FILE__).'/lib/hyla_tpl/hyla_tpl.class.php';

# Import views
$__autoload['AbstractView']			= dirname(__FILE__).'/views/class.bp.abstractview.php';
$__autoload['TribeView']			= dirname(__FILE__).'/views/class.bp.tribeview.php';
$__autoload['PostView']				= dirname(__FILE__).'/views/class.bp.postview.php';
$__autoload['GenericView']			= dirname(__FILE__).'/views/class.bp.genericview.php';

$__autoload['AbstractController']	= dirname(__FILE__).'/controllers/class.bp.abstractcontroller.php';
$__autoload['TribeController']		= dirname(__FILE__).'/controllers/class.bp.tribecontroller.php';
$__autoload['PostController']		= dirname(__FILE__).'/controllers/class.bp.postcontroller.php';
$__autoload['UpdateController']		= dirname(__FILE__).'/controllers/class.bp.updatecontroller.php';

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
	$url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']."admin/install/";
	header("Location: ".$url,TRUE,302);
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

# Check if setting table exist
$schema = dbSchema::init($core->con);
if (in_array($core->prefix.'setting', $schema->getTables())) {

	$blog_settings = new bpSettings($core->con, $core->prefix, 'root');

	# Set timezone
	$timezone_default = $blog_settings->get('planet_timezone');
	if (!empty($timezone_default))
		dt::setTZ($timezone_default);
		#date_default_timezone_set($timezone_default);

	# Set Locale
	$locale = $blog_settings->get('planet_lang');

	# Set log level
	$log = $blog_settings->get('planet_log');

	if ($core->auth->sessionExists()) {
		# If we have a session we launch it now
		try {
			if (!$core->auth->checkSession())
			{
				# Avoid loop caused by old cookie
				$p = $core->session->getCookieParameters(false,-600);
				$p[3] = '/';
				call_user_func_array('setcookie',$p);

				http::redirect($blog_settings->get('planet_url').'/auth.php');
			}
		} catch (Exception $e) {
			__error(T_('Database error')
				,T_('There seems to be no Session table in your database.
				 Is Bilboplanet completly installed?')
				,20);
		}

		# We can set the user to the tribe
		$core->tribes->setUser($core->auth->userID());
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
			http::redirect($blog_settings->get('planet_url'));
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
//T_setlocale(LANGUAGE,$locale);
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
