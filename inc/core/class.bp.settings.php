<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : http://chili.kiwais.com/projects/bilboplanet
* Blog : www.bilboplanet.com
* 
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
***** END LICENSE BLOCK *****/
?><?php

/**
@ingroup DC_CORE
@brief Blog settings handler

dcSettings provides blog settings management. This class instance exists as
dcBlog $settings property. You should create a new settings instance when
updating another blog settings.
*/
class bpSettings
{
	protected $con;		///< <b>connection</b> Database connection object
	protected $table;		///< <b>string</b> Permission table name
	protected $user_id;		///< <b>string</b> User ID
	
	protected $settings = array();		///< <b>array</b> Associative settings array
	protected $global_settings = array();	///< <b>array</b> Global settings array
	protected $local_settings = array();	///< <b>array</b> Local settings array
	
	/**
	Object constructor. Retrieves blog settings and puts them in $settings
	array. Local (blog) settings have a highest priority than global settings.
	
	@param	core		<b>bpCore</b>		bpCore object
	@param	user_id	<b>string</b>		User ID
	*/
	public function __construct(&$core,$user_id)
	{
		$this->con =& $core->con;
		$this->table = $core->prefix.'setting';
		$this->user_id =& $user_id;
		
		$this->getSettings();
	}
	
	private function getSettings()
	{
		$strReq = "SELECT 
					user_id,
					setting_id,
					setting_value,
					setting_label,
					setting_type
				FROM ".$this->table." 
				WHERE user_id = '".$this->con->escape($this->user_id)."'
				OR user_id IS NULL 
				ORDER BY setting_value, setting_id DESC ";
		
		try {
			$rs = $this->con->select($strReq);
		} catch (Exception $e) {
			trigger_error(T_('Unable to retrieve settings:').' '.$this->con->error(), E_USER_ERROR);
		}

		while ($rs->fetch())
		{
			$id = trim($rs->setting_id);
			$value = $rs->setting_value;
			$type = $rs->setting_type;
			
			if ($type == 'float' || $type == 'double') {
				$type = 'float';
			} elseif ($type != 'boolean' && $type != 'integer') {
				$type = 'string';
			}
			
			settype($value,$type);
			
			$array = $rs->user_id ? 'local' : 'global';
			
			$this->{$array.'_settings'}[$id] = array(
				'value' => $value,
				'type' => $type,
				'label' => (string) $rs->setting_label,
				'global' => $rs->user_id == ''
			);
		}
		
		$this->settings = $this->global_settings;
		
		foreach ($this->local_settings as $id => $v) {
			$this->settings[$id] = $v;
		}
			
		return true;
	}
	
	private function settingExists($id,$global=false)
	{
		$array = $global ? 'global' : 'local';
		return isset($this->{$array.'_settings'}[$id]);
	}
	
	
	/**
	Creates or updates a setting.
	
	$type could be 'string', 'integer', 'float', 'boolean' or null. If $type is
	null and setting exists, it will keep current setting type.
	
	$value_change allow you to not change setting. Useful if you need to change
	a setting label or type and don't want to change its value.
	
	@param	id			<b>string</b>		Setting ID
	@param	value		<b>mixed</b>		Setting value
	@param	type		<b>string</b>		Setting type
	@param	label		<b>string</b>		Setting label
	@param	value_change	<b>boolean</b>		Change setting value or not
	@param	global		<b>boolean</b>		Setting is global
	*/
	public function put($id,$value,$type=null,$label=null,$value_change=true,$global=false)
	{
		if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_.]+$/',$id)) {
			throw new Exception(sprintf(T_('%s is not a valid setting id'),$id));
		}
		
		# We don't want to change setting value
		if (!$value_change) {
			if (!$global && $this->settingExists($id,false)) {
				$value = $this->local_settings[$id]['value'];
			} elseif ($this->settingExists($id,true)) {
				$value = $this->global_settings[$id]['value'];
			}
		}
		
		# Setting type
		if ($type == 'double')
		{
			$type = 'float';
		}
		elseif ($type === null)
		{
			if (!$global && $this->settingExists($id,false)) {
				$type = $this->local_settings[$id]['type'];
			} elseif ($this->settingExists($id,true)) {
				$type = $this->global_settings[$id]['type'];
			} else {
				$type = 'string';
			}
		}
		elseif ($type != 'boolean' && $type != 'integer' && $type != 'float')
		{
			$type = 'string';
		}
		
		# We don't change label
		if ($label == null)
		{
			if (!$global && $this->settingExists($id,false)) {
				$label = $this->local_settings[$id]['label'];
			} elseif ($this->settingExists($id,true)) {
				$label = $this->global_settings[$id]['label'];
			}
		}
		
		settype($value,$type);
		
		$cur = $this->con->openCursor($this->table);
		$cur->setting_value = ($type == 'boolean') ? (string) (integer) $value : (string) $value;
		$cur->setting_type = $type;
		$cur->setting_label = $label;
		
		#If we are local, compare to global value
		if (!$global && $this->settingExists($id,true))
		{
			$g = $this->global_settings[$id];
			$same_setting = $g['value'] == $value && $g['type'] == $type && $g['label'] == $label;
			
			# Drop setting if same value as global
			if ($same_setting && $this->settingExists($id,false)) {
				$this->drop($id);
			} elseif ($same_setting) {
				return;
			}
		}
		
		if ($this->settingExists($id,$global))
		{
			if ($global) {
				$where = 'WHERE user_id IS NULL ';
			} else {
				$where = "WHERE user_id = '".$this->con->escape($this->user_id)."' ";
			}
			
			$cur->update($where."AND setting_id = '".$this->con->escape($id)."' ");
		}
		else
		{
			$cur->setting_id = $id;
			$cur->user_id = $global ? null : $this->user_id;
			
			$cur->insert();
		}
	}
	
	/**
	Removes an existing setting. Namespace 
	
	@param	id		<b>string</b>		Setting ID
	*/
	public function drop($id)
	{
		$strReq =	'DELETE FROM '.$this->table.' ';
		
		if ($this->user_id === null) {
			$strReq .= 'WHERE user_id IS NULL ';
		} else {
			$strReq .= "WHERE user_id = '".$this->con->escape($this->user_id)."' ";
		}
		
		$strReq .= "AND setting_id = '".$this->con->escape($id)."' ";
		
		$this->con->execute($strReq);
	}
	
	/**
	Returns setting value if exists.
	
	@param	n		<b>string</b>		Setting name
	@return	<b>mixed</b>
	*/
	public function get($n)
	{
		if (isset($this->settings[$n]['value'])) {
			return $this->settings[$n]['value'];
		}
		
		return null;
	}
	
	/**
	Magic __get method.
	@copydoc ::get
	*/
	public function __get($n)
	{
		return $this->get($n);
	}
	
	/**
	Sets a setting in $settings property. This sets the setting for script
	execution time only and if setting exists.
	
	@param	n		<b>string</b>		Setting name
	@param	v		<b>mixed</b>		Setting value
	*/
	public function set($n,$v)
	{
		if (isset($this->settings[$n])) {
			$this->settings[$n]['value'] = $v;
		} else {
			$this->settings[$n] = array(
				'value' => $v,
				'type' => gettype($n),
				'label' => '',
				'global' => false
			);
		}
	}
	
	/**
	Magic __set method.
	@copydoc ::set
	*/
	public function __set($n,$v)
	{
		$this->set($n,$v);
	}
	
	/**
	Returns $settings property content.
	
	@return	<b>array</b>
	*/
	public function dumpSettings()
	{
		return $this->settings;
	}
	
	/**
	Returns $global_settings property content.
	
	@return	<b>array</b>
	*/
	public function dumpGlobalSettings()
	{
		return $this->global_settings;
	}
	
}
?>
