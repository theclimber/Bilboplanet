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
@defgroup CB_NET_FEED Clearbricks Feeds Reader
@ingroup CB_NET
*/

/**
@ingroup CB_NET_FEED
@brief netHttp based feed reader.

Features:

- Reads RSS 1.0 (rdf), RSS 2.0 and Atom feeds.
- HTTP cache negociation support
- Cache TTL.
*/
class feedReader extends netHttp
{
	protected $user_agent = 'Clearbricks Feed Reader/0.2';
	protected $timeout = 5;
	protected $validators = null;				///< <b>array</b>	HTTP Cache validators
	
	protected $cache_dir = null;				///< <b>string</b>	Cache temporary directory
	protected $cache_file_prefix = 'cbfeed';	///< <b>string</b>	Cache file prefix
	protected $cache_ttl = '-30 minutes';		///< <b>string</b>	Cache TTL
	
	public function __construct()
	{
		parent::__construct('');
	}
	
	/**
	Returns a new feedParser instance for given URL or false if source URL is
	not a valid feed.
	
	@param	url		<b>string</b>		Feed URL
	@return	<b>feedParser</b>
	*/
	public function parse($url)
	{
		$this->validators = array();
		if ($this->cache_dir)
		{
			return $this->withCache($url);
		}
		else
		{
			if (!$this->getFeed($url)) {
				return false;
			}
			
			if ($this->getStatus() != '200') {
				return false;
			}
			
			return new feedParser($this->getContent());
		}
	}
	
	/**
	This static method returns a new feedParser instance for given URL. If a
	<var>$cache_dir</var> is specified, cache will be activated.
	
	@param	url		<b>string</b>		Feed URL
	@param	cache_dir	<b>string</b>		Cache directory
	@return	<b>feedParser</b>
	*/
	public static function quickParse($url,$cache_dir=null)
	{
		$parser = new self();
		if ($cache_dir) {
			$parser->setCacheDir($cache_dir);
		}
		
		return $parser->parse($url);
	}
	
	/**
	Returns true and sets <var>cache_dir</var> property if <var>$dir</var> is
	a writable directory. Otherwise, returns false.
	
	@param	dir		<b>string</b>		Cache directory
	@return	<b>boolean</b>
	*/
	public function setCacheDir($dir)
	{
		$this->cache_dir = null;
		
		if (!empty($dir) && is_dir($dir) && is_writeable($dir))
		{
			$this->cache_dir = $dir;
			return true;
		}
		
		return false;
	}
	
	/**
	Sets cache TTL. <var>$str</var> is a interval readable by strtotime
	(-3 minutes, -2 hours, etc.)
	
	@param	str		<b>string</b>		TTL
	*/
	public function setCacheTTL($str)
	{
		$str = trim($str);
		if (!empty($str))
		{
			if (substr($str,0,1) != '-') {
				$str = '-'.$str;
			}
			$this->cache_ttl = $str;
		}
	}
	
	/**
	Returns feed content for given URL.
	
	@param	url		<b>string</b>		Feed URL
	@return	<b>string</b>
	*/
	protected function getFeed($url)
	{
		if (!self::readURL($url,$ssl,$host,$port,$path,$user,$pass)) {
			return false;
		}
		$this->setHost($host,$port);
		$this->useSSL($ssl);
		$this->setAuthorization($user,$pass);
		
		return $this->get($path);
	}
	
	/**
	Returns feedParser object from cache if present or write it to cache and
	returns result.
	
	@param	url		<b>string</b>		Feed URL
	@return	<b>feedParser</b>
	*/
	protected function withCache($url)
	{
		$url_md5 = md5($url);
		$cached_file = sprintf('%s/%s/%s/%s/%s.php',
			$this->cache_dir,
			$this->cache_file_prefix,
			substr($url_md5,0,2),
			substr($url_md5,2,2),
			$url_md5
		);
		
		$may_use_cached = false;
		
		if (@file_exists($cached_file))
		{
			$may_use_cached = true;
			$ts = @filemtime($cached_file);
			if ($ts > strtotime($this->cache_ttl))
			{
				# Direct cache
				return unserialize(file_get_contents($cached_file));
			}
			$this->setValidator('IfModifiedSince', $ts);
		}
		
		if (!$this->getFeed($url))
		{
			if ($may_use_cached)
			{
				# connection failed - fetched from cache
				return unserialize(file_get_contents($cached_file));
			}
			return false;
		}
		
		switch ($this->getStatus())
		{
			case '304':
				@files::touch($cached_file);
				return unserialize(file_get_contents($cached_file));
			case '200':
				if ($feed = new feedParser($this->getContent()))
				{
					try {
						files::makeDir(dirname($cached_file),true);
					} catch (Exception $e) {
						return $feed;
					}
					
					if (($fp = @fopen($cached_file, 'wb')))
					{
						fwrite($fp, serialize($feed));
						fclose($fp);
						files::inheritChmod($cached_file);
					}
					return $feed;
				}
		}
		
		return false;
	}
	
	/**
	Adds HTTP cache headers to common headers.
	
	@copydoc netHttp::buildRequest
	*/
	protected function buildRequest()
	{
		$headers = parent::buildRequest();
		
		# Cache validators
		if (!empty($this->validators))
		{
			if (isset($this->validators['IfModifiedSince'])) {
				$headers[] = 'If-Modified-Since: '.$this->validators['IfModifiedSince'];
			}
			if (isset($this->validators['IfNoneMatch'])) {
				if (is_array($this->validators['IfNoneMatch'])) {
					$etags = implode(',',$this->validators['IfNoneMatch']);
				} else {
					$etags = $this->validators['IfNoneMatch'];
				}
				$headers[] = '';
			}
		}
		
		return $headers;
	}
	
	private function setValidator($key,$value)
	{
		if ($key == 'IfModifiedSince') {
			$value = gmdate('D, d M Y H:i:s',$value).' GMT';
		}
		
		$this->validators[$key] = $value;
	}
}
?>