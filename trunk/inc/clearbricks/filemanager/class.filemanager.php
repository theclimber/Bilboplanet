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

class filemanager
{
	public $root;
	public $root_url;
	protected $pwd;
	protected $exclude_list = array();
	protected $exclude_pattern = '';
	
	public $dir = array('dirs'=>array(),'files'=>array());
	
	public function __construct($root,$root_url='')
	{
		$this->root = $this->pwd = path::real($root);
		$this->root_url = $root_url;
		
		if (!preg_match('#/$#',$this->root_url)) {
			$this->root_url = $this->root_url.'/';
		}
		
		if (!$this->root) {
			throw new Exception('Invalid root directory.');
		}
	}
	
	public function chdir($dir)
	{
		$realdir = path::real($this->root.'/'.path::clean($dir));
		if (!$realdir || !is_dir($realdir)) {
			throw new Exception('Invalid directory.');
		}
		
		if ($this->isExclude($realdir)) {
			throw new Exception('Directory is excluded.');
		}
		
		$this->pwd = $realdir;
	}
	
	public function getPwd()
	{
		return $this->pwd;
	}
	
	public function writable()
	{
		if (!$this->pwd) {
			return false;
		}
		
		return is_writable($this->pwd);
	}
	
	public function addExclusion($f)
	{
		if (is_array($f))
		{
			foreach ($f as $v) {
				if (($V = path::real($v)) !== false) {
					$this->exclude_list[] = $V;
				}
			}
		}
		elseif (($F = path::real($f)) !== false)
		{
			$this->exclude_list[] = $F;
		}
	}
	
	protected function isExclude($f)
	{
		foreach ($this->exclude_list as $v)
		{
			if (strpos($f,$v) === 0) {
				return true;
			}
		}
		
		return false;
	}
	
	protected function isFileExclude($f)
	{
		if (!$this->exclude_pattern) {
			return false;
		}
		
		return preg_match($this->exclude_pattern,$f);
	}
	
	protected function inJail($f)
	{
		$f = path::real($f);
		
		if ($f !== false) {
			return preg_match('|^'.preg_quote($this->root,'|').'|',$f);
		}
		
		return false;
	}
	
	public function inFiles($f)
	{
		foreach ($this->dir['files'] as $v) {
			if ($v->relname == $f) {
				return true;
			}
		}
		return false;
	}
	
	public function getDir()
	{
		$dir = path::clean($this->pwd);
		
		$dh = @opendir($dir);
		
		if ($dh === false) {
			throw new Exception('Unable to read directory.');
		}
		
		$d_res = $f_res = array();
			
		while (($file = readdir($dh)) !== false)
		{
			$fname = $dir.'/'.$file;
			
			if ($this->inJail($fname) && !$this->isExclude($fname))
			{
				if (is_dir($fname) && $file != '.') {
					$tmp = new fileItem($fname,$this->root,$this->root_url);
					if ($file == '..') {
						$tmp->parent = true;
					}
					$d_res[] = $tmp;
				}
				
				if (is_file($fname) && strpos($file,'.') !== 0 && !$this->isFileExclude($file)) {
					$f_res[] = new fileItem($fname,$this->root,$this->root_url);
				}
			}
		}
		closedir($dh);
		
		$this->dir = array('dirs'=>$d_res,'files'=>$f_res);
		usort($this->dir['dirs'],array($this,'sortHandler'));
		usort($this->dir['files'],array($this,'sortHandler'));
	}
	
	public function getRootDirs()
	{
		$d = files::getDirList($this->root);
		
		$dir = array();
		
		foreach ($d['dirs'] as $v) {
			$dir[] = new fileItem($v,$this->root,$this->root_url);
		}
		
		return $dir;
	}
	
	public function uploadFile($tmp,$dest)
	{
		$dest = $this->pwd.'/'.path::clean($dest);
		
		if ($this->isFileExclude($dest)) {
			throw new Exception(__('Uploading this file is not allowed.'));
		}
		
		if (!$this->inJail(dirname($dest))) {
			throw new Exception(__('Destination directory is not in jail.'));
		}
		
		if (!is_writable(dirname($dest))) {
			throw new Exception(__('Cannot write in this directory.'));
		}
		
		if (@move_uploaded_file($tmp,$dest) === false) {
			throw new Exception(__('An error occurred while writing the file.'));
		}
		
		files::inheritChmod($dest);
		return path::real($dest);
	}
	
	public function uploadBits($name,$bits)
	{
		$dest = $this->pwd.'/'.path::clean($name);
		
		if ($this->isFileExclude($dest)) {
			throw new Exception(__('Uploading this file is not allowed.'));
		}
		
		if (!$this->inJail(dirname($dest))) {
			throw new Exception(__('Destination directory is not in jail.'));
		}
		
		if (!is_writable(dirname($dest))) {
			throw new Exception(__('Cannot write in this directory.'));
		}
		
		$fp = @fopen($dest,'wb');
		if ($fp === false) {
			throw new Exception(__('An error occurred while writing the file.'));
		}
		
		fwrite($fp,$bits);
		fclose($fp);
		files::inheritChmod($dest);
		
		return path::real($dest);
	}
	
	public function makeDir($d)
	{
		files::makeDir($this->pwd.'/'.path::clean($d)); 
	}
	
	public function moveFile($s,$d)
	{
		$s = $this->root.'/'.path::clean($s);
		$d = $this->root.'/'.path::clean($d);
		
		if (($s = path::real($s)) === false) {
			throw new Exception(__('Source file does not exist.'));
		}
		
		$dest_dir = path::real(dirname($d));
		
		if (!$this->inJail($s)) {
			throw new Exception(__('File is not in jail.'));
		}
		if (!$this->inJail($dest_dir)) {
			throw new Exception(__('File is not in jail.'));
		}
		
		if (!is_writable($dest_dir)) {
			throw new Exception(__('Destination directory is not writable.'));
		}
		
		if (@rename($s,$d) === false) {
			throw new Exception(__('Unable to rename file.'));
		}
	}
	
	public function removeItem($f)
	{
		$file = path::real($this->pwd.'/'.path::clean($f));
		
		if (is_file($file)) {
			$this->removeFile($f);
		} elseif (is_dir($file)) {
			$this->removeDir($f);
		}
	}
	
	public function removeFile($f)
	{
		$f = path::real($this->pwd.'/'.path::clean($f));
		
		if (!$this->inJail($f)) {
			throw new Exception(__('File is not in jail.'));
		}
		
		if (!files::isDeletable($f)) {
			throw new Exception(__('File cannot be removed.'));
		}
		
		if (@unlink($f) === false) {
			throw new Exception(__('File cannot be removed.'));
		}
	}
	
	public function removeDir($d)
	{
		$d = path::real($this->pwd.'/'.path::clean($d));
		
		if (!$this->inJail($d)) {
			throw new Exception(__('Directory is not in jail.'));
		}
		
		if (!files::isDeletable($d)) {
			throw new Exception(__('Directory cannot be removed.'));
		}
		
		if (@rmdir($d) === false) {
			throw new Exception(__('Directory cannot be removed.'));
		}
	}
	
	protected function sortHandler($a,$b)
	{
		if ($a->parent && !$b->parent || !$a->parent && $b->parent) {
			return ($a->parent) ? -1 : 1;
		}
		return strcasecmp($a->basename,$b->basename);
	}
}

class fileItem
{
	public $file;
	public $basename;
	public $dir;
	public $file_url;
	public $dir_url;
	public $extension;
	public $relname;
	public $parent = false;
	
	public $type;
	public $mtime;
	public $size;
	public $mode;
	public $uid;
	public $gid;
	public $w;
	public $d;
	public $x;
	public $f;
	public $del;
	
	public function __construct($file,$root,$root_url='')
	{
		$file = path::real($file);
		$stat = stat($file);
		$path = path::info($file);
		
		$rel = preg_replace('/^'.preg_quote($root,'/').'\/?/','',$file);
		
		$this->file = $file;
		$this->basename = $path['basename'];
		$this->dir = $path['dirname'];
		$this->relname = $rel;
		
		$this->file_url = str_replace('%2F','/',rawurlencode($rel));
		$this->file_url = $root_url.$this->file_url;
		
		$this->dir_url = dirname($this->file_url);
		$this->extension = $path['extension'];
		
		$this->mtime = $stat[9];
		$this->size = $stat[7];
		$this->mode = $stat[2];
		$this->uid = $stat[4];
		$this->gid = $stat[5];
		$this->w = is_writable($file);
		$this->d = is_dir($file);
		$this->f = is_file($file);
		$this->x = file_exists($file.'/.');
		$this->del = files::isDeletable($file);
		
		$this->type = $this->d ? null : files::getMimeType($file);
		$this->type_prefix = preg_replace('/^(.+?)\/.+$/','$1',$this->type);
	}
}
?>