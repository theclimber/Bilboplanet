<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2007 Olivier Meunier and contributors.
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

class xmlrpcException extends Exception
{
	public function __construct($message,$code=0)
	{
		parent::__construct($message,$code);
	}
}

class xmlrpcValue
{
	protected  $data;
	protected  $type;
	
	public function __construct($data, $type = false)
	{
		$this->data = $data;
		if (!$type) {
			$type = $this->calculateType();
		}
		$this->type = $type;
		if ($type == 'struct') {
			# Turn all the values in the array in to new xmlrpcValue objects
			foreach ($this->data as $key => $value) {
				$this->data[$key] = new xmlrpcValue($value);
			}
		}
		if ($type == 'array') {
			for ($i = 0, $j = count($this->data); $i < $j; $i++) {
				$this->data[$i] = new xmlrpcValue($this->data[$i]);
			}
		}
	}
	
	public function getXml()
	{
		# Return XML for this value
		switch ($this->type)
		{
			case 'boolean':
				return '<boolean>'.(($this->data) ? '1' : '0').'</boolean>';
				break;
			case 'int':
				return '<int>'.$this->data.'</int>';
				break;
			case 'double':
				return '<double>'.$this->data.'</double>';
				break;
			case 'string':
				return '<string>'.htmlspecialchars($this->data).'</string>';
				break;
			case 'array':
				$return = '<array><data>'."\n";
				foreach ($this->data as $item) {
					$return .= '  <value>'.$item->getXml()."</value>\n";
				}
				$return .= '</data></array>';
				return $return;
				break;
			case 'struct':
				$return = '<struct>'."\n";
				foreach ($this->data as $name => $value) {
					$return .= "  <member><name>$name</name><value>";
					$return .= $value->getXml()."</value></member>\n";
				}
				$return .= '</struct>';
				return $return;
				break;
			case 'date':
			case 'base64':
				return $this->data->getXml();
				break;
		}
		return false;
	}
	
	protected function calculateType()
	{
		if ($this->data === true || $this->data === false) {
			return 'boolean';
		}
		if (is_integer($this->data)) {
			return 'int';
		}
		if (is_double($this->data)) {
			return 'double';
		}
		# Deal with xmlrpc object types base64 and date
		if (is_object($this->data) && $this->data instanceof xmlrpcDate) {
			return 'date';
		}
		if (is_object($this->data) && $this->data instanceof xmlrpcBase64) {
			return 'base64';
		}
		# If it is a normal PHP object convert it in to a struct
		if (is_object($this->data)) {
			$this->data = get_object_vars($this->data);
			return 'struct';
		}
		if (!is_array($this->data)) {
			return 'string';
		}
		# We have an array - is it an array or a struct ?
		if ($this->isStruct($this->data)) {
			return 'struct';
		} else {
			return 'array';
		}
	}
	
	protected function isStruct($array)
	{
		# Nasty function to check if an array is a struct or not
		$expected = 0;
		foreach ($array as $key => $value) {
			if ((string)$key != (string)$expected) {
				return true;
			}
			$expected++;
		}
		return false;
	}
}

class xmlrpcMessage
{
	protected $brutxml;
	protected $message;
	
	public $messageType;  # methodCall / methodResponse / fault
	public $faultCode;
	public $faultString;
	public $methodName;
	public $params;
	
	# Current variable stacks
	protected $_arraystructs = array();   # The stack used to keep track of the current array/struct
	protected $_arraystructstypes = array(); # Stack keeping track of if things are structs or array
	protected $_currentStructName = array();  # A stack as well
	protected $_param;
	protected $_value;
	protected $_currentTag;
	protected $_currentTagContents;
	
	# The XML parser
	protected $_parser;
	
	public function __construct($message)
	{
		$this->brutxml = $this->message = $message;
	}
	
	public function parse()
	{
		// first remove the XML declaration
		$this->message = preg_replace('/<\?xml(.*)?\?'.'>/', '', $this->message);
		
		if (trim($this->message) == '') {
			throw new Exception('XML Parser Error. Empty message');
		}
		
		$this->_parser = xml_parser_create();
		
		# Set XML parser to take the case of tags in to account
		xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
		
		# Set XML parser callback functions
		xml_set_object($this->_parser, $this);
		xml_set_element_handler($this->_parser, 'tag_open','tag_close');
		xml_set_character_data_handler($this->_parser, 'cdata');
		
		if (!xml_parse($this->_parser, $this->message))
		{
			$c = xml_get_error_code($this->_parser);
			$e = xml_error_string($c);
			$e .= ' on line '.xml_get_current_line_number($this->_parser);
			throw new Exception('XML Parser Error. '.$e,$c);
		}
		
		xml_parser_free($this->_parser);
		
		# Grab the error messages, if any
		if ($this->messageType == 'fault')
		{
			$this->faultCode = $this->params[0]['faultCode'];
			$this->faultString = $this->params[0]['faultString'];
		}
		return true;
	}
	
	protected function tag_open($parser,$tag,$attr)
	{
		$this->currentTag = $tag;
		
		switch($tag)
		{
			case 'methodCall':
			case 'methodResponse':
			case 'fault':
				$this->messageType = $tag;
				break;
			# Deal with stacks of arrays and structs
			case 'data':    # data is to all intents and puposes more interesting than array
				$this->_arraystructstypes[] = 'array';
				$this->_arraystructs[] = array();
				break;
			case 'struct':
				$this->_arraystructstypes[] = 'struct';
				$this->_arraystructs[] = array();
				break;
		}
	}
	
	protected function cdata($parser,$cdata)
	{
		$this->_currentTagContents .= $cdata;
	}
	
	protected function tag_close($parser,$tag)
	{
		$valueFlag = false;
		
		switch($tag)
		{
			case 'int':
			case 'i4':
				$value = (int)trim($this->_currentTagContents);
				$this->_currentTagContents = '';
				$valueFlag = true;
				break;
			case 'double':
				$value = (double)trim($this->_currentTagContents);
				$this->_currentTagContents = '';
				$valueFlag = true;
				break;
			case 'string':
				$value = (string)trim($this->_currentTagContents);
				$this->_currentTagContents = '';
				$valueFlag = true;
				break;
			case 'dateTime.iso8601':
				$value = new xmlrpcDate(trim($this->_currentTagContents));
				# $value = $iso->getTimestamp();
				$this->_currentTagContents = '';
				$valueFlag = true;
				break;
			case 'value':
				# "If no type is indicated, the type is string."
				if (trim($this->_currentTagContents) != '')
				{
					$value = (string)$this->_currentTagContents;
					$this->_currentTagContents = '';
					$valueFlag = true;
				}
				break;
			case 'boolean':
				$value = (boolean)trim($this->_currentTagContents);
				$this->_currentTagContents = '';
				$valueFlag = true;
				break;
			case 'base64':
				$value = base64_decode($this->_currentTagContents);
				$this->_currentTagContents = '';
				$valueFlag = true;
				break;
			# Deal with stacks of arrays and structs
			case 'data':
			case 'struct':
				$value = array_pop($this->_arraystructs);
				array_pop($this->_arraystructstypes);
				$valueFlag = true;
				break;
			case 'member':
				array_pop($this->_currentStructName);
				break;
			case 'name':
				$this->_currentStructName[] = trim($this->_currentTagContents);
				$this->_currentTagContents = '';
				break;
			case 'methodName':
				$this->methodName = trim($this->_currentTagContents);
				$this->_currentTagContents = '';
				break;
		}
		
		if ($valueFlag)
		{
			if (count($this->_arraystructs) > 0)
			{
				# Add value to struct or array
				if ($this->_arraystructstypes[count($this->_arraystructstypes)-1] == 'struct') {
					# Add to struct
					$this->_arraystructs[count($this->_arraystructs)-1][$this->_currentStructName[count($this->_currentStructName)-1]] = $value;
				} else {
					# Add to array
					$this->_arraystructs[count($this->_arraystructs)-1][] = $value;
				}
			}
			else
			{
				# Just add as a paramater
				$this->params[] = $value;
			}
		}
	}       
}

class xmlrpcRequest
{
	public $method;
	public $args;
	public $xml;
	
	function __construct($method, $args)
	{
		$this->method = $method;
		$this->args = $args;
		
		$this->xml =
		'<?xml version="1.0"?>'."\n".
		"<methodCall>\n".
		'  <methodName>'.$this->method."</methodName>\n".
		"  <params>\n";
		
		foreach ($this->args as $arg)
		{
			$this->xml .= '    <param><value>';
			$v = new xmlrpcValue($arg);
			$this->xml .= $v->getXml();
			$this->xml .= "</value></param>\n";
		}
		
		$this->xml .= '  </params></methodCall>';
	}
	
	public function getLength()
	{
		return strlen($this->xml);
	}
	
	public function getXml()
	{
		return $this->xml;
	}
}

class xmlrpcDate
{
	protected $year;
	protected $month;
	protected $day;
	protected $hour;
	protected $minute;
	protected $second;
	
	public function __construct($time)
	{
		# $time can be a PHP timestamp or an ISO one
		if (is_numeric($time)) {
			$this->parseTimestamp($time);
		} else {
			$this->parseTimestamp(strtotime($time));
		}
	}
	
	protected function parseTimestamp($timestamp)
	{
		$this->year = date('Y', $timestamp);
		$this->month = date('m', $timestamp);
		$this->day = date('d', $timestamp);
		$this->hour = date('H', $timestamp);
		$this->minute = date('i', $timestamp);
		$this->second = date('s', $timestamp);
		$this->ts = $timestamp;
	}
	
	public function getIso()
	{
		return $this->year.$this->month.$this->day.'T'.$this->hour.':'.$this->minute.':'.$this->second;
	}
	
	public function getXml()
	{
		return '<dateTime.iso8601>'.$this->getIso().'</dateTime.iso8601>';
	}
	
	public function getTimestamp()
	{
		return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
	}
}

class xmlrpcBase64
{
	protected $data;
	
	public function __construct($data)
	{
		$this->data = $data;
	}
	
	public function getXml()
	{
		return '<base64>'.base64_encode($this->data).'</base64>';
	}
}

class xmlrpcClient extends netHttp
{
	protected $request;
	protected $message;
	
	public function __construct($url)
	{
		if (!$this->readUrl($url,$ssl,$host,$port,$path,$user,$pass)) {
			return false;
		}
		
		parent::__construct($host,$port);
		$this->useSSL($ssl);
		$this->setAuthorization($user,$pass);
		
		$this->path = $path;
		$this->user_agent = 'Clearbricks XML/RPC Client';
	}
	
	public function query()
	{
		$args = func_get_args();
		$method = array_shift($args);
		$this->request = new xmlrpcRequest($method, $args);
		
		$this->doRequest();
		
		if ($this->status != 200) {
			throw new Exception('HTTP Error. '.$this->status.' '.$this->status_string);
		}
		
		# Now parse what we've got back
		$this->message = new xmlrpcMessage($this->content);
		$this->message->parse();
		
		# Is the message a fault?
		if ($this->message->messageType == 'fault')
		{
			throw new xmlrpcException($this->message->faultString,$this->message->faultCode);
		}
		
		return $this->message->params[0];
	}
	
	# Overloading netHttp::buildRequest method, we don't need all the stuff of
	# HTTP client.
	protected function buildRequest()
	{
		if ($this->proxy_host) {
			$path = $this->getRequestURL();
		} else {
			$path = $this->path;
		}
		
		return array(
			'POST '.$path.' HTTP/1.0',
			'Host: '.$this->host,
			'Content-Type: text/xml',
			'User-Agent: '.$this->user_agent,
			'Content-Length: '.$this->request->getLength(),
			'',
			$this->request->getXML()
		);
	}
}

class xmlrpcClientMulticall extends xmlrpcClient
{
	protected $calls = array();
	
	function __construct($url)
	{
		parent::__construct($url);
	}
	
	function addCall()
	{
		$args = func_get_args();
		$methodName = array_shift($args);
		
		$struct = array(
			'methodName' => $methodName,
			'params' => $args
		);
		
		$this->calls[] = $struct;
	}
	
	function query()
	{
		# Prepare multicall, then call the parent::query() method
		return parent::query('system.multicall',$this->calls);
	}
}

class xmlrpcServer
{
	protected $callbacks = array();
	protected $data;
	protected $encoding;
	protected $message;
	protected $capabilities;
	
	public $strict_check = false;
	
	public function __construct($callbacks=false,$data=false,$encoding='UTF-8')
	{
		$this->encoding = $encoding;
		$this->setCapabilities();
		if ($callbacks) {
			$this->callbacks = $callbacks;
		}
		$this->setCallbacks();
		$this->serve($data);
	}
	
	public function serve($data=false)
	{
		if (!$data)
		{
			try
			{
				# Check HTTP Method
				if ($_SERVER['REQUEST_METHOD'] != 'POST') {
					throw new Exception('XML-RPC server accepts POST requests only.',405);
				}
				
				# Check HTTP_HOST
				if (!isset($_SERVER['HTTP_HOST'])) {
					throw new Exception('No Host Specified',400);
				}
				
				global $HTTP_RAW_POST_DATA;
				if (!$HTTP_RAW_POST_DATA) {
					$HTTP_RAW_POST_DATA = @file_get_contents('php://input');
					if (!$HTTP_RAW_POST_DATA) {
						throw new Exception('No Message',400);
					}
				}
				
				if ($this->strict_check)
				{
					# Check USER_AGENT
					if (!isset($_SERVER['HTTP_USER_AGENT'])) {
						throw new Exception('No User Agent Specified',400);
					}
					
					# Check CONTENT_TYPE
					if (!isset($_SERVER['CONTENT_TYPE']) || strpos($_SERVER['CONTENT_TYPE'],'text/xml') !== 0) {
						throw new Exception('Invalid Content-Type',400);
					}
					
					# Check CONTENT_LENGTH
					if (!isset($_SERVER['CONTENT_LENGTH']) || $_SERVER['CONTENT_LENGTH'] != strlen($HTTP_RAW_POST_DATA)) {
						throw new Exception('Invalid Content-Lenth',400);
					}
				}
				
				$data = $HTTP_RAW_POST_DATA;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 400) {
					$this->head(400,'Bad Request');
				} elseif ($e->getCode() == 405) {
					$this->head(405,'Method Not Allowed');
					header('Allow: POST');
				}
				
				header('Content-Type: text/plain');
				echo $e->getMessage();
				exit;
			}
		}
		
		$this->message = new xmlrpcMessage($data);
		
		try
		{
			$this->message->parse();
			
			if ($this->message->messageType != 'methodCall') {
				throw new xmlrpcException('Server error. Invalid xml-rpc. not conforming to spec. Request must be a methodCall',-32600);
			}
			
			$result = $this->call($this->message->methodName,$this->message->params);
		}
		catch (Exception $e)
		{
			$this->error($e);
		}
		
		# Encode the result
		$r = new xmlrpcValue($result);
		$resultxml = $r->getXml();
		
		# Create the XML
		$xml =
		"<methodResponse>\n".
		"<params>\n".
		"<param>\n".
		"  <value>\n".
		'   '.$resultxml."\n".
		"  </value>\n".
		"</param>\n".
		"</params>\n".
		"</methodResponse>";
		
		# Send it
		$this->output($xml);
	}
	
	protected function head($code,$msg)
	{
		$status_mode = preg_match('/cgi/',PHP_SAPI);
		
		if ($status_mode) {
			header('Status: '.$code.' '.$msg);
		} else {
			if (version_compare(phpversion(),'4.3.0','>=')) {
				header($msg,true,$code);
			} else {
				header('HTTP/1.x '.$code.' '.$msg);
			}
		}
	}
	
	protected function call($methodname,$args)
	{
		if (!$this->hasMethod($methodname)) {
			throw new xmlrpcException('server error. requested method "'.$methodname.'" does not exist.',-32601);
		}
		
		$method = $this->callbacks[$methodname];
		
		# Perform the callback and send the response
		if (!is_callable($method)) {
			throw new xmlrpcException('server error. internal requested function for "'.$methodname.'" does not exist.',-32601);
		}
		
		return call_user_func_array($method,$args);
	}
	
	protected function error($e)
	{
		$msg = $e->getMessage();
		
		$this->output(
		"<methodResponse>\n".
		"  <fault>\n".
		"    <value>\n".
		"      <struct>\n".
		"        <member>\n".
		"          <name>faultCode</name>\n".
		'          <value><int>'.$e->getCode()."</int></value>\n".
		"        </member>\n".
		"        <member>\n".
		"          <name>faultString</name>\n".
		'          <value><string>'.$msg."</string></value>\n".
		"        </member>\n".
		"      </struct>\n".
		"    </value>\n".
		"  </fault>\n".
		"</methodResponse>\n"
		);
	}
	
	protected function output($xml)
	{
		$xml = '<?xml version="1.0" encoding="'.$this->encoding.'"?>'."\n".$xml;
		$length = strlen($xml);
		header('Connection: close');
		header('Content-Length: '.$length);
		header('Content-Type: text/xml');
		header('Date: '.date('r'));
		echo $xml;
		exit;
	}
	
	protected function hasMethod($method)
	{
		return in_array($method, array_keys($this->callbacks));
	}
	
	protected function setCapabilities()
	{
		# Initialises capabilities array
		$this->capabilities = array(
			'xmlrpc' => array(
				'specUrl' => 'http://www.xmlrpc.com/spec',
				'specVersion' => 1
			),
			'faults_interop' => array(
				'specUrl' => 'http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php',
				'specVersion' => 20010516
			),
			'system.multicall' => array(
				'specUrl' => 'http://www.xmlrpc.com/discuss/msgReader$1208',
				'specVersion' => 1
			),
		);   
	}
	
	protected function getCapabilities()
	{
		return $this->capabilities;
	}
	
	protected function setCallbacks()
	{
		$this->callbacks['system.getCapabilities'] = array($this,'getCapabilities');
		$this->callbacks['system.listMethods'] = array($this,'listMethods');
		$this->callbacks['system.multicall'] = array($this,'multiCall');
	}
	
	protected function listMethods()
	{
		# Returns a list of methods - uses array_reverse to ensure user defined
		# methods are listed before server defined methods
		return array_reverse(array_keys($this->callbacks));
	}
	
	protected function multiCall($methodcalls)
	{
		# See http://www.xmlrpc.com/discuss/msgReader$1208
		$return = array();
		foreach ($methodcalls as $call)
		{
			$method = $call['methodName'];
			$params = $call['params'];
			
			try
			{
				if ($method == 'system.multicall') {
					throw new xmlrpcException('Recursive calls to system.multicall are forbidden',-32600);
				}
				
				$result = $this->call($method, $params);
				$return[] = array($result);
			}
			catch (Exception $e)
			{
				$return[] = array(
					'faultCode' => $e->getCode(),
					'faultString' => $e->getMessage()
				);
			}
		}
		
		return $return;
	}
}

class xmlrpcIntrospectionServer extends xmlrpcServer
{
	protected $signatures;
	protected $help;
	
	public function __construct($encoding='UTF-8')
	{
		$this->encoding = $encoding;
		$this->setCallbacks();
		$this->setCapabilities();
		
		$this->capabilities['introspection'] = array (
			'specUrl' => 'http://xmlrpc.usefulinc.com/doc/reserved.html',
			'specVersion' => 1
		);
		
		$this->addCallback(
			'system.methodSignature', 
			array($this,'methodSignature'), 
			array('array','string'), 
			'Returns an array describing the return type and required parameters of a method'
		);
		
		$this->addCallback(
			'system.getCapabilities', 
			array($this,'getCapabilities'), 
			array('struct'), 
			'Returns a struct describing the XML-RPC specifications supported by this server'
		);
		
		$this->addCallback(
			'system.listMethods', 
			array($this,'listMethods'), 
			array('array'), 
			'Returns an array of available methods on this server'
		);
		
		$this->addCallback(
			'system.methodHelp', 
			array($this,'methodHelp'), 
			array('string','string'), 
			'Returns a documentation string for the specified method'
		);
		
		$this->addCallback(
			'system.multicall',
			array($this,'multiCall'),
			array('struct','array'),
			'Returns result of multiple methods calls'
		);
	}
	
	protected function addCallback($method, $callback, $args, $help)
	{
		$this->callbacks[$method] = $callback;
		$this->signatures[$method] = $args;
		$this->help[$method] = $help;
	}
	
	protected function call($methodname,$args)
	{
		# Make sure it's in an array
		if ($args && !is_array($args)) {
			$args = array($args);
		}
		
		# Over-rides default call method, adds signature check
		if (!$this->hasMethod($methodname)) {
			throw new xmlrpcException('Server error. Requested method "'.$methodname.'" not specified.',-32601);
		}
		
		$method = $this->callbacks[$methodname];
		$signature = $this->signatures[$methodname];
		
		if (!is_array($signature)) {
			throw new xmlrpcException('Server error. Wrong method signature',-36600);
		}
		
		$return_type = array_shift($signature);
		
		# Check the number of arguments
		if (count($args) > count($signature)) {
			throw new xmlrpcException('Server error. Wrong number of method parameters',-32602);
		}
		
		# Check the argument types
		if (!$this->checkArgs($args,$signature)) {
			throw new xmlrpcException('Server error. Invalid method parameters',-32602);
		}
		
		# It passed the test - run the "real" method call
		return parent::call($methodname, $args);
	}
	
	protected function checkArgs($args,$signature)
	{
		for ($i = 0, $j = count($args); $i < $j; $i++)
		{
			$arg = array_shift($args);
			$type = array_shift($signature);
			
			switch ($type)
			{
				case 'int':
				case 'i4':
					if (is_array($arg) || !is_int($arg)) {
						return false;
					}
					break;
				case 'base64':
				case 'string':
					if (!is_string($arg)) {
						return false;
					}
					break;
				case 'boolean':
					if ($arg !== false && $arg !== true) {
						return false;
					}
					break;
				case 'float':
				case 'double':
					if (!is_float($arg)) {
						return false;
					}
					break;
				case 'date':
				case 'dateTime.iso8601':
					if (!($arg instanceof xmlrpcDate)) {
						return false;
					}
					break;
			}
		}
		return true;
	}
	
	protected function methodSignature($method)
	{
		if (!$this->hasMethod($method)) {
			throw new xmlrpcException('Server error. Requested method "'.$method.'" not specified.',-32601);
			
		}
		
		# We should be returning an array of types
		$types = $this->signatures[$method];
		$return = array();
		
		foreach ($types as $type)
		{
			switch ($type)
			{
				case 'string':
					$return[] = 'string';
					break;
				case 'int':
				case 'i4':
					$return[] = 42;
					break;
				case 'double':
					$return[] = 3.1415;
					break;
				case 'dateTime.iso8601':
					$return[] = new xmlrpcDate(time());
					break;
				case 'boolean':
					$return[] = true;
					break;
				case 'base64':
					$return[] = new xmlrpcBase64('base64');
					break;
				case 'array':
					$return[] = array('array');
					break;
				case 'struct':
					$return[] = array('struct' => 'struct');
					break;
			}
		}
		return $return;
	}
	
	protected function methodHelp($method)
	{
		return $this->help[$method];
	}
}
?>