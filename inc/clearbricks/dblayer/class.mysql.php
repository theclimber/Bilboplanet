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

/**
@ingroup CB_DBLAYER
@brief MySQL Database Driver.

See the dbLayer documentation for common methods.
*/
class mysqlConnection extends dbLayer implements i_dbLayer
{
	public static $weak_locks = false;
	
	protected $__driver = 'mysql';
	
	public function db_connect($host,$user,$password,$database)
	{
		if (!function_exists('mysql_connect')) {
			throw new Exception('PHP MySQL functions are not available');
		}
		
		if (($link = @mysql_connect($host,$user,$password,true)) === false) {
#			throw new Exception('Unable to connect to database');
			throw new Exception(mysql_error());
		}
		
		$this->db_post_connect($link,$database);
		
		return $link;
	}
	
	public function db_pconnect($host,$user,$password,$database)
	{
		if (!function_exists('mysql_pconnect')) {
			throw new Exception('PHP MySQL functions are not available');
		}
		
		if (($link = @mysql_pconnect($host,$user,$password)) === false) {
#			throw new Exception('Unable to connect to database');
			throw new Exception(mysql_error());
		}
		
		$this->db_post_connect($link,$database);
		
		return $link;
	}
	
	private function db_post_connect($link,$database)
	{
		if (@mysql_select_db($database,$link) === false) {
			throw new Exception('Unable to use database '.$database);
		}
		
		if (version_compare($this->db_version($link),'4.1','>='))
		{
			$this->db_query($link,'SET NAMES utf8');
			$this->db_query($link,'SET CHARACTER SET utf8');
			$this->db_query($link,"SET COLLATION_CONNECTION = 'utf8_general_ci'");
			$this->db_query($link,"SET COLLATION_SERVER = 'utf8_general_ci'");
			$this->db_query($link,"SET CHARACTER_SET_SERVER = 'utf8'");
			$this->db_query($link,"SET CHARACTER_SET_DATABASE = 'utf8'");
		}
	}
	
	public function db_close($handle)
	{
		if (is_resource($handle)) {
			mysql_close($handle);
		}
	}
	
	public function db_version($handle)
	{
		if (is_resource($handle)) {
			return mysql_get_server_info();
		}
		return null;
	}
	
	public function db_query($handle,$query)
	{
		if (is_resource($handle))
		{
			$res = @mysql_query($query,$handle);
			if ($res === false) {
				$e = new Exception($this->db_last_error($handle));
				$e->sql = $query;
				throw $e;
			}
			return $res;
		}
	}
	
	public function db_exec($handle,$query)
	{
		return $this->db_query($handle,$query);
	}
	
	public function db_num_fields($res)
	{
		if (is_resource($res)) {
			return mysql_num_fields($res);
		}
		return 0;
	}
	
	public function db_num_rows($res)
	{
		if (is_resource($res)) {
			return mysql_num_rows($res);
		}
		return 0;
	}
	
	public function db_field_name($res,$position)
	{
		if (is_resource($res)) {
			return mysql_field_name($res,$position);
		}
	}
	
	public function db_field_type($res,$position)
	{
		if (is_resource($res)) {
			return mysql_field_type($res,$position);
		}
	}
	
	public function db_fetch_assoc($res)
	{
		if (is_resource($res)) {
			return mysql_fetch_assoc($res);
		}
	}
	
	public function db_result_seek($res,$row)
	{
		if (is_resource($res)) {
			return mysql_data_seek($res,$row);
		}
	}
	
	public function db_changes($handle,$res)
	{
		if (is_resource($handle)) {
			return mysql_affected_rows($handle);
		}
	}
	
	public function db_last_error($handle)
	{
		if (is_resource($handle))
		{
			$e = mysql_error($handle);
			if ($e) {
				return $e.' ('.mysql_errno($handle).')';
			}
		}		
		return false;
	}
	
	public function db_escape_string($str,$handle=null)
	{
		if (is_resource($handle)) {
			return mysql_real_escape_string($str,$handle);
		} else {
			return mysql_escape_string($str);
		}
	}
	
	public function db_write_lock($table)
	{
		try {
			$this->execute('LOCK TABLES '.$this->escapeSystem($table).' WRITE');
		} catch (Exception $e) {
			# As lock is a privilege in MySQL, we can avoid errors with weak_locks static var
			if (!self::$weak_locks) {
				throw $e;
			}
		}
	}
	
	public function db_unlock()
	{
		try {
			$this->execute('UNLOCK TABLES');
		} catch (Exception $e) {
			if (!self::$weak_locks) {
				throw $e;
			}
		}
	}
	
	public function vacuum($table)
	{
		$this->execute('OPTIMIZE TABLE '.$this->escapeSystem($table));
	}
	
	public function dateFormat($field,$pattern)
	{
		$pattern = str_replace('%M','%i',$pattern);
		
		return 'DATE_FORMAT('.$field.','."'".$this->escape($pattern)."') ";
	}
	
	public function concat()
	{
		$args = func_get_args();
		return 'CONCAT('.implode(',',$args).')';
	}
	
	public function escapeSystem($str)
	{
		return '`'.$str.'`';
	}
}
?>
