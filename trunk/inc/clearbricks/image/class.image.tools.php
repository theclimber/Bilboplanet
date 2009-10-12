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

# Some functions taken from BIG
# https://dev.media-box.net/big/

class imageTools
{
	public $res;
	public $memory_limit = null;
	
	public function __construct()
	{
		if (!function_exists('imagegd2')) {
			throw new Exception('GD is not installed');
		}
		$this->res = null;
	}
	
	public function close()
	{
		if (is_resource($this->res)) {
			imagedestroy($this->res);
		}
		
		if ($this->memory_limit) {
			ini_set('memory_limit',$this->memory_limit);
		}
	}
	
	public function loadImage($f)
	{
		if (!file_exists($f)) {
			throw new Exception('Image doest not exists');
		}
		
		if (($info = @getimagesize($f)) !== false)
		{
			$this->memoryAllocate($info[0],$info[1]);
			
			switch ($info[2])
			{
				case 3 :
					$this->res = @imagecreatefrompng($f);
					if (is_resource($this->res)) {
						@imagealphablending($this->res);
					}
					break;
				case 2 :
					$this->res = @imagecreatefromjpeg($f);
					break;
				case 1 :
					$this->res = @imagecreatefromgif($f);
					break;
			}
		}
		
		if (!is_resource($this->res)) {
			throw new Exception('Unable to load image');
		}
	}
	
	public function getW()
	{
		return imagesx($this->res);
	}
	
	public function getH()
	{
		return imagesy($this->res);
	}
	
	public function memoryAllocate($w,$h)
	{
		$mem_used = function_exists('memory_get_usage') ? @memory_get_usage() : 4000000;
		$mem_limit = @ini_get('memory_limit');
		if ($mem_used || $mem_limit)
		{
			$mem_limit = files::str2bytes($mem_limit);
			$mem_avail = $mem_limit-$mem_used-(512*1024);
			$mem_needed = $w*$h*8;
			
			if ($mem_needed > $mem_avail)
			{
				if (@ini_set('memory_limit',$mem_limit+$mem_needed+$mem_used) === false) {
					throw new Exception(__('Not enough memory to open image.'));
				}
				
				if (!$this->memory_limit) {
					$this->memory_limit = $mem_limit;
				}
			}
		}
	}
	
	function output($type='png',$file=null,$qual=90)
	{
		if (!$file)
		{
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Pragma: no-cache');
			switch (strtolower($type))
			{
				case 'png' :
					header('Content-type: image/png');
					imagepng($this->res);
					return true;
				case 'jpeg' :
				case 'jpg':
					header('Content-type: image/jpeg');
					imagejpeg($this->res,null,$qual);
					return true;
				default :
					return false;
			}
		}
		elseif (is_writable(dirname($file)))
		{
			switch(strtolower($type))
			{
				case 'png' :
					return imagepng($this->res,$file);
				case 'jpeg' :
				case 'jpg' :
					return imagejpeg($this->res,$file,$qual);
				default :
					return false;
			}
		}
		return false;
	}
	
	/**
	@function resize
	
	Resize image ressource
	
	@param mixed	WIDTH		Image width (px or percent)
	@param mixed	HEIGHT		Image height (px or percent)
	@param string	mode			Crop mode (force, crop, ratio)
	@param boolean	EXPAND		Allow resize of image
	*/
	function resize($WIDTH,$HEIGHT,$MODE='ratio',$EXPAND=false)
	{
		
		$imgWidth=$this->getW();
		$imgHeight=$this->getH();
		
		if(strpos($WIDTH,'%',0))
		$WIDTH=$imgWidth*$WIDTH/100;
		if(strpos($HEIGHT,'%',0))
		$HEIGHT=$imgHeight*$HEIGHT/100;
		
		$ratio=$imgWidth/$imgHeight;
		
		// guess resize ($_w et $_h)
		if($MODE=='ratio')
		{
			$_w=99999;
			if($HEIGHT>0)
			{
				$_h=$HEIGHT;
				$_w=$_h*$ratio;
			}
			if($WIDTH>0 && $_w>$WIDTH)
			{
				$_w=$WIDTH;
				$_h=$_w/$ratio;
			}
			
			if(!$EXPAND && $_w>$imgWidth)
			{
				$_w=$imgWidth;
				$_h=$imgHeight;
			}
		}
		else
		{
			// crop source image
			$_w=$WIDTH;
			$_h=$HEIGHT;
		}
		
		if($MODE=='force')
		{
			if($WIDTH>0)
			$_w=$WIDTH;
			else
			$_w=$HEIGHT*$ratio;
			
			if($HEIGHT>0)
			$_h=$HEIGHT;
			else
			$_h=$WIDTH/$ratio;
			
			if(!$EXPAND && $_w>$imgWidth)
			{
				$_w=$imgWidth;
				$_h=$imgHeight;
			}
			
			$cropW=$imgWidth;
			$cropH=$imgHeight;
			$decalW=0;
			$decalH=0;
		}
		else
		{
			// guess real viewport of image
			$innerRatio=$_w/$_h;
			if($ratio>=$innerRatio)
			{
				$cropH=$imgHeight;
				$cropW=$imgHeight*$innerRatio;
				$decalH=0;
				$decalW=($imgWidth-$cropW)/2;
			}
			else
			{
				$cropW=$imgWidth;
				$cropH=$imgWidth/$innerRatio;
				$decalW=0;
				$decalH=($imgHeight-$cropH)/2;
			}
		}
		
		if ($_w < 1) {
			$_w = 1;
		}
		if ($_h < 1) {
			$_h = 1;
		}
		
		$this->memoryAllocate($_w,$_h);
		$dest = imagecreatetruecolor($_w,$_h);
		$fill = imagecolorallocate($dest,128,128,128);
		imagefill($dest,0,0,$fill);
		imagecopyresampled($dest,$this->res,0,0,$decalW,$decalH,$_w,$_h,$cropW,$cropH);
		imagedestroy($this->res);
		$this->res = $dest;
		return true;
	}
}
?>