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

class pager
{
	private $env;
	private $nb_elements;
	private $nb_per_page;
	private $nb_pages_per_group;
	
	private $nb_pages;
	private $nb_groups;
	public $index_start;
	public $index_end;
	private $env_group;
	private $index_group_start;
	private $index_group_end;
	
	private $page_url = null;
	public $base_url = null;
	
	public $var_page = 'page';
	
	public $html_cur_page	= '<strong>%s</strong>';
	public $html_link_sep	= '-';
	public $html_prev		= '&#171;prev.';
	public $html_next		= 'next&#187;';
	public $html_prev_grp	= '...';
	public $html_next_grp	= '...';
	
	public function __construct($env,$nb_elements,$nb_per_page=10,$nb_pages_per_group=10)
	{
		$this->env = abs((integer) $env);
		$this->nb_elements = abs((integer) $nb_elements);
		$this->nb_per_page = abs((integer) $nb_per_page);
		$this->nb_pages_per_group = abs((integer) $nb_pages_per_group);
		
		
		# Pages count
		$this->nb_pages = ceil($this->nb_elements/$this->nb_per_page);
		
		# Fix env value
		if ($this->env > $this->nb_pages || $this->env < 1) {
			$this->env = 1;
		}
		
		# Groups count
		$this->nb_groups = (integer) ceil($this->nb_pages/$this->nb_pages_per_group);
		
		# Page first element index
		$this->index_start = ($this->env-1)*$this->nb_per_page;
		
		# Page last element index
		$this->index_end = $this->index_start+$this->nb_per_page-1;
		if($this->index_end >= $this->nb_elements) {
			$this->index_end = $this->nb_elements-1;
		}
		
		# Current group
		$this->env_group = (integer) ceil($this->env/$this->nb_pages_per_group);
		
		# Group first page index
		$this->index_group_start = ($this->env_group-1)*$this->nb_pages_per_group+1;
		
		# Group last page index
		$this->index_group_end = $this->index_group_start+$this->nb_pages_per_group-1;
		if($this->index_group_end > $this->nb_pages) {
			$this->index_group_end = $this->nb_pages;
		}
	}
	
	public function getLinks()
	{
		$htmlLinks = '';
		$htmlPrev = '';
		$htmlNext = '';
		$htmlPrevGrp = '';
		$htmlNextGrp = '';
		
		$this->setURL();
		
		for($i=$this->index_group_start; $i<=$this->index_group_end; $i++)
		{
			if($i == $this->env) {
				$htmlLinks .= sprintf($this->html_cur_page,$i);
			} else {
				$htmlLinks .= '<a href="'.sprintf($this->page_url,$i).'">'.$i.'</a>';
			}
			
			if($i != $this->index_group_end) {
				$htmlLinks .= $this->html_link_sep;
			}
		}
		
		# Previous page
		if($this->env != 1) {
			$htmlPrev = '<a href="'.sprintf($this->page_url,$this->env-1).'">'.
			$htmlPrev .= $this->html_prev.'</a>&nbsp;';
		}
		
		# Next page
		if($this->env != $this->nb_pages) {
			$htmlNext = '&nbsp;<a href="'.sprintf($this->page_url,$this->env+1).'">';
			$htmlNext .= $this->html_next.'</a>';
		}
		
		# Previous group
		if($this->env_group != 1) {
			$htmlPrevGrp = '&nbsp;<a href="'.sprintf($this->page_url,$this->index_group_start - $this->nb_pages_per_group).'">';
			$htmlPrevGrp .= $this->html_prev_grp.'</a>&nbsp;';
		}
		
		# Next group
		if($this->env_group != $this->nb_groups) {
			$htmlNextGrp = '&nbsp;<a href="'.sprintf($this->page_url,$this->index_group_end+1).'">';
			$htmlNextGrp .= $this->html_next_grp.'</a>&nbsp;';
		}
		
		$res =	$htmlPrev.
				$htmlPrevGrp.
				$htmlLinks.
				$htmlNextGrp.
				$htmlNext;
		
		return $this->nb_elements > 0 ? $res : '';
	}
	
	private function setURL()
	{
		if ($this->base_url !== null) {
			$this->page_url = $this->base_url;
			return;
		}
		
		$url = $_SERVER['REQUEST_URI'];
		
		# Removing session information
		if (session_id())
		{
			$url = preg_replace('/'.preg_quote(session_name().'='.session_id(),'/').'([&]?)/','',$url);
			$url = preg_replace('/&$/','',$url);
		}
		
		# Escape page_url for sprintf
		$url = preg_replace('/%/','%%',$url);
		
		# Changing page ref
		if (preg_match('/[?&]'.$this->var_page.'=[0-9]+/',$url))
		{
			$url = preg_replace('/([?&]'.$this->var_page.'=)[0-9]+/','$1%1$d',$url);
		}
		elseif (preg_match('/[?]/',$url))
		{
			$url .= '&'.$this->var_page.'=%1$d';
		}
		else
		{
			$url .= '?'.$this->var_page.'=%1$d';
		}
		
		$this->page_url = html::escapeHTML($url);
	}
	
	public function debug()
	{
		return
		"Elements per page ........... ".$this->nb_per_page."\n".
		'Pages per group.............. '.$this->nb_pages_per_group."\n".
		"Elements count .............. ".$this->nb_elements."\n".
		'Pages ....................... '.$this->nb_pages."\n".
		'Groups ...................... '.$this->nb_groups."\n\n".
		'Current page .................'.$this->env."\n".
		'Start index ................. '.$this->index_start."\n".
		'End index ................... '.$this->index_end."\n".
		'Current group ............... '.$this->env_group."\n".
		'Group first page index ...... '.$this->index_group_start."\n".
		'Group last page index ....... '.$this->index_group_end;
	}
}
?>