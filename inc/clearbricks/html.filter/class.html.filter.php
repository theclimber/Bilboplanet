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
#
# Theses static functions are taken fomr Ulf Harnhammar's Kses 0.2.2
# http://sourceforge.net/projects/kses

class htmlFilter
{
	private $parser;
	public $content;
	
	private $tag;
	
	public function __construct()
	{
		$this->parser = xml_parser_create('UTF-8');
		xml_set_object($this->parser,$this);
		xml_set_element_handler($this->parser, 'tag_open', 'tag_close');
		xml_set_character_data_handler($this->parser, 'cdata');
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		
		$this->removeTags(
			'applet','base','basefont','body','center','dir','font',
			'frame','frameset','head','html','isindex',
			'link','menu','meta','noframes','script','style'
		);
		
		$this->removeAttributes(
			'onclick','ondblclick','onfocus','onkeydown','onkeypress',
			'onkeyup','onload','onmousedown','onmousemove','onmouseout',
			'onmouseover','onmouseup','onreset','onselect','onsubmit',
			'onunload'
		);
	}
	
	public function removeTags()
	{
		foreach ($this->argsArray(func_get_args()) as $tag) {
			$this->removed_tags[] = $tag;
		}
	}
	
	public function removeAttributes()
	{
		foreach ($this->argsArray(func_get_args()) as $a) {
			$this->removed_attrs[] = $a;
		}
	}
	
	public function removeTagAttributes($tag)
	{
		$args = $this->argsArray(func_get_args());
		array_shift($args);
		
		foreach ($args as $a) {
			$this->removed_tag_attrs[$tag][] = $a;
		}
	}
	
	public function setTags($t)
	{
		if (is_array($t)) {
			$this->tags = $t;
		}
	}
	
	public function apply($str)
	{
		if (extension_loaded('tidy') && class_exists('tidy'))
		{
			$config = array(
				'doctype' => 'strict',
				'drop-proprietary-attributes' => true,
				'drop-font-tags' => true,
				'escape-cdata' => true,
				'indent' => false,
				'join-classes' => false,
				'join-styles' => true,
				'lower-literals' => true,
				'output-xhtml' => true,
				'show-body-only' => true,
				'wrap' => 80
			);
			
			$str = '<p>tt</p>'.$str; // Fixes a big issue
			
			$tidy = new tidy;
			$tidy->parseString($str, $config, 'utf8');
			$tidy->cleanRepair();
			
			$str = (string) $tidy;
			
			$str = preg_replace('#^<p>tt</p>\s?#','',$str);
		}
		else
		{
			$str = $this->miniTidy($str);
		}
		
		# Removing open comments, open CDATA and processing instructions
		$str = preg_replace('%<!--.*?-->%msu','',$str);
		$str = str_replace('<!--','',$str);
		$str = preg_replace('%<!\[CDATA\[.*?\]\]>%msu','',$str);
		$str = str_replace('<![CDATA[','',$str);
		
		# Transform processing instructions
		$str = str_replace('<?','&gt;?',$str);
		$str = str_replace('?>','?&lt;',$str);
		
		$str = html::decodeEntities($str,true);
		
		$this->content = '';
		xml_parse($this->parser,'<all>'.$str.'</all>');
		return $this->content;
	}
	
	private function argsArray($args)
	{
		$A = array();
		foreach ($args as $v) {
			if (is_array($v)) {
				$A = array_merge($A,$v);
			} else {
				$A[] = (string) $v;
			}
		}
		return array_unique($A);
	}
	
	private function tag_open(&$parser,$tag,$attrs)
	{
		$this->tag = strtolower($tag);
		
		if ($this->tag == 'all') {
			return;
		}
		
		if ($this->allowedTag($this->tag))
		{
			$this->content .= '<'.$tag.$this->getAttrs($tag,$attrs);
			
			if (in_array($this->tag,$this->single_tags)) {
				$this->content .= ' />';
			} else {
				$this->content .= '>';
			}
		}
	}
	
	private function tag_close(&$parser,$tag)
	{
		if (!in_array($tag,$this->single_tags) && $this->allowedTag($tag)) {
			$this->content .= '</'.$tag.'>';
		}
	}
	
	private function cdata($parser, $cdata)
	{
		$this->content .= html::escapeHTML($cdata);
	}
	
	private function getAttrs($tag,$attrs)
	{
		$res = '';
		foreach ($attrs as $n => $v)
		{
			if ($this->allowedAttr($tag,$n)) {
				$res .= $this->getAttr($n,$v);
			}
		}
		return $res;
	}
	
	private function getAttr($attr,$value)
	{
		$value = preg_replace('/\xad+/', '', $value);
		
		if (in_array($attr,$this->uri_attrs)) {
			$value = $this->getURI($value);
		}
		
		return ' '.$attr.'="'.html::escapeHTML($value).'"';
	}
	
	private function getURI($uri)
	{
		$u = @parse_url($uri);
		
		if (is_array($u) && (empty($u['scheme']) || in_array($u['scheme'],$this->allowed_schemes))) {
			return $uri;
		}
		
		return '#';
	}
	
	private function allowedTag($tag)
	{
		return
		!in_array($tag,$this->removed_tags)
		&& isset($this->tags[$tag]);
	}
	
	private function allowedAttr($tag,$attr)
	{
		if (in_array($attr,$this->removed_attrs)) {
			return false;
		}
		
		if (isset($this->removed_tag_attrs[$tag]) && in_array($attr,$this->removed_tag_attrs[$tag])) {
			return false;
		}
		
		if (!isset($this->tags[$tag]) || !in_array($attr,$this->tags[$tag])) {
			return false;
		}
		return true;
	}
	
	private function miniTidy($str)
	{
		$str = preg_replace_callback('%(<(?!(\s*?/|!)).*?>)%msu',array($this,'miniTidyFixTag'),$str);
		return $str;
	}
	
	private function miniTidyFixTag($m)
	{
		# Non quoted attributes
		return preg_replace_callback('%(=")(.*?)(")%msu',array($this,'miniTidyFixAttr'),$m[1]);
	}
	
	private function miniTidyFixAttr($m)
	{
		# Escape entities in attributes value
		return $m[1].html::escapeHTML(html::decodeEntities($m[2])).$m[3];
	}
	
	/* Tags and attributes definitions
	------------------------------------------------------- */
	private $removed_tags = array();
	private $removed_attrs = array();
	private $removed_tag_attrs = array();
	
	private $allowed_schemes = array(
		'http','https','ftp','mailto','news'
	);
	
	private $uri_attrs = array(
		'action','background','cite','classid','codebase',
		'data','href','longdesc','profile','src','usemap'
	);
	
	private $single_tags = array(
		'area','base','basefont','br','col','frame','hr','img','input',
		'isindex','link','meta','param'
	);
	
	private $tags = array(
	'a'			=> array('accesskey','charset','class','coords','dir','href',
				'hreflang','id','lang','name','onblur','onclick',
				'ondblclick','onfocus','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','rel','rev','shape','style','tabindex','target',
				'title','type'),
	'abbr'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'acronym'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'address'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'applet'		=> array('align','alt','archive','class','code','codebase',
				'height','hspace','id','name','object','style','title',
				'vspace','width'),
	'area'		=> array('accesskey','alt','class','coords','dir','href',
				'id','lang','nohref','onblur','onclick','ondblclick',
				'onfocus','onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup','shape',
				'style','tabindex','target','title'),
	'b'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'base'		=> array('href','target'),
	'basefont'	=> array('color','face','id','size'),
	'bdo'		=> array('class','dir','id','lang','style','title'),
	'big'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'blockquote'	=> array('cite','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'body'		=> array('alink','background','bgcolor','class','dir','id',
				'lang','link','onclick','ondblclick','onkeydown',
				'onkeypress','onkeyup','onload','onmousedown','onmousemove',
				'onmouseout','onmouseover','onmouseup','onunload','style',
				'text','title','vlink'),
	'br'			=> array('class','clear','id','style','title'),
	'button'		=> array('accesskey','class','dir','disabled','id','lang',
				'name','onblur','onclick','ondblclick','onfocus',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','tabindex','title','type','value'),
	'caption'		=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'center'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'cite'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'code'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'col'		=> array('align','char','charoff','class','dir','id','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','span','style','title','valign','width'),
	'colgroup'	=> array('align','char','charoff','class','dir','id','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','span','style','title','valign','width'),
	'dd'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'del'		=> array('class','datetime','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'dfn'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'dir'		=> array('class','compact','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'div'		=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'dl'			=> array('class','compact','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'dt'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'em'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'fieldset'	=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'font'		=> array('class','color','dir','face','id','lang','size',
				'style','title'),
	'form'		=> array('accept-charset','accept','action','class','dir',
				'enctype','id','lang','method','name','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','onreset','onsubmit','style','target','title'),
	'frame'		=> array('class','frameborder','id','longdesc',
				'marginheight','marginwidth','name','noresize','scrolling',
				'src','style','title'),
	'frameset'	=> array('class','cols','id','onload','onunload','rows',
				'style','title'),
	'h1'			=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'h2'			=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'h3'			=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'h4'			=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'h5'			=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'h6'			=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'head'		=> array('dir','lang','profile'),
	'hr'			=> array('align','class','dir','id','lang','noshade',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','size','style','title','width'),
	'html'		=> array('dir','lang','version'),
	'i'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'iframe'		=> array('align','class','frameborder','height','id',
				'longdesc','marginheight','marginwidth','name','scrolling',
				'src','style','title','width'),
	'img'		=> array('align','alt','border','class','dir','height',
				'hspace','id','ismap','lang','longdesc','name','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','src','style','title','usemap','vspace','width'),
	'input'		=> array('accept','accesskey','align','alt','checked',
				'class','dir','disabled','id','ismap','lang','maxlength',
				'name','onblur','onchange','onclick','ondblclick','onfocus',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'onselect','readonly','size','src','style','tabindex',
				'title','type','usemap','value'),
	'ins'		=> array('class','datetime','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'isindex'		=> array('class','dir','id','lang','prompt','style','title'),
	'kbd'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'label'		=> array('accesskey','class','dir','for','id','lang',
				'onblur','onclick','ondblclick','onfocus','onkeydown',
				'onkeypress','onkeyup','onmousedown','onmousemove',
				'onmouseout','onmouseover','onmouseup','style','title'),
	'legend'		=> array('accesskey','align','class','dir','id','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'li'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title','type','value'),
	'link'		=> array('charset','class','dir','href','hreflang','id',
				'lang','media','onclick','ondblclick','onkeydown',
				'onkeypress','onkeyup','onmousedown','onmousemove',
				'onmouseout','onmouseover','onmouseup','rel','rev','style',
				'target','title','type'),
	'map'		=> array('class','dir','id','lang','name','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'menu'		=> array('class','compact','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'meta'		=> array('content','dir','http-equiv','lang','name','scheme'),
	'noframes'	=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'noscript'	=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'object'		=> array('align','border','class','classid','codebase',
				'codetype','data','declare','dir','height','hspace','id',
				'lang','name','onclick','ondblclick','onkeydown',
				'onkeypress','onkeyup','onmousedown','onmousemove',
				'onmouseout','onmouseover','onmouseup','standby','style',
				'tabindex','title','type','usemap','vspace','width'),
	'ol'			=> array('class','compact','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','start','style','title','type'),
	'optgroup'	=> array('class','dir','disabled','id','label','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'option'		=> array('class','dir','disabled','id','label','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','selected','style','title','value'),
	'p'			=> array('align','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	'param'		=> array('id','name','type','value','valuetype'),
	'pre'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title','width'),
	'q'			=> array('cite','class','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title'),
	's'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'samp'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'script'		=> array('charset','defer','language','src','type'),
	'select'		=> array('class','dir','disabled','id','lang','multiple',
				'name','onblur','onchange','onclick','ondblclick','onfocus',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup','size',
				'style','tabindex','title'),
	'small'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'span'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'strike'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'strong'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'style'		=> array('dir','lang','media','type'),
	'sub'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'sup'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'table'		=> array('align','bgcolor','border','cellpadding',
				'cellspacing','class','dir','frame','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','rules','style','summary','title','width'),
	'tbody'		=> array('align','char','charoff','class','dir','id','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title','valign'),
	'td'			=> array('abbr','align','axis','bgcolor','char','charoff',
				'class','colspan','dir','headers','height','id','lang',
				'nowrap','onclick','ondblclick','onkeydown','onkeypress',
				'onkeyup','onmousedown','onmousemove','onmouseout',
				'onmouseover','onmouseup','rowspan','scope','style','title',
				'valign','width'),
	'textarea'	=> array('accesskey','class','dir','disabled','id','lang',
				'name','onblur','onchange','onclick','ondblclick','onfocus',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'onselect','readonly','style','tabindex','title'),
	'tfoot'		=> array('align','char','charoff','class','dir','id','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title','valign'),
	'th'			=> array('abbr','align','axis','bgcolor','char','charoff',
				'class','colspan','dir','headers','height','id','lang',
				'nowrap','onclick','ondblclick','onkeydown','onkeypress',
				'onkeyup','onmousedown','onmousemove','onmouseout',
				'onmouseover','onmouseup','rowspan','scope','style','title',
				'valign','width'),
	'thead'		=> array('align','char','charoff','class','dir','id','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title','valign'),
	'title'		=> array('dir','lang'),
	'tr'			=> array('align','char','charoff','class','dir','id','lang',
				'onclick','ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title','valign'),
	'tt'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'u'			=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title'),
	'ul'			=> array('class','compact','dir','id','lang','onclick',
				'ondblclick','onkeydown','onkeypress','onkeyup',
				'onmousedown','onmousemove','onmouseout','onmouseover',
				'onmouseup','style','title','type'),
	'var'		=> array('class','dir','id','lang','onclick','ondblclick',
				'onkeydown','onkeypress','onkeyup','onmousedown',
				'onmousemove','onmouseout','onmouseover','onmouseup',
				'style','title')
	);
}
?>