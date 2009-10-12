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

/// @defgroup CB_DBLAYER Clearbricks Database Abstraction Layer
/// @ingroup CLEARBRICKS

require dirname(__FILE__).'/class.cursor.php';

/**
@ingroup CB_DBLAYER
@brief Clearbricks Database Abstraction Layer interface

All methods in this interface should be implemented in your database driver.

Database driver is a class that extends dbLayer, implements i_dbLayer and has
a name of the form (driver name)Connection.
*/
interface i_dbLayer
{
	/**
	This method should open a database connection and return a new resource
	link.
	
	@param	host		<b>string</b>		Database server host
	@param	user		<b>string</b>		Database user name
	@param	password	<b>string</b>		Database password
	@param	database	<b>string</b>		Database name
	@returns	<b>resource</b>
	*/
	function db_connect($host,$user,$password,$database);
	
	/**
	This method should open a persistent database connection and return a new
	resource link.
	
	@param	host		<b>string</b>		Database server host
	@param	user		<b>string</b>		Database user name
	@param	password	<b>string</b>		Database password
	@param	database	<b>string</b>		Database name
	@returns	<b>resource</b>
	*/
	function db_pconnect($host,$user,$password,$database);
	
	/**
	This method should close resource link.
	
	@param	handle	<b>resource</b>	Resource link
	*/
	function db_close($handle);
	
	/**
	This method should return database version number.
	
	@param	handle	<b>resource</b>	Resource link
	@returns	<b>string</b>
	*/
	function db_version($handle);
	
	/**
	This method should run an SQL query and return a resource result.
	
	@param	handle	<b>resource</b>	Resource link
	@param	query	<b>string</b>		SQL query string
	@return	<b>resource</b>
	*/
	function db_query($handle,$query);
	
	/**
	This method should run an SQL query and return a resource result.
	
	@param	handle	<b>resource</b>	Resource link
	@param	query	<b>string</b>		SQL query string
	@return	<b>resource</b>
	*/
	function db_exec($handle,$query);
	
	/**
	This method should return the number of fields in a result.
	
	@param	res		<b>resource</b>	Resource result
	@return	<b>integer</b>
	*/
	function db_num_fields($res);
	
	/**
	This method should return the number of rows in a result.
	
	@param	res		<b>resource</b>	Resource result
	@return	<b>integer</b>
	*/
	function db_num_rows($res);
	
	/**
	This method should return the name of the field at the given position
	<var>$position</var>.
	
	@param	res		<b>resource</b>	Resource result
	@param	position	<b>integer</b>		Field position
	@return	<b>string</b>
	*/
	function db_field_name($res,$position);
	
	/**
	This method should return the field type a the given position
	<var>$position</var>.
	
	@param	res		<b>resource</b>	Resource result
	@param	position	<b>integer</b>		Field position
	@return	<b>string</b>
	*/
	function db_field_type($res,$position);
	
	/**
	This method should fetch one line of result and return an associative array
	with field name as key and field value as value.
	
	@param	res		<b>resource</b>	Resource result
	@return	<b>array</b>
	*/
	function db_fetch_assoc($res);
	
	/**
	This method should move result cursor on given row position <var>$row</var>
	and return true on success.
	
	@param	res		<b>resource</b>	Resource result
	@param	row		<b>integer</b>		Row position
	@return	<b>boolean</b>
	*/
	function db_result_seek($res,$row);
	
	/**
	This method should return number of rows affected by INSERT, UPDATE or
	DELETE queries.
	
	@param	handle	<b>resource</b>	Resource link
	@param	res		<b>resource</b>	Resource result
	@return	<b>integer</b>
	*/
	function db_changes($handle,$res);
	
	/**
	This method should return the last error string for the current connection.
	
	@param	handle	<b>resource</b>	Resource link
	@return	<b>string</b>
	*/
	function db_last_error($handle);
	
	/**
	This method should return an escaped string for the current connection.
	
	@param	str		<b>string</b>		String to escape
	@param	handle	<b>resource</b>	Resource link
	@return	<b>string</b>
	*/
	function db_escape_string($str,$handle=null);
	
	/**
	This method lock the given table in write access.
	
	@param	table	<b>string</b>		Table name
	*/
	function db_write_lock($table);
	
	/**
	This method releases an acquiered lock.
	*/
	function db_unlock();
}

/**
@ingroup CB_DBLAYER
@brief Database Abstraction Layer class

Base class for database abstraction. Each driver extends this class and
implements i_dbLayer interface.
*/
class dbLayer
{
	protected $__driver = null;		///< <b>string</b>		Driver name
	protected $__version = null;		///< <b>string</b>		Database version
	
	protected $__link;				///< <b>resource</b>	Database resource link
	protected $__last_result;		///< <b>resource</b>	Last result resource
	
	/**
	Static function to use to init database layer. Returns a object extending
	dbLayer.
	
	@param	driver		<b>string</b>		Driver name
	@param	host			<b>string</b>		Database hostname
	@param	database		<b>string</b>		Database name
	@param	user			<b>string</b>		User ID
	@param	password		<b>string</b>		Password
	@param	persistent	<b>boolean</b>		Persistent connection (false)
	@return	<b>object</b>
	*/
	public static function init($driver,$host,$database,$user='',$password='',$persistent=false)
	{
		if (file_exists(dirname(__FILE__).'/class.'.$driver.'.php')) {
			require_once dirname(__FILE__).'/class.'.$driver.'.php';
			$driver_class = $driver.'Connection';
		} else {
			trigger_error('Unable to load DB layer for '.$driver,E_USER_ERROR);
			exit(1);
		}
		
		return new $driver_class($host,$database,$user,$password,$persistent);
	}
	
	/**
	Inits database connection.
	
	@param	host			<b>string</b>		User ID
	@param	database		<b>string</b>		Password
	@param	user			<b>string</b>		Server to connect
	@param	password		<b>string</b>		Database name
	@param	persistent	<b>boolean</b>		Open a persistent connection
	*/
	public function __construct($host,$database,$user='',$password='',$persistent=false)
	{
		if ($persistent) {
			$this->__link = $this->db_pconnect($host,$user,$password,$database);
		} else {
			$this->__link = $this->db_connect($host,$user,$password,$database);
		}
		
		$this->__version = $this->db_version($this->__link);
		$this->__database = $database;
	}
	
	/**
	Closes database connection.
	*/
	public function close()
	{
		$this->db_close($this->__link);
	}
	
	/**
	Returns database driver name
	
	@return	<b>string</b>
	*/
	public function driver()
	{
		return $this->__driver;
	}
	
	/**
	Returns database driver version
	
	@return	<b>string</b>
	*/
	public function version()
	{
		return $this->__version;
	}
	
	/**
	Returns current database name
	
	@return	<b>string</b>
	*/
	public function database()
	{
		return $this->__database;
	}
	
	/**
	Returns link resource
	
	@return	<b>resource</b>
	*/
	public function link()
	{
		return $this->__link;
	}
	
	/**
	Executes a query and return a recordset. Recordset could be either static
	(default beahvior) or dynamic (useful for large results).
	$query could be a string or an array of params for a previously prepared
	query.
	
	@param	sql		<b>string</b>		SQL query
	@return	<b>record</b>
	*/
	public function select($sql)
	{
		$result = $this->db_query($this->__link,$sql);
		
		$this->__last_result =& $result;
		
		$info = array();
		$info['con'] =& $this;
		$info['cols'] = $this->db_num_fields($result);
		$info['rows'] = $this->db_num_rows($result);
		$info['info'] = array();
		
		for ($i=0; $i<$info['cols']; $i++) {
			$info['info']['name'][] = $this->db_field_name($result,$i);
			$info['info']['type'][] = $this->db_field_type($result,$i);
		}
		
		return new record($result,$info);
	}
	
	/**
	Executes a query and return true if query succeded.
	
	@param	sql		<b>string</b>		SQL query
	@return	<b>boolean</b>
	*/
	public function execute($sql)
	{
		$result = $this->db_exec($this->__link,$sql);
		
		$this->__last_result =& $result;
		
		return true;
	}
	
	/**
	Begins a transaction.
	*/
	public function begin()
	{
		$this->execute('BEGIN');
	}
	
	/**
	Commits a transaction.
	*/
	public function commit()
	{
		$this->execute('COMMIT');
	}
	
	/**
	Rollbacks a transaction.
	*/
	public function rollback()
	{
		$this->execute('ROLLBACK');
	}
	
	/**
	This method lock the given table in write access.
	
	@param	table	<b>string</b>		Table name
	*/
	public function writeLock($table)
	{
		$this->db_write_lock($table);
	}
	
	/**
	This method releases an acquiered lock.
	*/
	public function unlock()
	{
		$this->db_unlock();
	}
	
	/**
	Vacuum the table given in argument.
	
	@param	table	<b>string</b>		Table name
	*/
	public function vacuum($table)
	{
	}
	
	/**
	Returns the number of lines affected by the last DELETE, INSERT or UPDATE
	query.
	
	@return	<b>integer</b>
	*/
	public function changes()
	{
		return $this->db_changes($this->__link,$this->__last_result);
	}
	
	/**
	Returns the last database error or false if no error.
	
	@returns <b>string</b>
	*/
	public function error()
	{
		$err = $this->db_last_error($this->__link);
		
		if (!$err) {
			return false;
		}
		
		return $err;
	}

	/**
	Returns a query fragment with date formater.
	
	The following modifiers are accepted:
	
	- %d : Day of the month, numeric
	- %H : Hour 24 (00..23)
	- %M : Minute (00..59)
	- %m : Month numeric (01..12)
	- %S : Seconds (00..59)
	- %Y : Year, numeric, four digits
	
	@param	field	<b>string</b>		Field name
	@param	pattern	<b>string</b>		Date format
	@return	<b>string</b>
	*/
	public function dateFormat($field,$pattern)
	{
		return
		'TO_CHAR('.$field.','."'".$this->escape($pattern)."') ";
	}
	
	/**
	Returns a LIMIT query fragment.
	
	@param	arg1		<b>mixed</b>		array or integer with limit intervals
	@param	arg2		<b>mixed</b>		integer or null (null)
	@return	<b>string</b>
	*/
	public function limit($arg1, $arg2=null)
	{
		if (is_array($arg1))
		{
			$arg1 = array_values($arg1);
			$arg2 = isset($arg1[1]) ? $arg1[1] : null;
			$arg1 = $arg1[0];
		}
		
		if ($arg2 === null) {
			$sql = ' LIMIT '.(integer) $arg1.' ';
		} else {
			$sql = ' LIMIT '.(integer) $arg2.' OFFSET '.$arg1.' ';
		}
		
		return $sql;
	}
	
	/**
	Returns a IN query fragment where $in could be an array, a string,
	an integer or null
	
	@param	in		<b>mixed</b>		array, string, integer or null
	@return	<b>string</b>
	*/
	public function in($in)
	{
		if (is_null($in))
		{
			return ' IN (NULL) ';
		}
		elseif (is_string($in))
		{
			return " IN ('".$this->escape($in)."') ";
		}
		elseif (is_array($in))
		{
			foreach ($in as $i => $v) {
				if (is_null($v)) {
					$in[$i] = 'NULL';
				} elseif (is_string($v)) {
					$in[$i] = "'".$this->escape($v)."'";
				}
			}
			return ' IN ('.implode(',',$in).') ';
		}
		else
		{
			return ' IN ( '.(integer) $in.') ';
		}
	}
	
	/**
	Returns SQL concatenation of methods arguments. Theses arguments
	should be properly escaped when needed.
	
	@return	<b>string</b>
	*/
	public function concat()
	{
		$args = func_get_args();
		return implode(' || ',$args);
	}
	
	/**
	Returns SQL protected string or array values.
	
	@param	i		<b>mixed</b>		String or array to protect
	@return	<b>mixed</b>
	*/
	public function escape($i)
	{
		if (is_array($i)) {
			foreach ($i as $k => $s) {
				$i[$k] = $this->db_escape_string($s,$this->__link);
			}
			return $i;
		}
		
		return $this->db_escape_string($i,$this->__link);
	}
	
	/**
	Returns SQL system protected string.
	
	@param	str		<b>string</b>		String to protect
	@return	<b>string</b>
	*/
	public function escapeSystem($str)
	{
		return '"'.$str.'"';
	}
	
	/**
	Returns a new instance of cursor class on <var>$table</var> for the current
	connection.
	
	@param	table	<b>string</b>		Cursor table
	@return	<b>cursor</b>
	*/
	public function openCursor($table)
	{
		return new cursor($this,$table);
	}
}

/**
@ingroup CB_DBLAYER
@brief Query Result Record Class

This class acts as an iterator over database query result. It does not fetch
all results on instantiation and thus, depending on database engine, should not
fill PHP process memory.
*/
class record
{
	protected $__link;				///< <b>resource</b>	Database resource link
	protected $__result;			///< <b>resource</b>	Query result resource
	protected $__info;				///< <b>array</b>		Result information array
	protected $__extend = array();	///< <b>array</b>		List of static functions that extend record
	
	protected $__index = 0;			///< <b>integer</b>		Current result position
	protected $__row = false;		///< <b>array</b>		Current result row content
	private $__fetch = false;
	
	/**
	Creates class instance from result link and some informations.
	<var>$info</var> is an array with the following content:
	
	- con => database object instance
	- cols => number of columns
	- rows => number of rows
	- info
	  - name => an array with columns names
	  - type => an array with columns types
	
	@param	result	<b>resource</b>	Resource result
	@param	info		<b>array</b>		Information array
	*/
	public function __construct($result,$info)
	{
		$this->__result = $result;
		$this->__info = $info;
		$this->__link = $info['con']->link();
		$this->index(0);
	}
	
	/**
	Converts this record to a staticRecord instance.
	*/
	public function toStatic()
	{
		if ($this instanceof staticRecord) {
			return $this;
		}
		return new staticRecord($this->__result,$this->__info);
	}
	
	/**
	Magic call function. Calls function in $__extend array if exists, passing it
	self object and arguments.
	*/
	public function __call($f,$args)
	{
		if (isset($this->__extend[$f]))
		{
			array_unshift($args,$this);
			return call_user_func_array($this->__extend[$f],$args);
		}
		
		trigger_error('Call to undefined method record::'.$f.'()',E_USER_ERROR);
	}
	
	/**
	Magic get method. Alias for field().
	*/
	public function __get($n)
	{
		return $this->field($n);
	}
	
	/**
	Alias for field().
	*/
	public function f($n)
	{
		return $this->field($n);
	}
	
	/**
	Retrieve named <var>$n</var> field value.
	
	@param	n		<b>string</b>		Field name
	@return	<b>string</b>
	*/
	public function field($n)
	{
		return $this->__row[$n];
	}
	
	/**
	Returns true if a field exists.
	
	@param	n		<b>string</b>		Field name
	@return	<b>string</b>
	*/
	public function exists($n)
	{
		return isset($this->__row[$n]);
	}
	
	/**
	Extends this instance capabilities by adding all public static methods of
	<var>$class</var> to current instance. Class methods should take at least
	this record as first parameter.
	@see __call()
	
	@param	class	<b>string</b>		Class name
	*/
	public function extend($class)
	{
		if (!class_exists($class)) {
			return;
		}
		
		$c = new ReflectionClass($class);
		foreach ($c->getMethods() as $m) {
			if ($m->isStatic() && $m->isPublic()) {
				$this->__extend[$m->name] = array($class,$m->name);
			}
		}
	}
	
	private function setRow()
	{
		$this->__row = $this->__info['con']->db_fetch_assoc($this->__result);
		
		if ($this->__row !== false)
		{
			foreach ($this->__row as $k => $v) {
				$this->__row[] =& $this->__row[$k];
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	Returns the current index position (O is first) or move to <var>$row</var> if
	specified.
	
	@param	row		<b>integer</b>		Row number to move
	@return	<b>integer</b>
	*/
	public function index($row=null)
	{
		if ($row === null) {
			return $this->__index === null ? 0 : $this->__index;
		}
		
		if ($row < 0 || $row+1 > $this->__info['rows']) {
			return false;
		}
		
		if ($this->__info['con']->db_result_seek($this->__result,(integer) $row))
		{
			$this->__index = $row;
			$this->setRow();
			$this->__info['con']->db_result_seek($this->__result,(integer) $row);
			return true;
		}
		return false;
	}
	
	/**
	This method moves index to one position and return true until index is not
	the last one. You can use it to loop over record. Example:
	@code
	<?php
	while ($rs->fetch()) {
		echo $rs->field1;
	}
	?>
	@endcode
	
	@return	<b>boolean</b>
	*/
	public function fetch()
	{
		if (!$this->__fetch) {
			$this->__fetch = true;
			$i = -1;
		} else {
			$i = $this->__index;
		}
		
		if (!$this->index($i+1)) {
			$this->__fetch = false;
			$this->__index = 0;
			return false;
		}
		
		return true;
	}
	
	/**
	Moves index to first position.
	
	@return	<b>boolean</b>
	*/
	public function moveStart()
	{
		return $this->index(0);
	}
	
	/**
	Moves index to last position.
	
	@return	<b>boolean</b>
	*/
	public function moveEnd()
	{
		return $this->index($this->__info['rows']-1);
	}
	
	/**
	Moves index to next position.
	
	@return	<b>boolean</b>
	*/
	public function moveNext()
	{
		return $this->index($this->__index+1);
	}
	
	/**
	Moves index to previous position.
	
	@return	<b>boolean</b>
	*/
	public function movePrev()
	{
		return $this->index($this->__index-1);
	}
	
	/**
	Returns true if index is at last position.
	
	@return	<b>boolean</b>
	*/
	public function isEnd()
	{
		return $this->__index+1 == $this->count();
	}
	
	/**
	Returns true if index is at first position.
	
	@return	<b>boolean</b>
	*/
	public function isStart()
	{
		return $this->__index <= 0;
	}
	
	/**
	Returns true if record contains no result.
	
	@return	<b>boolean</b>
	*/
	public function isEmpty()
	{
		return $this->count() == 0;
	}
	
	/**
	Returns number of rows in record.
	
	@return	<b>integer</b>
	*/
	public function count()
	{
		return $this->__info['rows'];
	}
	
	/**
	Returns an array of columns, with name as key and type as value.
	
	@return	<b>array</b>
	*/
	public function columns()
	{
		return $this->__info['info']['name'];
	}
	
	/**
	Returns an array of all rows in record.
	
	@return	<b>array</b>
	*/
	public function rows()
	{
		return $this->getData();
	}
	
	/**
	Returns an array of all rows in record. This method is called by rows().
	
	@return	<b>array</b>
	*/
	protected function getData()
	{
		$res = array();
		
		if ($this->count() == 0) {
			return $res;
		}
		
		$this->__info['con']->db_result_seek($this->__result,0);
		while (($r = $this->__info['con']->db_fetch_assoc($this->__result)) !== false) {
			foreach ($r as $k => $v) {
				$r[] =& $r[$k];
			}
			$res[] = $r;
		}
		$this->__info['con']->db_result_seek($this->__result,$this->__index);
		
		return $res;
	}
}

/**
@ingroup CB_DBLAYER
@brief Query Result Static Record Class

Unlike record class, this one contains all results in an associative array.
*/
class staticRecord extends record
{
	public $__data = array();	///< <b>array</b>	Data array
	private $__sortfield;
	private $__sortsign;
	
	public function __construct($result,$info)
	{
		if (is_array($result))
		{
			$this->__info = $info;
			$this->__data = $result;
		}
		else
		{
			parent::__construct($result,$info);
			$this->__data = parent::getData();
		}
		
		unset($this->__link);
		unset($this->__result);
	}
	
	/**
	Returns a new instance of object from an associative array.
	
	@param	data		<b>array</b>		Data array
	@return	<b>staticRecord</b>
	*/
	public static function newFromArray($data)
	{
		if (!is_array($data)) {
			$data = array();
		}
		
		$data = array_values($data);
		
		if (empty($data) || !is_array($data[0])) {
			$cols = 0;
		} else {
			$cols = count($data[0]);
		}
		
		$info = array(
			'con' => null,
			'info' => null,
			'cols' => $cols,
			'rows' => count($data)
		);
		
		return new self($data,$info);
	}
	
	public function field($n)
	{
		return $this->__data[$this->__index][$n];
	}
	
	public function index($row=null)
	{
		if ($row === null) {
			return $this->__index;
		}
		
		if ($row < 0 || $row+1 > $this->__info['rows']) {
			return false;
		}
		
		$this->__index = $row;
		return true;
	}
	
	public function rows()
	{
		return $this->__data;
	}
	
	/**
	Changes value of a given field in the current row.
	
	@param	n	<b>string</b>		Field name
	@param	v	<b>mixed</b>		Field value
	*/
	public function set($n,$v)
	{
		if ($this->__index === null) {
			return false;
		}
		
		$this->__data[$this->__index][$n] = $v;
	}
	
	/**
	Sorts values by a field in a given order.
	
	@param	field	<b>string</b>		Field name
	@param	order	<b>string</b>		Sort type (asc or desc)
	*/
	public function sort($field,$order='asc')
	{
		if (!isset($this->__data[0][$field])) {
			return false;
		}
		
		$this->__sortfield = $field;
		$this->__sortsign = strtolower($order) == 'asc' ? 1 : -1;
		
		usort($this->__data,array($this,'sortCallback'));
		
		$this->__sortfield = null;
		$this->__sortsign = null;
	}
	
	private function sortCallback($a,$b)
	{
		$a = $a[$this->__sortfield];
		$b = $b[$this->__sortfield];
		
		# Integer values
		if ($a == (string) (integer) $a && $b == (string) (integer) $b) {
			$a = (integer) $a;
			$b = (integer) $b;
			return ($a - $b) * $this->__sortsign;
		}
		
		return strcmp($a,$b) * $this->__sortsign;
	}
}
?>