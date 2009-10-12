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

class restServer
{
	public $rsp;
	public $functions = array();
	
	public function __construct()
	{
		$this->rsp = new xmlTag('rsp');
	}
	
	public function addFunction($name, $callback)
	{
		if (is_callable($callback)) {
			$this->functions[$name] = $callback;
		}
	}
	
	protected function callFunction($name,$get,$post)
	{
		if (isset($this->functions[$name])) {
			return call_user_func($this->functions[$name],$get,$post);
		}
	}
	
	public function serve($encoding='UTF-8')
	{
		$get = array();
		if (isset($_GET)) {
			$get = $_GET;
		}
		
		$post = array();
		if (isset($_POST)) {
			$post = $_POST;
		}
		
		if (!isset($_REQUEST['f'])) {
			$this->rsp->status = 'failed';
			$this->rsp->message('No function given');
			$this->getXML($encoding);
			return false;
		}
		
		if (!isset($this->functions[$_REQUEST['f']])) {
			$this->rsp->status = 'failed';
			$this->rsp->message('Function does not exist');
			$this->getXML($encoding);
			return false;
		}
		
		try {
			$res = $this->callFunction($_REQUEST['f'],$get,$post);
		} catch (Exception $e) {
			$this->rsp->status = 'failed';
			$this->rsp->message($e->getMessage());
			$this->getXML($encoding);
			return false;
		}
		
		$this->rsp->status = 'ok';
		
		$this->rsp->insertNode($res);
		
		$this->getXML($encoding);
		return true;
	}
	
	private function getXML($encoding='UTF-8')
	{
		header('Content-Type: text/xml; charset='.$encoding);
		echo $this->rsp->toXML(1,$encoding);
	}
}

class xmlTag
{
	private $_name;
	private $_attr = array();
	private $_nodes = array();
	
	public function __construct($name=null, $content=null)
	{
		$this->_name = $name;
		
		if ($content !== null) {
			$this->insertNode($content);
		}
	}
	
	public function __set($name, $value)
	{
		$this->insertAttr($name, $value);
	}
	
	public function __call($name, $args)
	{
		if (!preg_match('#^[a-z_]#',$name)) {
			return false;
		}
		
		if (!isset($args[0])) {
			$args[0] = null;
		}
		
		$this->insertNode(new self($name,$args[0]));
	}
	
	public function CDATA($value)
	{
		$this->insertNode($value);
	}
	
	public function insertAttr($name, $value)
	{
		$this->_attr[$name] = $value;
	}
	
	public function insertNode($node=null)
	{
		if ($node instanceof self)
		{
			$this->_nodes[] = $node;
		}
		elseif (is_array($node))
		{
			$child = new self(null);
			foreach ($node as $tag => $n) {
				$child->insertNode(new self($tag,$n));
			}
			$this->_nodes[] = $child;
		}
		elseif (is_bool($node))
		{
			$this->_nodes[] = $node ? '1' : '0';
		}
		else
		{
			$this->_nodes[] = (string) $node;
		}
	}
	
	public function toXML($prolog=false,$encoding='UTF-8')
	{
		if ($this->_name && count($this->_nodes) > 0) {
			$p = '<%1$s%2$s>%3$s</%1$s>';
		} elseif ($this->_name && count($this->_nodes) == 0) {
			$p = '<%1$s%2$s/>';
		} else {
			$p = '%3$s';
		}
		
		$res = $attr = $content = '';
		
		
		foreach ($this->_attr as $k => $v) {
			$attr .= ' '.$k.'="'.htmlspecialchars($v,ENT_QUOTES,$encoding).'"';
		}
		
		foreach ($this->_nodes as $node)
		{
			if ($node instanceof self) {
				$content .= $node->toXML();
			} else {
				$content .= htmlspecialchars($node,ENT_QUOTES,$encoding);
			}
		}
		
		$res = sprintf($p,$this->_name,$attr,$content);
		
		if ($prolog && $this->_name) {
			$res = '<?xml version="1.0" encoding="'.$encoding.'" ?>'."\n".$res;
		}
		
		return $res;
	}
}
?>