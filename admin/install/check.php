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

function dcSystemCheck(&$con,&$err)
{
	$err = array();
	
	if (version_compare(phpversion(),'5.0','<')) {
		$err[] = sprintf(T_('PHP version is %s (5.0 or earlier needed).'),phpversion());
	}
	
	if (!function_exists('mb_detect_encoding')) {
		$err[] = T_('Multibyte string module (mbstring) is not available.');
	}
	
	if (!function_exists('iconv')) {
		$err[] = T_('Iconv module is not available.');
	}
	
	if (!function_exists('ob_start')) {
		$err[] = T_('Output control functions are not available.');
	}
	
	if (!function_exists('simplexml_load_string')) {
		$err[] = T_('SimpleXML module is not available.');
	}
	
	if (!function_exists('dom_import_simplexml')) {
		$err[] = T_('DOM XML module is not available.');
	}
	
	$pcre_str = base64_decode('w6nDqMOgw6o=');
	if (!@preg_match('/'.$pcre_str.'/u', $pcre_str)) {
		$err[] = T_('PCRE engine does not support UTF-8 strings.');
	}
	
	if (!function_exists("spl_classes")) {
		$err[] = T_('SPL module is not available.');
	}
	
	if ($con->driver() == 'mysql')
	{
		if (version_compare($con->version(),'4.1','<'))
		{
			$err[] = sprintf(T_('MySQL version is %s (4.1 or earlier needed).'),$con->version());
		}
		else
		{
			$rs = $con->select('SHOW ENGINES');
			$innodb = false;
			while ($rs->fetch()) {
				if (strtolower($rs->f(0)) == 'innodb' && strtolower($rs->f(1)) != 'disabled' && strtolower($rs->f(1)) != 'no') {
					$innodb = true;
					break;
				}
			}
			
			#if (!$innodb) {
			#	$err[] = T_('MySQL InnoDB engine is not available.');
			#}
		}
	}
	elseif ($con->driver() == 'pgsql')
	{
		if (version_compare($con->version(),'8.0','<'))
		{
			$err[] = sprintf(T_('PostgreSQL version is %s (8.0 or earlier needed).'),$con->version());
		}
	}
	
	return count($err) == 0;
}
?>
