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
@nosubgrouping
@brief Authentication and user credentials management

bpAuth is a class used to handle everything related to user authentication
and credentials. Object is provided by dcCore $auth property.
*/
class bpAuth
{
	protected $core;		///< <b>dcCore</b> dcCore instance
	protected $con;		///< <b>connection</b> Database connection object

	protected $user_table;	///< <b>string</b>	User table name
	protected $perm_table;	///< <b>string</b>	Perm table name

	protected $user_id;					///< <b>string</b>		Current user ID
	protected $user_token;
	protected $user_info = array();		///< <b>array</b>		Array with user information
	protected $user_options = array();		///< <b>a<rray</b>		Array with user options
	protected $user_admin;				///< <b>boolean</b>		User is super admin
	protected $permissions = array();		///< <b>array</b>		Permissions for each blog
	protected $allow_pass_change = true;	///< <b>boolean</b>		User can change its password
	protected $blogs = array();			///< <b>array</b>		List of blogs on which the user has permissions
	public $blog_count = null;				///< <b>integer</b>		Count of user blogs

	protected $perm_types;	///< <b>array</b> Permission types

	/**
	Class constructor. Takes dcCore object as single argument.

	@param	core		<b>dcCore</b>		dcCore object
	*/
	public function __construct(&$core)
	{
		$this->core =& $core;
		$this->con =& $core->con;
		$this->user_table = $core->prefix.'user';
		$this->perm_table = $core->prefix.'permissions';

		$this->perm_types = array(
			'admin' => T_('administrator'),
			'usage' => T_('manage their own entries and comments'),
			'publish' => T_('publish entries and comments'),
			'delete' => T_('delete entries and comments'),
			'contentadmin' => T_('manage all entries and comments'),
			'categories' => T_('manage categories'),
			'media' => T_('manage their own media items'),
			'media_admin' => T_('manage all media items')
		);
	}

	/// @name Credentials and user permissions
	//@{
	/**
	Checks if user exists and can log in. <var>$pwd</var> argument is optionnal
	while you may need to check user without password. This method will create
	credentials and populate all needed object properties.

	@param	user_id	<b>string</b>		User ID
	@param	pwd		<b>string</b>		User password
	@param	user_key	<b>string</b>		User key check
	@return	<b>boolean</b>
	*/
	public function checkUser($user_id, $pwd=null, $user_key=null)
	{
		# Check user and password
		$strReq = 'SELECT user_id, user_pwd, user_email, '.
				'user_fullname, user_status, user_token, '.
				'user_lang '.
				'FROM '.$this->user_table.' '.
				"WHERE user_id = '".$this->con->escape($user_id)."' ";

		$rs = $this->con->select($strReq);

		if ($rs->isEmpty()) {
			#print "isEmpty";
			return false;
		}

		$rs->extend('rsExtUser');

		if ($pwd != '')
		{
			if (crypt::hmac('BP_MASTER_KEY',$pwd) != $rs->user_pwd) {
				#print crypt::hmac('BP_MASTER_KEY',$pwd)." != ".$rs->user_pwd;
				sleep(rand(2,5));
				return false;
			}
		}
		elseif ($user_key != '')
		{
			if (http::browserUID('BP_MASTER_KEY'.$rs->user_id.$rs->user_pwd) != $user_key) {
				return false;
			}
		}

		$this->user_id = $rs->user_id;
		$this->user_admin = false;
		$this->user_token = $rs->user_token;

		$this->user_info['user_pwd'] = $rs->user_pwd;
		$this->user_info['user_fullname'] = $rs->user_fullname;
		$this->user_info['user_email'] = $rs->user_email;
		$this->user_info['user_lang'] = $rs->user_lang;
		$this->user_info['user_status'] = $rs->user_status;
		$this->user_info['user_token'] = $rs->user_token;

		$strReq2 = 'SELECT permissions '.
				'FROM '.$this->perm_table.' '.
				"WHERE user_id = '".$rs->user_id."' ";
		$rs2 = $this->con->select($strReq2);
		$user_perms = json_decode($rs2->f('permissions'));
		if ($user_perms->{'role'} == 'god')
			$this->user_admin = true;
		return true;
	}

	/**
	This method only check current user password.

	@param	pwd		<b>string</b>		User password
	@return	<b>boolean</b>
	*/
	public function checkPassword($pwd)
	{
		if (!empty($this->user_info['user_pwd'])) {
			return $pwd == $this->user_info['user_pwd'];
		}

		return false;
	}

	/**
	This method checks if user session cookie exists

	@return	<b>boolean</b>
	*/
	public function sessionExists()
	{
		return isset($_COOKIE[BP_SESSION_NAME]);
	}

	/**
	This method checks user session validity.

	@return	<b>boolean</b>
	*/
	public function checkSession($uid=null)
	{
		$this->core->session->start();

		# If session does not exist, logout.
		if (!isset($_SESSION['sess_user_id'])) {
			$this->core->session->destroy();
			return false;
		}

		# Check here for user and IP address
		$this->checkUser($_SESSION['sess_user_id']);
		$uid = $uid ? $uid : http::browserUID('BP_MASTER_KEY');

		$user_can_log = $this->userID() !== null && $uid == $_SESSION['sess_browser_uid'];

		if (!$user_can_log) {
			$this->core->session->destroy();
			return false;
		}

		return true;
	}


	/**
	Returns true if user is allowed to change its password.

	@return	<b>boolean</b>
	*/
	public function allowPassChange()
	{
		return $this->allow_pass_change;
	}
	//@}

	/// @name User code handlers
	//@{
	public function getUserCode()
	{
		$code =
		pack('a32',$this->userID()).
		pack('H*',crypt::hmac('BP_MASTER_KEY',$this->getInfo('user_pwd')));
		return bin2hex($code);
	}

	public function checkUserCode($code)
	{
		$code = @pack('H*',$code);

		$user_id = trim(@pack('a32',substr($code,0,32)));
		$pwd = @unpack('H40hex',substr($code,32,40));

		if ($user_id === false || $pwd === false) {
			return false;
		}

		$pwd = $pwd['hex'];

		$strReq = 'SELECT user_id, user_pwd '.
				'FROM '.$this->user_table.' '.
				"WHERE user_id = '".$this->con->escape($user_id)."' ";

		$rs = $this->con->select($strReq);

		if ($rs->isEmpty()) {
			return false;
		}

		if (crypt::hmac('BP_MASTER_KEY',$rs->user_pwd) != $pwd) {
			return false;
		}

		return $rs->user_id;
	}


	/**
	Returns if user is super user

	@return	<b>string</b>
	*/
	public function superUser()
	{
		return $this->user_admin;
	}


	/**
	Returns current user ID

	@return	<b>string</b>
	*/
	public function userID()
	{
		return $this->user_id;
	}


	public function userToken()
	{
		return $this->user_token;
	}

	/**
	Returns information about a user .

	@param	n		<b>string</b>		Information name
	@return	<b>string</b> Information value
	*/
	public function getInfo($n)
	{
		if (isset($this->user_info[$n])) {
			return $this->user_info[$n];
		}

		return null;
	}

	/**
	Returns a specific user option

	@param	n		<b>string</b>		Option name
	@return	<b>string</b> Option value
	*/
	public function getOption($n)
	{
		if (isset($this->user_options[$n])) {
			return $this->user_options[$n];
		}
		return null;
	}

	/**
	Returns all user options in an associative array.

	@return	<b>array</b>
	*/
	public function getOptions()
	{
		return $this->user_options;
	}
	//@}

	/// @name Permissions
	//@{
	/**
	Returns an array with permissions parsed from the string <var>$level</var>

	@param	level	<b>string</b>		Permissions string
	@return	<b>array</b>
	*/
	public function parsePermissions($level)
	{
		$level = preg_replace('/^\|/','',$level);
		$level = preg_replace('/\|$/','',$level);

		$res = array();
		foreach (explode('|',$level) as $v) {
			$res[$v] = true;
		}
		return $res;
	}

	/**
	Returns <var>perm_types</var> property content.

	@return	<b>array</b>
	*/
	public function getPermissionsTypes()
	{
		return $this->perm_types;
	}

	/**
	Adds a new permission type.

	@param	name		<b>string</b>		Permission name
	@param	title	<b>string</b>		Permission title
	*/
	public function setPermissionType($name,$title)
	{
		$this->perm_types[$name] = $title;
	}
	//@}

	/// @name Password recovery
	//@{
	/**
	Add a recover key to a specific user identified by its email and
	password.

	@param	user_id		<b>string</b>		User ID
	@param	user_email	<b>string</b>		User Email
	@return	<b>string</b> Recover key
	*/
	public function setRecoverKey($user_id,$user_email)
	{
		$strReq = 'SELECT user_id '.
				'FROM '.$this->user_table.' '.
				"WHERE user_id = '".$this->con->escape($user_id)."' ".
				"AND user_email = '".$this->con->escape($user_email)."' ";

		$rs = $this->con->select($strReq);

		if ($rs->isEmpty()) {
			throw new Exception(T_('That user does not exists in the database.'));
		}

		$key = md5(uniqid());

		$cur = $this->con->openCursor($this->user_table);
		$cur->user_recover_key = $key;

		$cur->update("WHERE user_id = '".$this->con->escape($user_id)."'");

		return $key;
	}

	/**
	Creates a new user password using recovery key. Returns an array:

	- user_email
	- user_id
	- new_pass

	@param	recover_key	<b>string</b>		Recovery key
	@return	<b>array</b>
	*/
	public function recoverUserPassword($recover_key)
	{
		$strReq = 'SELECT user_id, user_email '.
				'FROM '.$this->user_table.' '.
				"WHERE user_recover_key = '".$this->con->escape($recover_key)."' ";

		$rs = $this->con->select($strReq);

		if ($rs->isEmpty()) {
			throw new Exception(T_('That key does not exists in the database.'));
		}

		$new_pass = crypt::createPassword();

		$cur = $this->con->openCursor($this->user_table);
		$cur->user_pwd = crypt::hmac('BP_MASTER_KEY',$new_pass);
		$cur->user_recover_key = null;

		$cur->update("WHERE user_recover_key = '".$this->con->escape($recover_key)."'");

		return array('user_email' => $rs->user_email, 'user_id' => $rs->user_id, 'new_pass' => $new_pass);
	}

}
?>
