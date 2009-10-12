<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2006 Olivier Meunier and contributors.
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

class urlHandler
{
	protected $types = array();
	protected $default_handler;
	public $mode;
	public $type = 'default';
	
	public function __construct($mode='path_info')
	{
		$this->mode = $mode;
	}
	
	public function register($type,$url,$representation,$handler)
	{
		$this->types[$type] = array(
			'url' => $url,
			'representation' => $representation,
			'handler' => $handler
		);
	}
	
	public function registerDefault($handler)
	{
		$this->default_handler = $handler;
	}
	
	public function unregister($type)
	{
		if (isset($this->types[$type])) {
			unset($this->types[$type]);
		}
	}
	
	public function getTypes()
	{
		return $this->types;
	}
	
	public function getBase($type)
	{
		if (isset($this->types[$type])) {
			return $this->types[$type]['url'];
		}
		return null;
	}
	
	public function getDocument()
	{
		$type = $args = '';
		
		if ($this->mode == 'path_info')
		{
			$part = substr($_SERVER['PATH_INFO'],1);
		}
		else
		{
			$part = '';
			
			$qs = $this->parseQueryString();
			
			# Recreates some _GET and _REQUEST pairs
			if (!empty($qs))
			{
				foreach ($_GET as $k => $v) {
					if (isset($_REQUEST[$k])) {
						unset($_REQUEST[$k]);
					}
				}
				$_GET = $qs;
				$_REQUEST = array_merge($qs,$_REQUEST);
				
				list($k,$v) = each($qs);
				if ($v === null) {
					$part = $k;
					unset($_GET[$k]);
					unset($_REQUEST[$k]);
				}
			}
		}
		
		$_SERVER['URL_REQUEST_PART'] = $part;
		
		$this->getArgs($part,$type,$args);
		
		if (!$type)
		{
			$this->type = 'default';
			$this->callDefaultHandler($args);
		}
		else
		{
			$this->type = $type;
			$this->callHandler($type,$args);
		}
	}
	
	public function getArgs($part,&$type,&$args)
	{
		if ($part == '') {
			$type = null;
			$args = null;
			return;
		}
		
		$this->sortTypes();
		
		foreach ($this->types as $k => $v)
		{
			$repr = $v['representation'];
			if ($repr == $part) {
				$type = $k;
				$args = null;
				return;
			}
			elseif (preg_match('#'.$repr.'#',$part,$m))
			{
				$type = $k;
				$args = isset($m[1]) ? $m[1] : null;
				return;
			}
		}
		
		# No type, pass args to default
		$args = $part;
	}
	
	public function callHandler($type,$args)
	{
		if (!isset($this->types[$type])) {
			throw new Exception('Unknown URL type');
		}
		
		$handler = $this->types[$type]['handler'];
		if (!is_callable($handler)) {
			throw new Exception('Unable to call function');
		}
		
		call_user_func($handler,$args);
	}
	
	public function callDefaultHandler($args)
	{
		if (!is_callable($this->default_handler)) {
			throw new Exception('Unable to call function');
		}
		
		call_user_func($this->default_handler,$args);
	}
	
	protected function parseQueryString()
	{
		if (!empty($_SERVER['QUERY_STRING']))
		{
			$q = explode('&',$_SERVER['QUERY_STRING']);
			$T = array();
			foreach ($q as $v)
			{
				$t = explode('=',$v,2);
				
				$t[0] = rawurldecode($t[0]);
				if (!isset($t[1])) {
					$T[$t[0]] = null;
				} else {
					$T[$t[0]] = urldecode($t[1]);
				}
			}
			
			return $T;
		}
		return array();
	}
	
	protected function sortTypes()
	{
		foreach ($this->types as $k => $v) {
			$r[$k] = $v['url'];
		}
		array_multisort($r,SORT_DESC,$this->types);
	}
}
?>