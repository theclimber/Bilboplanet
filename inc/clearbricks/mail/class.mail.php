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

class mail
{
	public static function sendMail($to,$subject,$message,$headers=null,$p=null)
	{
		$f = function_exists('_mail') ? '_mail' : null;
		$eol = trim(ini_get('sendmail_path')) ? "\n" : "\r\n";
		
		if (is_array($headers)) {
			$headers = implode($eol,$headers);
		}
		
		if ($f == null)
		{
			if (!@mail($to,$subject,$message,$headers,$p)) {
				throw new Exception('Unable to send email');
			}
		}
		else
		{
			call_user_func($f,$to,$subject,$message,$headers,$p);
		}
		
		return true;
	}
	
	public static function getMX($host)
	{
		if (!getmxrr($host,$mx_h,$mx_w) || count($mx_h) == 0) {
			return false;
		}
		
		$res = array();
		
		for ($i=0; $i<count($mx_h); $i++) {
			$res[$mx_h[$i]] = $mx_w[$i];
		}
		
		asort($res);
		
		return $res;
	}
	
	/**
	@function QPHeader
	
	Encodes given string as a quoted printable mail header.
	
	@param str	string	String to encode
	@return string
	*/
	public static function QPHeader($str,$charset='UTF-8')
	{
		if (!preg_match('/[^\x00-\x3C\x3E-\x7E]/',$str)) {
			return $str;
		}
		
		return '=?'.$charset.'?Q?'.text::QPEncode($str).'?=';
	}
	
	/**
	@function B64Header
	
	Encodes given string as a base64 mail header.
	
	@param str	string	String to encode
	@return string
	*/
	public static function B64Header($str,$charset='UTF-8')
	{
		if (!preg_match('/[^\x00-\x3C\x3E-\x7E]/',$str)) {
			return $str;
		}
		
		return '=?'.$charset.'?B?'.base64_encode($str).'?=';
	}
}
?>