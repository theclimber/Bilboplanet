<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2007 Olivier Meunier and contributors.
# All rights reserved.
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
@ingroup CLEARBRICKS
@brief HTML markup validation class
*/

class htmlValidator extends netHttp
{
	protected $host = 'www.htmlhelp.com';
	protected $path = '/cgi-bin/validate.cgi';
	protected $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.3a) Gecko/20021207';
	protected $timeout = 2;
	
	protected $html_errors = array();		///<	<b>array</b>		Validation errors list
	
	/**
	Constructor, no parameters.
	*/
	public function __construct()
	{
		parent::__construct($this->host,80,$this->timeout);
	}
	
	/**
	Returns an HTML document from a <var>$fragment</var>.
	
	@param	fragment	<b>string</b>		HTML content
	@return	<b>string</b>
	*/
	public function getDocument($fragment)
	{
		return
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" '.
		'"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n".
		'<html xmlns="http://www.w3.org/1999/xhtml">'."\n".
		'<head>'."\n".
		'<title>validation</title>'."\n".
		'</head>'."\n".
		'<body>'."\n".
		$fragment."\n".
		'</body>'."\n".
		'</html>';
	}
	
	/**
	Performs HTML validation of <var>$html</var>.
	
	@param	html		<b>string</b>		HTML document
	@param	charset	<b>string</b>		Document charset
	@return	<b>boolean</b>
	*/
	public function perform($html,$charset='UTF-8')
	{
		$data = array('area' => $html, 'charset' => $charset);
		$this->post($this->path,$data);
		
		if ($this->getStatus() != 200) {
			throw new Exception('Status code line invalid.');
		}
		
		$result = $this->getContent();
		
		if (strpos($result,'<p class=congratulations><strong>Congratulations, no errors!</strong></p>'))
		{
			return true;
		}
		else
		{
			if ($errors = preg_match('#<h2>Errors</h2>[\s]*(<ul>.*</ul>)#msU',$result,$matches)) {
				$this->html_errors = strip_tags($matches[1],'<ul><li><pre><b>');
			}
			return false;
		}
	}
	
	/**
	Returns HTML validation errors list.
	
	@return	<b>string</b>
	*/
	public function getErrors()
	{
		return $this->html_errors;
	}
	
	/**
	Static validation method of an HTML fragment. Returns an array with the
	following parameters:
	
	- valid (boolean)
	_ errors (string)
	
	@param	fragment	<b>string</b>		HTML content
	@param	charset	<b>string</b>			Document charset
	@return	<b>array</b>
	*/
	public static function validate($fragment,$charset='UTF-8')
	{
		$o = new self;
		$fragment = $o->getDocument($fragment,$charset);
		
		if ($o->perform($fragment,$charset))
		{
			return array('valid' => true, 'errors' => null);
		}
		else
		{
			return array('valid' => false, 'errors' => $o->getErrors());
		}
	}
}
?>