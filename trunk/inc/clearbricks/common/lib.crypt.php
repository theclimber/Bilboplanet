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
* Functions to handle passwords (hash, random password generator...)
*
* @package Clearbricks
*/
class crypt
{
	/**
	* Returns an HMAC encoded value of <var>$data</var>, using the said <var>$key</var>
	* and <var>$hashfunc</var> as hash method (sha1 or md5 are accepted.)
	*
	* @param	string	$key			Hash key
	* @param	string	$data		Data
	* @param	string	$hashfunc		Hash function (md5 or sha1)
	* @return string
	*/
	public static function hmac($key,$data,$hashfunc='sha1')
	{
		$blocksize=64;
		if ($hashfunc != 'sha1') {
			$hashfunc = 'md5';
		}
		
		if (strlen($key)>$blocksize) {
			$key=pack('H*', $hashfunc($key));
		}
		
		$key=str_pad($key,$blocksize,chr(0x00));
		$ipad=str_repeat(chr(0x36),$blocksize);
		$opad=str_repeat(chr(0x5c),$blocksize);
		$hmac = pack('H*',$hashfunc(($key^$opad).pack('H*',$hashfunc(($key^$ipad).$data))));
		return bin2hex($hmac);
	}
	
	/**
	* Returns an 8 characters random password.
	*
	* @return	string
	*/
	public static function createPassword()
	{
		$pwd = array();
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$chars2 = '$!@';
		
		foreach (range(0,8) as $i) {
			$pwd[] = $chars[rand(0,strlen($chars)-1)];
		}
		
		$pos1 = array_rand(array(0,1,2,3));
		$pos2 = array_rand(array(4,5,6,7));
		$pwd[$pos1] = $chars2[rand(0,strlen($chars2)-1)];
		$pwd[$pos2] = $chars2[rand(0,strlen($chars2)-1)];
		
		return implode('',$pwd);
	}
}
?>