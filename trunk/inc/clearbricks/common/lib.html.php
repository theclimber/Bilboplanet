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

class html
{
	public static $url_root;
	public static $absolute_regs = array();
	
	/**
	@function escapeHTML
	
	Replaces HTML special characters by entities.
	
	@param	str	string	String to escape
	@return	string
	*/
	public static function escapeHTML($str)
	{
		return htmlspecialchars($str,ENT_COMPAT,'UTF-8');
	}
	
	/**
	@function decodeEntities
	
	Returns a string with all entities decoded.
	
	@param	str	string	String to protect
	@param	keep_special string		Keep special characters: &gt; &lt; &amp;
	@return	string
	*/
	public static function decodeEntities($str,$keep_special=false)
	{
		if ($keep_special) {
			$str = str_replace(
				array('&amp;','&gt;','&lt;'),
				array('&amp;amp;','&amp;gt;','&amp;lt;'),
				$str);
		}
		
		# Some extra replacements
		$extra = array(
			'&apos;' => "'"
		);
		
		$str = str_replace(array_keys($extra),array_values($extra),$str);
		
		return html_entity_decode($str,ENT_QUOTES,'UTF-8');
	}
	
	/**
	@function clean
	
	Removes every tags, comments, cdata from string
	
	@param	str	string	String to clean
	@return	string
	*/
	public static function clean($str)
	{
		$str = strip_tags($str);
		return $str;
	}
	
	/**
	@function escapeJS
	
	Returns a protected JavaScript string
	
	@param	string	str			String to protect
	@return	string
	*/
	public static function escapeJS($str)
	{
		$str = htmlspecialchars($str,ENT_NOQUOTES,'UTF-8');
		$str = str_replace("'","\'",$str);
		$str = str_replace('"','\"',$str);
		return $str;
	}
	
	/**
	@function escapeURL
	
	Returns an escaped URL string for HTML content
	
	@param	str	string	String to escape
	@return	string
	*/
	public static function escapeURL($str)
	{
		return str_replace('&','&amp;',$str);
	}
	
	/**
	@function sanitizeURL
	
	Encode every parts between / in url
	
	@param	str	string	String to satinyze
	@return	string
	*/
	public static function sanitizeURL($str)
	{
		return str_replace('%2F','/',rawurlencode($str));
	}
	
	/**
	@function stripHostURL
	
	Removes host part in URL
	
	@param	url	string	URL to transform
	@return	string
	*/
	public static function stripHostURL($url)
	{
		return preg_replace('|^[a-z]{3,}://.*?(/.*$)|','$1',$url);
	}
	
	/**
	@function absoluteURLs
	
	Appends $root URL to URIs attributes in $str.
	
	@param	str		string	HTML to transform
	@param	root		string	Base URL
	@return	string
	*/
	public static function absoluteURLs($str,$root)
	{
		self::$url_root = $root;
		$attr = 'action|background|cite|classid|codebase|data|href|longdesc|profile|src|usemap';
		
		$str = preg_replace_callback('/((?:'.$attr.')=")(.*?)(")/msu',array('self','absoluteURLHandler'),$str);
		
		foreach (self::$absolute_regs as $r) {
			$str = preg_replace_callback($r,array('self','absoluteURLHandler'),$str);
		}
		
		self::$url_root = null;
		return $str;
	}
	
	private static function absoluteURLHandler($m)
	{
		$url = $m[2];
		
		$link = $m[1].'%s'.$m[3];
		$host = preg_replace('|^([a-z]{3,}://)(.*?)/(.*)$|','$1$2',self::$url_root);
		
		$parse = parse_url($m[2]);
		if (empty($parse['scheme']))
		{
			if (strpos($url,'/') === 0) {
				$url = $host.$url;
			} elseif (preg_match('|/$|',self::$url_root)) {
				$url = self::$url_root.$url;
			} else {
				$url = dirname(self::$url_root).'/'.$url;
			}
		}
		
		return sprintf($link,$url);
	}
}
?>