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
require_once(dirname(__FILE__).'/i18n.php');

if (isset($_SERVER['BP_CONFIG_PATH'])) {
	define('BP_CONFIG_PATH',$_SERVER['BP_CONFIG_PATH']);
} elseif (isset($_SERVER['REDIRECT_BP_CONFIG_PATH'])) {
	define('BP_CONFIG_PATH',$_SERVER['REDIRECT_BP_CONFIG_PATH']);
} else {
	define('BP_CONFIG_PATH',dirname(__FILE__).'/../../inc/config.php');
}

#  ClearBricks and Bilboplanet classes auto-loader
if (@is_dir('/usr/lib/clearbricks')) {
	define('CLEARBRICKS_PATH','/usr/lib/clearbricks');
} elseif (is_dir(dirname(__FILE__).'/../../inc/clearbricks')) {
	define('CLEARBRICKS_PATH',dirname(__FILE__).'/../../inc/clearbricks');
} elseif (isset($_SERVER['CLEARBRICKS_PATH']) && is_dir($_SERVER['CLEARBRICKS_PATH'])) {
	define('CLEARBRICKS_PATH',$_SERVER['CLEARBRICKS_PATH']);
}

if (!defined('CLEARBRICKS_PATH') || !is_dir(CLEARBRICKS_PATH)) {
	exit('No clearbricks path defined');
}

require CLEARBRICKS_PATH.'/_common.php';

$DBDRIVER = !empty($_POST['DBDRIVER']) ? $_POST['DBDRIVER'] : 'mysql';
$DBHOST = !empty($_POST['DBHOST']) ? $_POST['DBHOST'] : '';
$DBNAME = !empty($_POST['DBNAME']) ? $_POST['DBNAME'] : '';
$DBUSER = !empty($_POST['DBUSER']) ? $_POST['DBUSER'] : '';
$DBPASSWORD = !empty($_POST['DBPASSWORD']) ? base64_encode(stripslashes($_POST['DBPASSWORD'])): '';
$DBPREFIX = !empty($_POST['DBPREFIX']) ? $_POST['DBPREFIX'] : 'bp_';

if (!empty($_POST))
{
	try
	{
		# Tries to connect to database
		try {
			$con = dbLayer::init($DBDRIVER,$DBHOST,$DBNAME,$DBUSER,base64_decode($DBPASSWORD));
		} catch (Exception $e) {
			throw new Exception('<p>' . T_($e->getMessage()) . '</p>');
		}
		
		# Checks system capabilites
		require dirname(__FILE__).'/check.php';
		if (!dcSystemCheck($con,$_e)) {
			$can_install = false;
			throw new Exception('<p>'.T_('The Bilboplanet could not be installed.').'</p><ul><li>'.implode('</li><li>',$_e).'</li></ul>');
		}
		
		# Does config.php.default exist?
		$config_in = dirname(__FILE__).'/../../inc/config.php.default';
		if (!is_file($config_in)) {
			throw new Exception(sprintf(T_('File %s does not exist.'),$config_in));
		}

		# Can we write config.php
		if (!is_writable(dirname(BP_CONFIG_PATH))) {
			throw new Exception(sprintf(T_('Cannot write %s file.'),BP_CONFIG_PATH));
		}
		
		# Creates config.php file
		$root_url = preg_replace('%/admin/install/wizard.php$%','',$_SERVER['REQUEST_URI']);
		$planet_url = http::getHost().$root_url;

		$full_conf = file_get_contents($config_in);
		writeConfigValue('BP_DBHOST',$DBHOST,$full_conf);
		writeConfigValue('BP_DBUSER',$DBUSER,$full_conf);
		writeConfigValue('BP_DBPASSWORD',$DBPASSWORD,$full_conf);
		writeConfigValue('BP_DBNAME',$DBNAME,$full_conf);
		writeConfigValue('BP_DBPREFIX',strtolower($DBPREFIX),$full_conf);
		writeConfigValue('BP_DBENCRYPTED_PASSWORD','1',$full_conf);
		writeConfigValue('BP_PLANET_URL',$planet_url,$full_conf);

		$fp = @fopen(BP_CONFIG_PATH,'wb');
		if ($fp === false) {
			throw new Exception(sprintf(T_('Cannot write %s file.'),BP_CONFIG_PATH));
		}
		fwrite($fp,$full_conf);
		fclose($fp);
		chmod(BP_CONFIG_PATH, 0775);
		#chmod(BP_CONFIG_PATH, 0666);
		
		# Check if bilboplanet is already installed
		$schema = dbSchema::init($con);
		if (in_array($DBPREFIX.'feed',$schema->getTables())) {
			throw new Exception(sprintf(T_('The Bilboplanet is already installed. Please remove the tables in the database before.
				</br> If you want to keep the content of the database, you can also update the database by launching the updating script : %s'),
				'<a href="'.$planet_url.'/inc/upgrade_db.php">'.T_('Run script').'</a>'));
		}

		$con->close();
		http::redirect('index.php?wiz=1');
	}
	catch (Exception $e)
	{
		$err = $e->getMessage();
	}
}


function writeConfigValue($name,$val,&$str)
{
	$val = str_replace("'","\'",$val);
	$str = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1,\''.$val.'\');',$str);
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <meta http-equiv="Content-Language" content="en" />
  <meta name="MSSmartTagsPreventParsing" content="TRUE" />
  <meta name="ROBOTS" content="NOARCHIVE,NOINDEX,NOFOLLOW" />
  <link rel="icon" type="image/ico" href="../../favicon.png" />
  <meta name="GOOGLEBOT" content="NOSNIPPET" />
  <link rel="stylesheet" type="text/css" href="meta/css/install.css" media="all" />
  <title><?php echo T_('Bilboplanet Install Wizard');?></title>
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
echo
'<h2>'.T_('Installation wizard of the Bilboplanet').'</h2>';

if (!empty($err)) {
	echo '<div class="error"><strong>'.T_('Errors:').'</strong>'.$err.'</div>';
}

if (is_file(BP_CONFIG_PATH)) {
	exit(printf(T_('The file <strong>%s</strong> already exists. If you need to reinitialize on of the configuration elements in this file, please remove the configuration file first or <a href="%s">continue the installation</a>.'),
	basename(BP_CONFIG_PATH),'index.php'));
}

echo
'<p>'.T_('Welcome to the BilboPlanet. Before you begin, we need some informations concerning the database. You\'ll need to provide the following information to begin the installation and to create the configuration file.<br /><br />
   1. The host of the database.<br/>
   2. The name of the database.<br/>
   3. Your username to the database.<br/>
   4. Your password to the database.<br/>
   4. Table prefix for your database.<br/><br/>').'</p>'.


'<form action="wizard.php" method="post">'.

'<p><label class="required" title="'.T_('Required field').'">'.T_('Database type:').' '.
form::combo('DBDRIVER',array('MySQL'=>'mysql','PosqtgreSQL'=>'pgsql','SQLite'=>'sqlite'), '', 'input').'</label></p>'.
#form::combo('feed_status',$status,'', 'input','','').'</label><br /><br />';

'<label>'.T_('Host of the database').' '.
form::field('DBHOST',30,255,html::escapeHTML($DBHOST)).'</label>
<span class="description">'.T_('ex: localhost').'</span><p class="clear" />'.

'<label>'.T_('Name of the database:').' '.
form::field('DBNAME',30,255,html::escapeHTML($DBNAME)).'</label>
<span class="description">'.T_('ex: bilboplanet').'</span><p class="clear" />'.

'<label>'.T_('Username:').' '.
form::field('DBUSER',30,255,html::escapeHTML($DBUSER)).'</label>
<span class="description">'.T_('Your username depends of your provider').'</span><p class="clear" />'.

'<label>'.T_('Password:').' '.
form::password('DBPASSWORD',30,255).'</label>
<span class="description">'.T_('Your password depends of your provider').'</span><p class="clear" />'.

'<label class="required" title="'.T_('Required field').'">'.T_('Database Tables Prefix:').' '.
form::field('DBPREFIX',30,255,html::escapeHTML($DBPREFIX)).'</label>
<span class="description">'.T_('bp_ is the default prefix').'</span><p class="clear" />'.

'<input class="save" type="submit" value="'.T_('Save').'" />'.
'</form>';
?>
</div>
</body>
</html>
