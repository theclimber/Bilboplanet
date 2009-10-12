<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2006 Florent Cotton, Olivier Meunier and contributors.
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
@ingroup CB_NET_FEED
@brief Base feed reader class

Base feed reader, works with feedReader
*/
class feedParser
{
	public $feed_type;			///< <b>string</b>	Feed type
	
	public $title;				///< <b>string</b>	Feed title
	public $link;				///< <b>string</b>	Feed link
	public $description;		///< <b>string</b>	Feed description
	public $pubdate;			///< <b>string</b>	Feed publication date
	public $generator;			///< <b>string</b>	Feed generator
	
	public $items = array();		///< <b>array</b>	Feed items
	
	protected $xml;			///< <b>SimpleXMLElement</b>	Feed XML content
	
	/**
	Constructor. Takes some <var>$data</var> as input. Returns false if data is
	not a valid XML stream. If everything's fine, feed is parsed and items are
	in <var>items</var> property.
	
	@param	data		<b>string</b>		XML stream
	*/
	public function __construct($data)
	{
		$this->xml = @simplexml_load_string($data);
		
		if (!$this->xml) {
			return false;
		}
		
		if (preg_match('/<rdf:RDF/', $data)) {
			$this->parseRSSRDF();
		} elseif (preg_match('/<rss/', $data)) {
			$this->parseRSS();
		} elseif (preg_match('!www.w3.org/2005/Atom!', $data)) {
			$this->parseAtom10();
		} else {
			$this->parseAtom03();
		}
		
		unset($data);
		unset($this->xml);
	}
	
	/**
	RSS 1.0 parser.
	*/
	protected function parseRSSRDF()
	{
		$this->feed_type = 'rss 1.0 (rdf)';
		
		$this->title = (string) $this->xml->channel->title;
		$this->link = (string) $this->xml->channel->link;
		$this->description = (string) $this->xml->channel->description;
		$this->pubdate = (string) $this->xml->channel->children('http://purl.org/dc/elements/1.1/')->date;
		
		# Feed generator agent
		$g = $this->xml->channel->children('http://webns.net/mvcb/')->generatorAgent;
		if ($g) {
			$g = $g->attributes('http://www.w3.org/1999/02/22-rdf-syntax-ns#');
			$this->generator = (string) $g['resource'];
		}
		
		if (empty($this->xml->item)) {
			return;
		}
		
		foreach ($this->xml->item as $i)
		{
			$item = new stdClass();
			$item->title = (string) $i->title;
			$item->link = (string) $i->link;
			$item->creator = (string) $i->children('http://purl.org/dc/elements/1.1/')->creator;
			$item->description = (string) $i->description;
			$item->content = (string) $i->children('http://purl.org/rss/1.0/modules/content/')->encoded;
			$item->subject = $this->nodes2array($i->children('http://purl.org/dc/elements/1.1/')->subject);
			$item->pubdate = (string) $i->children('http://purl.org/dc/elements/1.1/')->date;
			$item->TS = strtotime($item->pubdate);
			
			$item->guid = (string) $item->link;
			if (!empty($i->attributes('http://www.w3.org/1999/02/22-rdf-syntax-ns#')->about)) {
				$item->guid = (string) $i->attributes('http://www.w3.org/1999/02/22-rdf-syntax-ns#')->about;
			}
			
			$this->items[] = $item;
		}
	}
	
	/**
	RSS 2.0 parser
	*/
	protected function parseRSS()
	{
		$this->feed_type = 'rss '.$this->xml['version'];
		
		$this->title = (string) $this->xml->channel->title;
		$this->link = (string) $this->xml->channel->link;
		$this->description = (string) $this->xml->channel->description;
		$this->pubdate = (string) $this->xml->channel->pubDate;
		
		$this->generator = (string) $this->xml->channel->generator;
		
		if (empty($this->xml->channel->item)) {
			return;
		}
		
		foreach ($this->xml->channel->item as $i)
		{
			$item = new stdClass();
			$item->title = (string) $i->title;
			$item->link = (string) $i->link;
			$item->creator = (string) $i->children('http://purl.org/dc/elements/1.1/')->creator;
			$item->description = (string) $i->description;
			$item->content = (string) $i->children('http://purl.org/rss/1.0/modules/content/')->encoded;
			
			$item->subject = array_merge(
				$this->nodes2array($i->children('http://purl.org/dc/elements/1.1/')->subject),
				$this->nodes2array($i->category)
			);
			
			$item->pubdate = (string) $i->pubDate;
			if (!$item->pubdate && !empty($i->children('http://purl.org/dc/elements/1.1/')->date)) {
				$item->pubdate = (string) $i->children('http://purl.org/dc/elements/1.1/')->date;
			}
			
			$item->TS = strtotime($item->pubdate);
			
			$item->guid = (string) $item->link;
			if (!empty($i->guid)) {
				$item->guid = (string) $i->guid;
			}
			
			$this->items[] = $item;
		}
	}
	
	/**
	Atom 0.3 parser
	*/
	protected function parseAtom03()
	{
		$this->feed_type = 'atom 0.3';
		
		$this->title = (string) $this->xml->title;
		$this->description = (string) $this->xml->subtitle;
		$this->pubdate = (string) $this->xml->modified;
		
		$this->generator = (string) $this->xml->generator;
		
		foreach ($this->xml->link as $link)
		{
			if ($link['rel'] == 'alternate' &&
				($link['type'] == 'text/html' || $link['type'] == 'application/xhtml+xml')) {
				$this->link = (string) $link['href'];
				break;
			}
		}
		
		if (empty($this->xml->entry)) {
			return;
		}
		
		foreach ($this->xml->entry as $i)
		{
			$item = new stdClass();
			
			foreach ($i->link as $link)
			{
				if ($link['rel'] == 'alternate' &&
					($link['type'] == 'text/html' || $link['type'] == 'application/xhtml+xml')) {
					$item->link = (string) $link['href'];
					break;
				}
			}
			
			$item->title = (string) $i->title;
			$item->creator = (string) $i->author->name;
			$item->description = (string) $i->summary;
			$item->content = (string) $i->content;
			$item->subject = $this->nodes2array($i->children('http://purl.org/dc/elements/1.1/')->subject);
			$item->pubdate = (string) $i->modified;
			$item->TS = strtotime($item->pubdate);
			
			$this->items[] = $item;
		}
	}
	
	/**
	Atom 1.0 parser
	*/
	protected function parseAtom10()
	{
		$this->feed_type = 'atom 1.0';
		
		$this->title = (string) $this->xml->title;
		$this->description = (string) $this->xml->subtitle;
		$this->pubdate = (string) $this->xml->updated;
		
		$this->generator = (string) $this->xml->generator;
		
		foreach ($this->xml->link as $link)
		{
			if ($link['rel'] == 'alternate' &&
				($link['type'] == 'text/html' || $link['type'] == 'application/xhtml+xml')) {
				$this->link = (string) $link['href'];
				break;
			}
		}
		
		if (empty($this->xml->entry)) {
			return;
		}
		
		foreach ($this->xml->entry as $i)
		{
			$item = new stdClass();
			
			foreach ($i->link as $link)
			{
				if ($link['rel'] == 'alternate' &&
					($link['type'] == 'text/html' || $link['type'] == 'application/xhtml+xml')) {
					$item->link = (string) $link['href'];
					break;
				}
			}
			
			$item->title = (string) $i->title;
			$item->creator = (string) $i->author->name;
			$item->description = (string) $i->summary;
			$item->content = (string) $i->content;
			$item->subject = $this->nodes2array($i->children('http://purl.org/dc/elements/1.1/')->subject);
			$item->pubdate = !empty($i->published) ? (string) $i->published : (string) $i->updated;
			$item->TS = strtotime($item->pubdate);
			
			$this->items[] = $item;
		}
	}
	
	/**
	Converts a SimpleXMLElement to an array.
	*/
	protected function nodes2array(&$nodes)
	{
		if (empty($nodes)) {
			return array();
		}
		
		$res = array();
		foreach ($nodes as $v)
		{
			$res[] = (string) $v;
		}
		
		return $res;
	}
	
	/**
	@if 
	*/
}
?>