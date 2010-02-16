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

class form
{
	private static function getNameAndId($nid,&$name,&$id)
	{
		if (is_array($nid)) {
			$name = $nid[0];
			$id = !empty($nid[1]) ? $nid[1] : null;
		} else {
			$name = $id = $nid;
		}
	}
	
	public static function combo($nid, $data ,$default='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<select name="'.$name.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= '>'."\n";
		
		$res .= self::comboOptions($data,$default);
		
		$res .= '</select>'."\n";
		
		return $res;
	}
	
	private static function comboOptions($data,$default)
	{
		$res = '';
		$option = '<option value="%1$s"%3$s>%2$s</option>'."\n";
		$optgroup = '<optgroup label="%1$s">'."\n".'%2$s'."</optgroup>\n";
		
		foreach($data as $k => $v)
		{
			if (is_array($v)) {
				$res .= sprintf($optgroup,$k,self::comboOptions($v,$default));
			} elseif ($v instanceof formSelectOption) {
				$res .= $v->render($default);
			} else {
				$s = ($v == $default) ? ' selected="selected"' : '';
				$res .= sprintf($option,$v,$k,$s);
			}
		}
		
		return $res;
	}
	
	public static function radio($nid, $value, $checked='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="radio" name="'.$name.'" value="'.$value.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $checked ? 'checked="checked" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= '/>'."\n";
		
		return $res;	
	}

	public static function checkbox($nid, $value, $checked='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="checkbox" name="'.$name.'" value="'.$value.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $checked ? 'checked="checked" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= ' />'."\n";

		return $res;
	}

	public static function field($nid, $size, $max, $default='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="text" size="'.$size.'" name="'.$name.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $max ? 'maxlength="'.$max.'" ' : '';
		$res .= $default || $default === '0' ? 'value="'.$default.'" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= ' />';
		
		return $res;
	}
	
	public static function password($nid, $size, $max, $default='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="password" size="'.$size.'" name="'.$name.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $max ? 'maxlength="'.$max.'" ' : '';
		$res .= $default || $default === '0' ? 'value="'.$default.'" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= ' />';
		
		return $res;
	}
	
	public static function textArea($nid, $cols, $rows, $default='', $class='',
	$tabindex='', $disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<textarea cols="'.$cols.'" rows="'.$rows.'" ';
		$res .= 'name="'.$name.'" ';
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= ($tabindex != '') ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html.'>';
		$res .= $default;
		$res .= '</textarea>';
		
		return $res;
	}
	
	public static function hidden($nid,$value)
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="hidden" name="'.$name.'" value="'.$value.'" ';
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= ' />';
		
		return $res;
	}
}

class formSelectOption
{
	public $name;
	public $value;
	public $class_name;
	public $html;
	
	private $option = '<option value="%1$s"%3$s>%2$s</option>';
	
	public function __construct($name,$value,$class_name='',$html='')
	{
		$this->name = $name;
		$this->value = $value;
		$this->class_name = $class_name;
		$this->html = $html;
	}
	
	public function render($default)
	{
		$attr = $this->html;
		$attr .= $this->class_name ? ' class="'.$this->class_name.'"' : '';
		
		if ($this->value == $default) {
			$attr .= ' selected="selected"';
		}
		
		return sprintf($this->option,$this->value,$this->name,$attr)."\n";
	}
}
?>