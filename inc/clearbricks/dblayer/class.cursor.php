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

class cursor
{
	private $__con;
	private $__data = array();
	private $__table;
	
	public function __construct(&$con,$table)
	{
		$this->__con =& $con;
		$this->setTable($table);
	}
	
	public function setTable($table)
	{
		$this->__table = $table;
		$this->__data = array();
	}
	
	public function setField($n,$v)
	{
		$this->__data[$n] = $v;
	}
	
	public function unsetField($n)
	{
		unset($this->__data[$n]);
	}
	
	public function isField($n)
	{
		return isset($this->__data[$n]);
	}
	
	public function getField($n)
	{
		if (isset($this->__data[$n])) {
			return $this->__data[$n];
		}
		
		return null;
	}
	
	public function __set($n,$v)
	{
		$this->setField($n,$v);
	}
	
	public function __get($n)
	{
		return $this->getField($n);
	}
	
	public function clean()
	{
		$this->__data = array();
	}
	
	private function formatFields()
	{
		$data = array();
		
		foreach ($this->__data as $k => $v)
		{
			$k = $this->__con->escapeSystem($k);
			
			if (is_null($v)) {
				$data[$k] = 'NULL';
			} elseif (is_string($v)) {
				$data[$k] = "'".$this->__con->escape($v)."'";
			} elseif (is_array($v)) {
				$data[$k] = $v[0];
			} else {
				$data[$k] = $v;
			}
		}
		
		return $data;
	}
	
	public function getInsert()
	{
		$data = $this->formatFields();
		
		$insReq = 'INSERT INTO '.$this->__con->escapeSystem($this->__table)." (\n".
				implode(",\n",array_keys($data))."\n) VALUES (\n".
				implode(",\n",array_values($data))."\n) ";
		
		return $insReq;
	}
	
	public function getUpdate($where)
	{
		$data = $this->formatFields();
		$fields = array();
		
		$updReq = 'UPDATE '.$this->__con->escapeSystem($this->__table)." SET \n";
		
		foreach ($data as $k => $v) {
			$fields[] = $k.' = '.$v."";
		}
		
		$updReq .= implode(",\n",$fields);
		$updReq .= "\n".$where;
		
		return $updReq;
	}
	
	public function insert()
	{
		if (!$this->__table) {
			throw new Exception('No table name.');
		}
		
		$insReq = $this->getInsert();
		
		$this->__con->execute($insReq);
		
		return true;
	}
	
	public function update($where)
	{
		if (!$this->__table) {
			throw new Exception('No table name.');
		}
		
		$updReq = $this->getUpdate($where);
		
		$this->__con->execute($updReq);
		
		return true;
	}
}
?>