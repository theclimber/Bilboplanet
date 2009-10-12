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

class socketMail
{
	public static $fp;
	public static $timeout = 10;
	public static $smtp_relay = null;
	
	public static function mail($to,$subject,$message,$headers=null)
	{
		$from = self::getFrom($headers);
		
		$H = 'Return-Path: <'.$from.">\r\n";
		
		$from_host = explode('@',$from);
		$from_host = $from_host[1];
		
		$to_host = explode('@',$to);
		$to_host = $to_host[1];
		
		if (self::$smtp_relay != null) {
			$mx = array(gethostbyname(self::$smtp_relay) => 1);
		} else {
			$mx = mail::getMX($to_host);
		}
		
		foreach ($mx as $h => $w)
		{
			self::$fp = @fsockopen($h,25,$errno,$errstr,self::$timeout);
			
			if (self::$fp !== false) {
				break;
			}
		}
		
		if (!is_resource(self::$fp)) {
			self::$fp = null;
			throw new Exception('Unable to open socket');
		}
		
		# We need to read the first line
		fgets(self::$fp);
		
		$data = '';
		# HELO cmd
		if (!self::cmd('HELO '.$from_host,$data)) {
			self::quit();
			throw new Exception($data);
		}
		
		# MAIL FROM: <...>
		if (!self::cmd('MAIL FROM: <'.$from.'>',$data)) {
			self::quit();
			throw new Exception($data);
		}
		
		# RCPT TO: <...>
		if (!self::cmd('RCPT TO: <'.$to.'>',$data)) {
			self::quit();
			throw new Exception($data);
		}
		
		# Compose mail and send it with DATA
		$H = 'Return-Path: <'.$from.">\r\n";
		$H .= 'To: <'.$to.">\r\n";
		$H .= 'Subject: '.$subject."\r\n";
		$H .= $headers."\r\n";
		
		$message = $H."\r\n\r\n".$message;
		
		if (!self::sendMessage($message,$data)) {
			self::quit();
			throw new Exception($data);
		}
		
		
		self::quit();
	}
	
	private static function getFrom($headers)
	{
		$f = '';
		
		if (preg_match('/^from: (.+?)$/msi',$headers,$m)) {
			$f = trim($m[1]);
		}
		
		if (preg_match('/(?:<)(.+?)(?:$|>)/si',$f,$m)) {
			$f = trim($m[1]);
		} elseif (preg_match('/^(.+?)\(/si',$f,$m)) {
			$f = trim($m[1]);
		} elseif (!text::isEmail($f)) {
			$f = trim(ini_get('sendmail_from'));
		}
		
		if (!$f) {
			throw new Exception('No valid from e-mail address');
		}
		
		return $f;
	}
	
	private static function cmd($out,&$data='')
	{
		fwrite(self::$fp,$out."\r\n");
		$data = self::data();
		
		if (substr($data,0,3) != '250') {
			return false;
		}
		
		return true;
	}
	
	private static function data()
	{
		$s='';
		stream_set_timeout(self::$fp, 2);
		
		for($i=0;$i<2;$i++) {
			$s .= fgets(self::$fp, 1024);
		}
		
		return $s;
	}
	
	private static function sendMessage($msg,&$data)
	{
		$msg .= "\r\n.";
		
		self::cmd('DATA',$data);
		
		if (substr($data,0,3) != '354') {
			return false;
		}
		
		return self::cmd($msg,$data);
	}
	
	private static function quit()
	{
		self::cmd('QUIT');
		fclose(self::$fp);
		self::$fp = null;
	}
}
?>