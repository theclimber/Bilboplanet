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
@defgroup DC_CORE Dotclear Core Classes
*/

/**
@ingroup DC_CORE
@nosubgrouping
@brief Dotclear core class

True to its name dcCore is the core of Dotclear. It handles everything related
to blogs, database connection, plugins...
*/
class bpCore
{
	public $con;		///< <b>connection</b>		Database connection object
	public $prefix;	///< <b>string</b>			Database tables prefix
	public $blog;		///< <b>dcBlog</b>			dcBlog object
	public $error;		///< <b>dcError</b>			dcError object
	public $auth;		///< <b>bpAuth</b>			bpAuth object
	public $session;	///< <b>sessionDB</b>		sessionDB object
	public $url;		///< <b>urlHandler</b>		urlHandler object
	public $wiki2xhtml;	///< <b>wiki2xhtml</b>		wiki2xhtml object
	public $plugins;	///< <b>dcModules</b>		dcModules object
	public $media;		///< <b>dcMedia</b>			dcMedia object
	public $rest;		///< <b>dcRestServer</b>		dcRestServer object

	private $versions = null;
	private $formaters = array();
	private $behaviors = array();
	private $post_types = array();

	/**
	dcCore constructor inits everything related to Dotclear. It takes arguments
	to init database connection.

	@param	driver	<b>string</b>	Database driver name
	@param	host		<b>string</b>	Database hostname
	@param	db		<b>string</b>	Database name
	@param	user		<b>string</b>	Database username
	@param	password	<b>string</b>	Database password
	@param	prefix	<b>string</b>	DotClear tables prefix
	@param	persist	<b>boolean</b>	Persistent database connection
	*/
	public function __construct($driver, $host, $db, $user, $password, $prefix, $persist)
	{
		$this->con = dbLayer::init($driver,$host,$db,$user,$password,$persist);

		# define weak_locks for mysql
		if ($this->con instanceof mysqlConnection) {
			mysqlConnection::$weak_locks = true;
		}

		$this->prefix = $prefix;

		$this->error = new dcError();
		$this->auth = $this->authInstance();
		$this->session = new sessionDB($this->con,$this->prefix.'session',BP_SESSION_NAME,'',null,false);
		$this->url = new urlHandler();

		$this->rest = new dcRestServer($this);

		# Create the Hyla_Tpl object
		$this->tpl = new Hyla_Tpl();
		$this->tpl->setL10nCallback('T_');
	}

	private function authInstance()
	{
		# You can set DC_AUTH_CLASS to whatever you want.
		# Your new class *should* inherits bpAuth.
		if (!defined('DC_AUTH_CLASS')) {
			$c = 'bpAuth';
		} else {
			$c = DC_AUTH_CLASS;
		}

		if (!class_exists($c)) {
			throw new Exception('Authentication class '.$c.' does not exists.');
		}

		if ($c != 'bpAuth')
		{
			$r = new ReflectionClass($c);
			$p = $r->getParentClass();

			if (!$p || $p->name != 'bpAuth') {
				throw new Exception('Authentication class '.$c.' does not inherit bpAuth.');
			}
		}

		return new $c($this);
	}



	/// @name Admin nonce secret methods
	//@{

	public function getNonce()
	{
		return crypt::hmac('BP_MASTER_KEY',session_id());
	}

	public function checkNonce($secret)
	{
		if (!preg_match('/^([0-9a-f]{40,})$/i',$secret)) {
			return false;
		}

		return $secret == crypt::hmac('BP_MASTER_KEY',session_id());
	}

	public function formNonce()
	{
		if (!session_id()) {
			return;
		}

		return form::hidden(array('xd_check'),$this->getNonce());
	}
	//@}


	/// @name Text Formatters methods
	//@{
	/**
	Adds a new text formater which will call the function <var>$func</var> to
	transform text. The function must be a valid callback and takes one
	argument: the string to transform. It returns the transformed string.

	@param	name		<b>string</b>		Formater name
	@param	func		<b>callback</b>	Function to use, must be a valid and callable callback
	*/
	public function addFormater($name,$func)
	{
		if (is_callable($func)) {
			$this->formaters[$name] = $func;
		}
	}

	/**
	Returns formaters list.

	@return	<b>array</b> An array of formaters names in values.
	*/
	public function getFormaters()
	{
		return array_keys($this->formaters);
	}

	/**
	If <var>$name</var> is a valid formater, it returns <var>$str</var>
	transformed using that formater.

	@param	name		<b>string</b>		Formater name
	@param	str		<b>string</b>		String to transform
	@return	<b>string</b>	String transformed
	*/
	public function callFormater($name,$str)
	{
		if (isset($this->formaters[$name])) {
			return call_user_func($this->formaters[$name],$str);
		}

		return $str;
	}
	//@}


	/// @name Behaviors methods
	//@{
	/**
	Adds a new behavior to behaviors stack. <var>$func</var> must be a valid
	and callable callback.

	@param	behavior	<b>string</b>		Behavior name
	@param	func		<b>callback</b>	Function to call
	*/
	public function addBehavior($behavior,$func)
	{
		if (is_callable($func)) {
			$this->behaviors[$behavior][] = $func;
		}
	}

	/**
	Tests if a particular behavior exists in behaviors stack.

	@param	behavior	<b>string</b>	Behavior name
	@return	<b>boolean</b>
	*/
	public function hasBehavior($behavior)
	{
		return isset($this->behaviors[$behavior]);
	}

	/**
	Get behaviors stack (or part of).

	@param	behavior	<b>string</b>		Behavior name
	@return	<b>array</b>
	*/
	public function getBehaviors($behavior='')
	{
		if (empty($this->behaviors)) return null;

		if ($behavior == '') {
			return $this->behaviors;
		} elseif (isset($this->behaviors[$behavior])) {
			return $this->behaviors[$behavior];
		}

		return array();
	}

	/**
	Calls every function in behaviors stack for a given behavior and returns
	concatened result of each function.

	Every parameters added after <var>$behavior</var> will be pass to
	behavior calls.

	@param	behavior	<b>string</b>	Behavior name
	@return	<b>string</b> Behavior concatened result
	*/
	public function callBehavior($behavior)
	{
		if (isset($this->behaviors[$behavior]))
		{
			$args = func_get_args();
			array_shift($args);

			$res = '';

			foreach ($this->behaviors[$behavior] as $f) {
				$res .= call_user_func_array($f,$args);
			}

			return $res;
		}
	}
	//@}

	/// @name Post types URLs management
	//@{
	public function getPostAdminURL($type,$post_id,$escaped=true)
	{
		if (!isset($this->post_types[$type])) {
			$type = 'post';
		}

		$url = sprintf($this->post_types[$type]['admin_url'],$post_id);
		return $escaped ? html::escapeURL($url) : $url;
	}

	public function getPostPublicURL($type,$post_url,$escaped=true)
	{
		if (!isset($this->post_types[$type])) {
			$type = 'post';
		}

		$url = sprintf($this->post_types[$type]['public_url'],$post_url);
		return $escaped ? html::escapeURL($url) : $url;
	}

	public function setPostType($type,$admin_url,$public_url)
	{
		$this->post_types[$type] = array(
			'admin_url' => $admin_url,
			'public_url' => $public_url
		);
	}

	public function getPostTypes()
	{
		return $this->post_types;
	}
	//@}



	/// @name Users management methods
	//@{
	/**
	Returns a user by its ID.

	@param	id		<b>string</b>		User ID
	@return	<b>record</b>
	*/
	public function getUser($id)
	{
		$params['user_id'] = $id;

		return $this->getUsers($params);
	}

	/**
	Returns a users list. <b>$params</b> is an array with the following
	optionnal parameters:

	 - <var>q</var>: search string (on user_id, user_name, user_firstname)
	 - <var>user_id</var>: user ID
	 - <var>order</var>: ORDER BY clause (default: user_id ASC)
	 - <var>limit</var>: LIMIT clause (should be an array ![limit,offset])

	@param	params		<b>array</b>		Parameters
	@param	count_only	<b>boolean</b>		Only counts results
	@return	<b>record</b>
	*/
	public function getUsers($params=array(),$count_only=false)
	{
		if ($count_only)
		{
			$strReq =
			'SELECT count(U.user_id) '.
			'FROM '.$this->prefix.'user U '.
			'WHERE NULL IS NULL ';
		}
		else
		{
			$strReq =
			'SELECT U.user_id,user_super,user_status,user_pwd,user_name,'.
			'user_firstname,user_displayname,user_email,user_url,'.
			'user_desc, user_lang,user_tz, user_post_status,user_options, '.
			'count(P.post_id) AS nb_post '.
			'FROM '.$this->prefix.'user U '.
				'LEFT JOIN '.$this->prefix.'post P ON U.user_id = P.user_id '.
			'WHERE NULL IS NULL ';
		}

		if (!empty($params['q'])) {
			$q = $this->con->escape(str_replace('*','%',strtolower($params['q'])));
			$strReq .= 'AND ('.
				"LOWER(U.user_id) LIKE '".$q."' ".
				"OR LOWER(user_name) LIKE '".$q."' ".
				"OR LOWER(user_firstname) LIKE '".$q."' ".
				') ';
		}

		if (!empty($params['user_id'])) {
			$strReq .= "AND U.user_id = '".$this->con->escape($params['user_id'])."' ";
		}

		if (!$count_only) {
			$strReq .= 'GROUP BY U.user_id,user_super,user_status,user_pwd,user_name,'.
			'user_firstname,user_displayname,user_email,user_url,'.
			'user_desc, user_lang,user_tz,user_post_status,user_options ';

			if (!empty($params['order']) && !$count_only) {
				$strReq .= 'ORDER BY '.$this->con->escape($params['order']).' ';
			} else {
				$strReq .= 'ORDER BY U.user_id ASC ';
			}
		}

		if (!$count_only && !empty($params['limit'])) {
			$strReq .= $this->con->limit($params['limit']);
		}

		$rs = $this->con->select($strReq);
		$rs->extend('rsExtUser');
		return $rs;
	}

	/**
	Create a new user. Takes a cursor as input and returns the new user ID.

	@param	cur		<b>cursor</b>		User cursor
	@return	<b>string</b>
	*/
	public function addUser(&$cur)
	{
		if (!$this->auth->isSuperAdmin()) {
			throw new Exception(T_('You are not an administrator'));
		}

		if ($cur->user_id == '') {
			throw new Exception(T_('No user ID given'));
		}

		if ($cur->user_pwd == '') {
			throw new Exception(T_('No password given'));
		}

		$this->getUserCursor($cur);

		if ($cur->user_creadt === null) {
			$cur->user_creadt = array('NOW()');
		}

		$cur->insert();

		$this->auth->afterAddUser($cur);

		return $cur->user_id;
	}

	/**
	Updates an existing user. Returns the user ID.

	@param	id		<b>string</b>		User ID
	@param	cur		<b>cursor</b>		User cursor
	@return	<b>string</b>
	*/
	public function updUser($id,&$cur)
	{
		$this->getUserCursor($cur);

		if (($cur->user_id !== null || $id != $this->auth->userID()) &&
		!$this->auth->isSuperAdmin()) {
			throw new Exception(T_('You are not an administrator'));
		}

		$cur->update("WHERE user_id = '".$this->con->escape($id)."' ");

		$this->auth->afterUpdUser($id,$cur);

		if ($cur->user_id !== null) {
			$id = $cur->user_id;
		}

		# Updating all user's blogs
		$rs = $this->con->select(
			'SELECT DISTINCT(blog_id) FROM '.$this->prefix.'post '.
			"WHERE user_id = '".$this->con->escape($id)."' "
			);

		while ($rs->fetch()) {
			$b = new dcBlog($this,$rs->blog_id);
			$b->triggerBlog();
			unset($b);
		}

		return $id;
	}

	/**
	Deletes a user.

	@param	id		<b>string</b>		User ID
	*/
	public function delUser($id)
	{
		if (!$this->auth->isSuperAdmin()) {
			throw new Exception(T_('You are not an administrator'));
		}

		if ($id == $this->auth->userID()) {
			return;
		}

		$rs = $this->getUser($id);

		if ($rs->nb_post > 0) {
			return;
		}

		$strReq = 'DELETE FROM '.$this->prefix.'user '.
				"WHERE user_id = '".$this->con->escape($id)."' ";

		$this->con->execute($strReq);

		$this->auth->afterDelUser($id);
	}

	/**
	Checks whether a user exists.

	@param	id		<b>string</b>		User ID
	@return	<b>boolean</b>
	*/
	public function userExists($id)
	{
		$strReq = 'SELECT user_id '.
				'FROM '.$this->prefix.'user '.
				"WHERE user_id = '".$this->con->escape($id)."' ";

		$rs = $this->con->select($strReq);

		return !$rs->isEmpty();
	}


	/**
	Returns if user is super user

	@return	<b>string</b>
	*/
	public function hasPermission($perm)
	{
		if ($this->auth->superUser()) {
			return true;
		}
		else {
			$user_perms = $this->getUserRolePermissions($this->auth->userID());
			return array_key_exists($perm, $this->auth->parsePermissions($user_perms->{'permissions'}));
		}
	}

	public function hasRole($role)
	{
		if ($this->auth->superUser()) {
			return true;
		}
		else {
			$user_perms = $this->getUserRolePermissions($this->auth->userID());
			if ($user_perms->{'role'} == 'manager') {
				return in_array($role, array('manager','user'));
			}
			elseif ($user_perms->{'role'} == $role) {
				return true;
			}
			else {
				return $role == 'user';
			}
		}
	}

	public function hasTokenRole($role, $token)
	{
		$rs = $this->con->select("SELECT user_id FROM ".$this->prefix."user
			WHERE user_status=1 AND user_token='".$token."';");
		if ($rs->count() == 1) {
			$user_perms = $this->getUserRolePermissions($rs->f('user_id'));
			if ($user_perms->{'role'} == $role) {
					return true;
			}
		}
		return false;
	}

	/**
	Returns all user permissions as an array which looks like:

	 - [blog_id]
	   - [name] => Blog name
	   - [url] => Blog URL
	   - [p]
	   	- [permission] => true
		- ...

	@param	id		<b>string</b>		User ID
	@return	<b>array</b>
	*/
	public function getUserRolePermissions($user_id)
	{
		$strReq = 'SELECT permissions '.
				'FROM '.$this->prefix.'permissions '.
				"WHERE user_id = '".$this->con->escape($user_id)."' ";

		$rs = $this->con->select($strReq);

#		$res = $this->auth->parsePermissions($rs->f('permissions'));
		$res = json_decode($rs->f('permissions'));

		return $res;
	}

	public function setUserPermission($user_id,$perm)
	{
		$user_perms = $this->getUserRolePermissions($user_id = null);
		if (!array_key_exists($perm, $this->auth->parsePermissions($user_perms->{'permissions'}))){
			if ($this->permInRole($perm, $user_perms['role'])) {
#				$perms = '|'.implode('|',$perms).'|';
				$user_perms->{'permissions'} .= $perm . '|';
			}
		}
		$new_perms = array(
			'role' => $user_perms->{'role'},
			'permissions' => $user_perms->{'permissions'},
			);

		if (!empty($perms)) {
			$cur = $this->con->openCursor($this->prefix.'permissions');
			$cur->user_id = (string) $user_id;
			$cur->permissions = json_encode($new_perms);
			$cur->modified = array('NOW()');
			$cur->update("WHERE user_id = '$user_id'");
		}
	}

	public function setUserPermissions($user_id,$perms)
	{
		$user_perms = $this->getUserRolePermissions($user_id);
		$new_perms = array(
			'role' => $user_perms->{'role'},
			'permissions' => '|'.implode('|',$perms).'|'
			);

		$cur = $this->con->openCursor($this->prefix.'permissions');
		$cur->user_id = (string) $user_id;
		$cur->permissions = json_encode($new_perms);
		$cur->modified = array('NOW()');
		$cur->update("WHERE user_id = '$user_id'");
	}

	public function setUserRole($user_id,$role)
	{
		$this->con->execute("DELETE FROM ".$this->prefix."permissions WHERE user_id = '$user_id'");

		$perms = array(
			'role' => $role,
			'permissions' => ''
			);
		$cur = $this->con->openCursor($this->prefix.'permissions');
		$cur->user_id = (string) $user_id;
		$cur->permissions = json_encode($perms);
		$cur->created = array('NOW()');
		$cur->modified = array('NOW()');
		$cur->insert();
	}

	public function permInRole($permission, $role)
	{
		$roles = array(
			'user' => array(),
			'manager' => array('configuration', 'administration', 'moderation'),
			'god' => array()
			);
		return array_key_exists($permission, $roles[$role]);
	}

	private function getUserCursor(&$cur)
	{
		if ($cur->isField('user_id')
		&& !preg_match('/^[A-Za-z0-9@._-]{2,}$/',$cur->user_id)) {
			throw new Exception(T_('User ID must contain at least 2 characters using letters, numbers or symbols.'));
		}

		if ($cur->user_url !== null && $cur->user_url != '') {
			if (!preg_match('|^http(s?)://|',$cur->user_url)) {
				$cur->user_url = 'http://'.$cur->user_url;
			}
		}

		if ($cur->isField('user_pwd')) {
			if (strlen($cur->user_pwd) < 6) {
				throw new Exception(T_('Password must contain at least 6 characters.'));
			}
			$cur->user_pwd = crypt::hmac('BP_MASTER_KEY',$cur->user_pwd);
		}

		if ($cur->user_lang !== null && !preg_match('/^[a-z]{2}(-[a-z]{2})?$/',$cur->user_lang)) {
			throw new Exception(T_('Invalid user language code'));
		}

		if ($cur->user_upddt === null) {
			$cur->user_upddt = array('NOW()');
		}

		if ($cur->user_options !== null) {
			$cur->user_options = serialize((array) $cur->user_options);
		}
	}

	/**
	Returns user default settings in an associative array with setting names in
	keys.

	@return	<b>array</b>
	*/
	public function userDefaults()
	{
		return array(
			'edit_size' => 24,
			'enable_wysiwyg' => true,
			'post_format' => 'wiki',
		);
	}

	/// @name HTML Filter methods
	//@{
	/**
	Calls HTML filter to drop bad tags and produce valid XHTML output (if
	tidy extension is present). If <b>enable_html_filter</b> blog setting is
	false, returns not filtered string.

	@param	str	<b>string</b>		String to filter
	@return	<b>string</b> Filtered string.
	*/
	public function HTMLfilter($str)
	{
		if ($this->blog instanceof dcBlog && !$this->blog->settings->enable_html_filter) {
			return $str;
		}

		$filter = new htmlFilter;
		$str = trim($filter->apply($str));
		return $str;
	}
	//@}

	/// @name wiki2xhtml methods
	//@{
	private function initWiki()
	{
		$this->wiki2xhtml = new wiki2xhtml;
	}

	/**
	Returns a transformed string with wiki2xhtml.

	@param	str		<b>string</b>		String to transform
	@return	<b>string</b>	Transformed string
	*/
	public function wikiTransform($str)
	{
		if (!($this->wiki2xhtml instanceof wiki2xhtml)) {
			$this->initWiki();
		}
		return $this->wiki2xhtml->transform($str);
	}

	/**
	Inits <var>wiki2xhtml</var> property for blog post.
	*/
	public function initWikiPost()
	{
		$this->initWiki();

		$this->wiki2xhtml->setOpts(array(
			'active_title' => 1,
			'active_setext_title' => 0,
			'active_hr' => 1,
			'active_lists' => 1,
			'active_quote' => 1,
			'active_pre' => 1,
			'active_empty' => 1,
			'active_auto_br' => 0,
			'active_auto_urls' => 0,
			'active_urls' => 1,
			'active_auto_img' => 0,
			'active_img' => 1,
			'active_anchor' => 1,
			'active_em' => 1,
			'active_strong' => 1,
			'active_br' => 1,
			'active_q' => 1,
			'active_code' => 1,
			'active_acronym' => 1,
			'active_ins' => 1,
			'active_del' => 1,
			'active_footnotes' => 1,
			'active_wikiwords' => 0,
			'active_macros' => 1,
			'parse_pre' => 1,
			'active_fr_syntax' => 0,
			'first_title_level' => 3,
			'note_prefix' => 'wiki-footnote',
			'note_str' => '<div class="footnotes"><h4>Notes</h4>%s</div>'
		));

		$this->wiki2xhtml->registerFunction('url:post',array($this,'wikiPostLink'));

		# --BEHAVIOR-- coreWikiPostInit
		$this->callBehavior('coreInitWikiPost',$this->wiki2xhtml);
	}

	/**
	Inits <var>wiki2xhtml</var> property for simple blog comment (basic syntax).
	*/
	public function initWikiSimpleComment()
	{
		$this->initWiki();

		$this->wiki2xhtml->setOpts(array(
			'active_title' => 0,
			'active_setext_title' => 0,
			'active_hr' => 0,
			'active_lists' => 0,
			'active_quote' => 0,
			'active_pre' => 0,
			'active_empty' => 0,
			'active_auto_br' => 1,
			'active_auto_urls' => 1,
			'active_urls' => 0,
			'active_auto_img' => 0,
			'active_img' => 0,
			'active_anchor' => 0,
			'active_em' => 0,
			'active_strong' => 0,
			'active_br' => 0,
			'active_q' => 0,
			'active_code' => 0,
			'active_acronym' => 0,
			'active_ins' => 0,
			'active_del' => 0,
			'active_footnotes' => 0,
			'active_wikiwords' => 0,
			'active_macros' => 0,
			'parse_pre' => 0,
			'active_fr_syntax' => 0
		));

		# --BEHAVIOR-- coreInitWikiSimpleComment
		$this->callBehavior('coreInitWikiSimpleComment',$this->wiki2xhtml);
	}

	/**
	Inits <var>wiki2xhtml</var> property for blog comment.
	*/
	public function initWikiComment()
	{
		$this->initWiki();

		$this->wiki2xhtml->setOpts(array(
			'active_title' => 0,
			'active_setext_title' => 0,
			'active_hr' => 0,
			'active_lists' => 1,
			'active_quote' => 0,
			'active_pre' => 1,
			'active_empty' => 0,
			'active_auto_br' => 1,
			'active_auto_urls' => 1,
			'active_urls' => 1,
			'active_auto_img' => 0,
			'active_img' => 0,
			'active_anchor' => 0,
			'active_em' => 1,
			'active_strong' => 1,
			'active_br' => 1,
			'active_q' => 1,
			'active_code' => 1,
			'active_acronym' => 1,
			'active_ins' => 1,
			'active_del' => 1,
			'active_footnotes' => 0,
			'active_wikiwords' => 0,
			'active_macros' => 0,
			'parse_pre' => 0,
			'active_fr_syntax' => 0
		));

		# --BEHAVIOR-- coreInitWikiComment
		$this->callBehavior('coreInitWikiComment',$this->wiki2xhtml);
	}

	public function wikiPostLink($url,$content)
	{
		if (!($this->blog instanceof dcBlog)) {
			return array();
		}

		$post_id = abs((integer) substr($url,5));
		if (!$post_id) {
			return array();
		}

		$post = $this->blog->getPosts(array('post_id'=>$post_id));
		if ($post->isEmpty()) {
			return array();
		}

		$res = array('url' => $post->getURL());
		$post_title = $post->post_title;

		if ($content != $url) {
			$res['title'] = html::escapeHTML($post->post_title);
		}

		if ($content == '' || $content == $url) {
			$res['content'] = html::escapeHTML($post->post_title);
		}

		if ($post->post_lang) {
			$res['lang'] = $post->post_lang;
		}

		return $res;
	}
	//@}

	/// @name Maintenance methods
	//@{
	/**
	Creates default settings for active blog. Optionnal parameter
	<var>defaults</var> replaces default params while needed.

	@param	defaults		<b>array</b>	Default parameters
	*/
	public function blogDefaults($defaults=null)
	{
		if (!is_array($defaults))
		{
			$defaults = array(
				array('allow_comments','boolean',true,
				'Allow comments on blog'),
				array('allow_trackbacks','boolean',true,
				'Allow trackbacks on blog'),
				array('blog_timezone','string','Europe/London',
				'Blog timezone'),
				array('comments_nofollow','boolean',true,
				'Add rel="nofollow" to comments URLs'),
				array('comments_pub','boolean',true,
				'Publish comments immediatly'),
				array('comments_ttl','integer',0,
				'Number of days to keep comments open (0 means no ttl)'),
				array('copyright_notice','string','','Copyright notice (simple text)'),
				array('date_format','string','%A, %B %e %Y',
				'Date format. See PHP strftime function for patterns'),
				array('editor','string','',
				'Person responsible of the content'),
				array('enable_html_filter','boolean',0,
				'Enable HTML filter'),
				array('enable_xmlrpc','boolean',0,
				'Enable XML/RPC interface'),
				array('lang','string','en',
				'Default blog language'),
				array('media_exclusion','string','',
				'File name exclusion pattern in media manager. (PCRE value)'),
				array('media_img_m_size','integer',448,
				'Image medium size in media manager'),
				array('media_img_s_size','integer',240,
				'Image small size in media manager'),
				array('media_img_t_size','integer',100,
				'Image thumbnail size in media manager'),
				array('media_img_title_pattern','string','Title ;; Date(%b %Y) ;; separator(, )',
				'Pattern to set image title when you insert it in a post'),
				array('nb_post_per_page','integer',20,
				'Number of entries on home page and category pages'),
				array('nb_post_per_feed','integer',20,
				'Number of entries on feeds'),
				array('nb_comment_per_feed','integer',20,
				'Number of comments on feeds'),
				array('post_url_format','string','{y}/{m}/{d}/{t}',
				'Post URL format. {y}: year, {m}: month, {d}: day, {id}: post id, {t}: entry title'),
				array('public_path','string','public',
				'Path to public directory, begins with a / for a full system path'),
				array('public_url','string','/public',
				'URL to public directory'),
				array('robots_policy','string','INDEX,FOLLOW',
				'Search engines robots policy'),
				array('short_feed_items','boolean',false,
				'Display short feed items'),
				array('theme','string','default',
				'Blog theme'),
				array('themes_path','string','themes',
				'Themes root path'),
				array('themes_url','string','/themes',
				'Themes root URL'),
				array('time_format','string','%H:%M',
				'Time format. See PHP strftime function for patterns'),
				array('tpl_allow_php','boolean',false,
				'Allow PHP code in templates'),
				array('tpl_use_cache','boolean',true,
				'Use template caching'),
				array('trackbacks_pub','boolean',true,
				'Publish trackbacks immediatly'),
				array('trackbacks_ttl','integer',0,
				'Number of days to keep trackbacks open (0 means no ttl)'),
				array('url_scan','string','query_string',
				'URL handle mode (path_info or query_string)'),
				array('use_smilies','boolean',false,
				'Show smilies on entries and comments'),
				array('wiki_comments','boolean',false,
				'Allow commenters to use a subset of wiki syntax')
			);
		}

		$settings = new dcSettings($this,null);
		$settings->setNameSpace('system');

		foreach ($defaults as $v) {
			$settings->put($v[0],$v[2],$v[1],$v[3],false,true);
		}
	}

	/**
	Recreates entries search engine index.

	@param	start	<b>integer</b>		Start entry index
	@param	limit	<b>integer</b>		Number of entry to index

	@return	<b>integer</b>		<var>$start</var> and <var>$limit</var> sum
	*/
	public function indexAllPosts($start=null,$limit=null)
	{
		$strReq = 'SELECT COUNT(post_id) '.
				'FROM '.$this->prefix.'post';
		$rs = $this->con->select($strReq);
		$count = $rs->f(0);

		$strReq = 'SELECT post_id, post_title, post_excerpt_xhtml, post_content_xhtml '.
				'FROM '.$this->prefix.'post ';

		if ($start !== null && $limit !== null) {
			$strReq .= $this->con->limit($start,$limit);
		}

		$rs = $this->con->select($strReq,true);

		$cur = $this->con->openCursor($this->prefix.'post');

		while ($rs->fetch())
		{
			$words = $rs->post_title.' '.	$rs->post_excerpt_xhtml.' '.
			$rs->post_content_xhtml;

			$cur->post_words = implode(' ',text::splitWords($words));
			$cur->update('WHERE post_id = '.(integer) $rs->post_id);
			$cur->clean();
		}

		if ($start+$limit > $count) {
			return null;
		}
		return $start+$limit;
	}

	/**
	Recreates comments search engine index.

	@param	start	<b>integer</b>		Start comment index
	@param	limit	<b>integer</b>		Number of comments to index

	@return	<b>integer</b>		<var>$start</var> and <var>$limit</var> sum
	*/
	public function indexAllComments($start=null,$limit=null)
	{
		$strReq = 'SELECT COUNT(comment_id) '.
				'FROM '.$this->prefix.'comment';
		$rs = $this->con->select($strReq);
		$count = $rs->f(0);

		$strReq = 'SELECT comment_id, comment_content '.
				'FROM '.$this->prefix.'comment ';

		if ($start !== null && $limit !== null) {
			$strReq .= $this->con->limit($start,$limit);
		}

		$rs = $this->con->select($strReq);

		$cur = $this->con->openCursor($this->prefix.'comment');

		while ($rs->fetch())
		{
			$cur->comment_words = implode(' ',text::splitWords($rs->comment_content));
			$cur->update('WHERE comment_id = '.(integer) $rs->comment_id);
			$cur->clean();
		}

		if ($start+$limit > $count) {
			return null;
		}
		return $start+$limit;
	}

	/**
	Reinits nb_comment and nb_trackback in post table.
	*/
	public function countAllComments()
	{
		$strReq = 'SELECT COUNT(comment_id) AS nb, post_id '.
				'FROM '.$this->prefix.'comment '.
				'WHERE comment_trackback %s 1 '.
				'AND comment_status = 1 '.
				'GROUP BY post_id ';

		$rsC = $this->con->select(sprintf($strReq,'<>'));
		$rsT = $this->con->select(sprintf($strReq,'='));

		$cur = $this->con->openCursor($this->prefix.'post');
		while ($rsC->fetch()) {
			$cur->nb_comment = (integer) $rsC->nb;
			$cur->update('WHERE post_id = '.(integer) $rsC->post_id);
			$cur->clean();
		}

		while ($rsT->fetch()) {
			$cur->nb_trackback = (integer) $rsT->nb;
			$cur->update('WHERE post_id = '.(integer) $rsT->post_id);
			$cur->clean();
		}
	}

	/**
	Empty templates cache directory
	*/
	public function emptyTemplatesCache()
	{
		if (is_dir(DC_TPL_CACHE.'/cbtpl')) {
			files::deltree(DC_TPL_CACHE.'/cbtpl');
		}
	}
	//@}
	public function renderTemplate() {
		echo $this->tpl->render();
		$this->con->close();
	}
}
?>
