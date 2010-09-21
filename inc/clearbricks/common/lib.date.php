<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2006 Olivier Meunier and contributors. All rights
# reserved.
#
# Clearbricks is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Clearbricks is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Clearbricks; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

/**
* Date/time utilities
*
* @package Clearbricks
*/
class dt
{
	/**
	* Format a timestamp like PHP strftime function
	*
	* @param string	$p		Format pattern
	* @param integer	$ts		Timestamp
	* @param string	$tz		Timezone
	* @return	string
	*/
	public static function str($p,$ts=null,$tz=null)
	{
		if ($ts === null) { $ts = time(); }
		
		$hash = '799b4e471dc78154865706469d23d512';
		$p = preg_replace('/(?<!%)%(a|A)/','{{'.$hash.'__$1%w__}}',$p);
		$p = preg_replace('/(?<!%)%(b|B)/','{{'.$hash.'__$1%m__}}',$p);
		
		if ($tz) {
			$T = self::getTZ();
			self::setTZ($tz);
		}
		
		$res = strftime($p,$ts);
		
		if ($tz) {
			self::setTZ($T);
		}
		
		$res = preg_replace_callback('/{{'.$hash.'__(a|A|b|B)([0-9]{1,2})__}}/',array('self','_callback'),$res);
		
		return $res;
	}
	
	/**
	* Format a literal date to another literal date
	*
	* @param string	$p		Format pattern
	* @param string	$dt		Date
	* @param string	$tz		Timezone
	* @return	string
	*/
	public static function dt2str($p,$dt,$tz=null)
	{
		return dt::str($p,strtotime($dt),$tz);
	}
	
	/**
	* Format a timestamp to ISO-8601 format
	*
	* @param integer	$ts		Timestamp
	* @param string	$tz		Timezone
	* @return	string
	*/
	public static function iso8601($ts,$tz='UTC')
	{
		$o = self::getTimeOffset($tz,$ts);
		$of = sprintf('%02u:%02u',abs($o)/3600,(abs($o)%3600)/60);
		return date('Y-m-d\\TH:i:s',$ts).($o < 0 ? '-' : '+').$of;
	}
	
	/**
	* Format a timestamp to RFC-822 format
	*
	* @param integer	$ts		Timestamp
	* @param string	$tz		Timezone
	* @return	string
	*/
	public static function rfc822($ts,$tz='UTC')
	{
		# Get offset
		$o = self::getTimeOffset($tz,$ts);
		$of = sprintf('%02u%02u',abs($o)/3600,(abs($o)%3600)/60);
		return strftime('%a, %d %b %Y %H:%M:%S '.($o < 0 ? '-' : '+').$of,$ts);
	}
	
	/**
	* Set timezone during script execution
	*
	* @param	string	$tz		Timezone
	*/
	public static function setTZ($tz)
	{
		if (function_exists('date_default_timezone_set')) {
			date_default_timezone_set($tz);
			return;
		}
		
		if (!ini_get('safe_mode')) {
			putenv('TZ='.$tz);
		}
	}
	
	/**
	* Get current timezone
	*
	* @return string
	*/
	public static function getTZ()
	{
		if (function_exists('date_default_timezone_get')) {
			return date_default_timezone_get();
		}

		return date('T');
	}
	
	/**
	* Get time offset for a timezone and an optionnal $ts timestamp
	*
	* @param string	$tz		Timezone
	* @param integer	$ts		Timestamp
	* @return integer
	*/
	public static function getTimeOffset($tz,$ts=false)
	{
		if (!$ts) {
			$ts = time();
		}
		
		$server_tz = self::getTZ();
		$server_offset = date('Z',$ts);
		
		self::setTZ($tz);
		$cur_offset = date('Z',$ts);
		
		self::setTZ($server_tz);
		
		return $cur_offset-$server_offset;
	}
	
	/**
	* Returns any timestamp from current timezone to UTC timestamp.
	*
	* @param integer	$ts		Timestamp
	* @return integer
	*/
	public static function toUTC($ts)
	{
		return $ts + self::getTimeOffset('UTC',$ts);
	}
	
	/**
	* Returns a timestamp with its timezone offset.
	*
	* @param string	$tz		Timezone
	* @param integer	$ts		Timestamp
	* @return integer
	*/
	public static function addTimeZone($tz,$ts=false)
	{
		if (!$ts) {
			$ts = time();
		}
		
		return $ts + self::getTimeOffset($tz,$ts);
	}
	
	/**
	* Returns an array of supported timezones, codes are keys and names are values.
	*
	* @param boolean	$flip	Names are keys and codes are values
	* @param boolean	$groups	Return timezones in arrays of continents
	* @return array
	*/
	public static function getZones($flip=false,$groups=false)
	{
		if (!is_readable($f = dirname(__FILE__).'/tz.dat')) {
			return array();
		}
		
		$tz =  file(dirname(__FILE__).'/tz.dat');
		
		$res = array();
		
		foreach ($tz as $v)
		{
			$v = trim($v);
			if ($v) {
				$res[$v] = str_replace('_',' ',$v);
			}
		}
		
		if ($flip) {
			$res = array_flip($res);
			if ($groups) {
				$tmp = array();
				foreach ($res as $k => $v) {
					$g = explode('/',$k);
					$tmp[$g[0]][$k] = $v;
				}
				$res = $tmp;
			}
		}
		
		return $res;
	}
	
	private static function _callback($args)
	{
		$b = array(1=>'_Jan',2=>'_Feb',3=>'_Mar',4=>'_Apr',5=>'_May',6=>'_Jun',
		7=>'_Jul',8=>'_Aug',9=>'_Sep',10=>'_Oct',11=>'_Nov',12=>'_Dec');
		
		$B = array(1=>'January',2=>'February',3=>'March',4=>'April',
		5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',
		10=>'October',11=>'November',12=>'December');
		
		$a = array(1=>'_Mon',2=>'_Tue',3=>'_Wed',4=>'_Thu',5=>'_Fri',
		6=>'_Sat',0=>'_Sun');
		
		$A = array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',
		5=>'Friday',6=>'Saturday',0=>'Sunday');
		
		return __(${$args[1]}[(integer) $args[2]]);
	}
}
?>