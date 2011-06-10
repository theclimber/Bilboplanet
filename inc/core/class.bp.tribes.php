<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
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
class bpTribes
{
	protected $con;		///< <b>connection</b> Database connection object
	protected $table;		///< <b>string</b> Permission table name
	protected $user_id;		///< <b>string</b> User ID
	protected $current_tribe;

	protected $tribes = array();		///< <b>array</b> Associative settings array
	protected $global_tribes = array();	///< <b>array</b> Global settings array
	protected $local_tribes = array();	///< <b>array</b> Local settings array

	/**
	Object constructor. Retrieves blog settings and puts them in $settings
	array. Local (blog) settings have a highest priority than global settings.

	@param	core		<b>bpCore</b>		bpCore object
	@param	user_id	<b>string</b>		User ID
	*/
	public function __construct(&$core,$user_id=null,$current=null)
	{
		$this->con =& $core->con;
		$this->table = $core->prefix.'tribe';
		$this->prefix = $core->prefix;
		$this->user_id =& $user_id;

		$this->getTribes();
		if (isset($current)) {
			$this->current_tribe = $this->tribes[$current];
		}
	}

	public function setUser($user_id) {
		$this->user_id = $user_id;

		foreach ($this->local_tribes as $id => $v) {
			unset($this->tribes[$id]);
		}
		$this->local_tribes = array();

		$strReq = "SELECT
					tribe_id,
					user_id,
					tribe_name,
					tribe_search,
					tribe_tags,
					tribe_users,
					visibility
				FROM ".$this->table."
				WHERE user_id = '".$this->con->escape($this->user_id)."'
				ORDER BY ordering DESC ";

		try {
			$rs = $this->con->select($strReq);
		} catch (Exception $e) {
			throw new Exception(T_('Unable to retrieve tribes:').' '.$this->con->error(), E_USER_ERROR);
		}

		while ($rs->fetch())
		{
			$tribe_id		= $rs->tribe_id;
			$tribe_owner	= $rs->user_id;
			$tribe_name		= $rs->tribe_name;
			$tribe_search	= json_decode($rs->tribe_search, true);
			$tribe_tags		= json_decode($rs->tribe_tags, true);
			$tribe_users	= json_decode($rs->tribe_users, true);
			$tribe_visibility = $rs->tribe_visibility ? true : false;

			$this->local_tribes[$tribe_id] = array(
				'id'		=> $tribe_id,
				'owner'		=> $tribe_owner,
				'name'		=> $tribe_name,
				'search'	=> $tribe_search,
				'tags'		=> $tribe_tags,
				'users'		=> $tribe_users,
				'visibility'=> $tribe_visibility,
				'global'	=> $rs->user_id == ''
			);
			$this->tribes[$id] = $v;
		}

		return true;
	}

	public function setTribe($id) {
		if (has_key($this->tribes, $id)) {
			$this->current_tribe = $this->tribes[$id];
		} else {
			$empty_array = array('with' => array(), 'without' => array());
			$this->tribes[$id] = array(
				'id'		=> $id,
				'owner'		=> $this->user_id,
				'name'		=> '',
				'search'	=> $empty_array,
				'tags'		=> $empty_array,
				'users'		=> $empty_array,
				'visibility'=> 0,
				'global'	=> 0
			);
		}
	}

	public function addToTribe ($type, $list, $method='with') {
		$patterns = array( '/, /', '/ ,/');
		$replacement = array(',', ',');
		$list = urldecode($list);
		$list = preg_replace($patterns, $replacement, $list);
		$list = preg_split('/,/',$list, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($list as $el) {
			$this->current_tribe[$type][$method][] = $el;
		}
	}

	public function rmFromTribe ($type, $list, $method='with') {
		$patterns = array( '/, /', '/ ,/');
		$replacement = array(',', ',');
		$list = urldecode($list);
		$list = preg_replace($patterns, $replacement, $list);
		$list = preg_split('/,/',$list, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($list as $el) {
			$index = array_keys($this->current_tribe[$type][$method], $el);
			if (isset($index)) {
				unset($this->current_tribe[$type][$method][$index]);
			}
		}
	}

	private function getTribes()
	{
		$strReq = "SELECT
					tribe_id,
					user_id,
					tribe_name,
					tribe_search,
					tribe_tags,
					tribe_users,
					visibility
				FROM ".$this->table."
				WHERE user_id = '".$this->con->escape($this->user_id)."'
				OR visibility = 1
				ORDER BY ordering DESC ";

		try {
			$rs = $this->con->select($strReq);
		} catch (Exception $e) {
			throw new Exception(T_('Unable to retrieve tribes:').' '.$this->con->error(), E_USER_ERROR);
		}

		while ($rs->fetch())
		{
			$tribe_id		= $rs->tribe_id;
			$tribe_owner	= $rs->user_id;
			$tribe_name		= $rs->tribe_name;
			$tribe_search	= json_decode($rs->tribe_search, true);
			$tribe_tags		= json_decode($rs->tribe_tags, true);
			$tribe_users	= json_decode($rs->tribe_users, true);
			$tribe_visibility = $rs->tribe_visibility ? true : false;

			$array = $rs->user_id ? 'local' : 'global';

			$this->{$array.'_tribes'}[$tribe_id] = array(
				'id'		=> $tribe_id,
				'owner'		=> $tribe_owner,
				'name'		=> $tribe_name,
				'search'	=> $tribe_search,
				'tags'		=> $tribe_tags,
				'users'		=> $tribe_users,
				'visibility'=> $tribe_visibility,
				'global'	=> $rs->user_id == ''
			);
		}

		$this->tribes = $this->global_tribes;

		foreach ($this->local_tribes as $id => $v) {
			$this->tribes[$id] = $v;
		}
		return true;
	}

	private function tribeExists($id,$global=false)
	{
		$array = $global ? 'global' : 'local';
		return isset($this->{$array.'_tribes'}[$id]);
	}


	/**
	Creates or updates a tribe.

	@param	name		<b>string</b>		Tribe name (visible name)
	@param	user_id		<b>string</b>		Tribe owner
	@param	search		<b>string</b>		array with / without
	@param	tags		<b>string</b>		array with / without
	@param	users		<b>string</b>		array with / without
	@param	ordering	<b>int</b>			Tribe order
	@param	visibility	<b>string</b>		Tribe visibility public or private
	@param	global		<b>boolean</b>		Setting is global
	*/
	public function put($id,$name,$search,$tags,$users,$ordering=100,$visibility='private',$global=false)
	{
		if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]+$/',$id)) {
			throw new Exception(sprintf(T_('%s is not a valid tribe id'),$id));
		}

		$public = $visibility == 'public' ? 1 : 0;

		$cur = $this->con->openCursor($this->table);
		if (!$global) {
			$cur->user_id = $this->user_id;
		}
		$cur->ordering = $ordering;
		$cur->visibility = $public;
		$cur->tribe_name = $name;
		$cur->tribe_search = json_encode($search);
		$cur->tribe_tags = json_encode($tags);
		$cur->tribe_users = json_encode($users);
		$cur->modified = array(' NOW() ');

		if ($this->tribeExists($id,$global))
		{
			if ($global) {
				$where = 'WHERE user_id IS NULL ';
			} else {
				$where = "WHERE user_id = '".$this->con->escape($this->user_id)."' ";
			}

			$cur->update($where."AND tribe_id = '".$this->con->escape($id)."' ");
		}
		else
		{
			$cur->tribe_id = $id;
			$cur->user_id = $global ? null : $this->user_id;
			$cur->created = array(' NOW() ');

			$cur->insert();
		}
	}

	/**
	Removes an existing tribe. Namespace

	@param	id		<b>string</b>		Tribe ID
	*/
	public function drop($id)
	{
		$strReq =	'DELETE FROM '.$this->table.' ';

		if ($this->user_id === null) {
			$strReq .= 'WHERE user_id IS NULL ';
		} else {
			$strReq .= "WHERE user_id = '".$this->con->escape($this->user_id)."' ";
		}

		$strReq .= "AND tribe_id = '".$this->con->escape($id)."' ";

		$this->con->execute($strReq);
	}

	public function get($id) {
		if (isset($this->tribes[$id])) {
			return $this->tribes[$id];
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
	Returns $global_settings property content.

	@return	<b>array</b>
	*/
	public function dumpGlobalTribes()
	{
		return $this->global_tribes;
	}

	public function dumpLocalTribes()
	{
		return $this->local_tribes;
	}

	public function getCurrentTribePopularPosts(
		$nb_items,
		$num_start = 0) {
		$tribe_id = $this->current_tribe['id'];
		$period = $this->current_tribe['period'];
		return $this->getTribePosts($tribe_id, $nb_items, $num_start, $period, true);
	}

	public function getCurrentTribePosts(
		$nb_items,
		$num_start = 0) {
		$tribe_id = $this->current_tribe['id'];
		$period = $this->current_tribe['period'];
		return $this->getTribePosts($tribe_id, $nb_items, $num_start, $period);
	}

	protected function getTribePosts(
			$tribe_id,
			$nb_items,
			$num_start = 0,
			$period = null,
			$popular = false,
			$post_status = null)
		{

		$sql = generate_SQL($tribe_id, $nb_items, $period, $popular, $post_status);
		$rs = $this->con->select($sql);
		$post_list = array();

		while($rs->fetch()){

			$post = array(
				"id" => $rs->post_id,
				"date" => mysqldatetime_to_date("d/m/Y",$rs->pubdate),
				"day" => mysqldatetime_to_date("d",$rs->pubdate),
				"month" => mysqldatetime_to_date("m",$rs->pubdate),
				"year" => mysqldatetime_to_date("Y",$rs->pubdate),
				"hour" => mysqldatetime_to_date("H:i",$rs->pubdate),
				"permalink" => urldecode($post_permalink),
				"title" => html_entity_decode($rs->title, ENT_QUOTES, 'UTF-8'),
				"content" => html_entity_decode($rs->content, ENT_QUOTES, 'UTF-8'),
				"author_id" => $rs->user_id,
				"author_fullname" => $rs->user_fullname,
				"author_email" => $rs->user_email,
				"nbview" => $rs->nbview,
				"last_viewed" => mysqldatetime_to_date('d/m/Y H:i',$rs->last_viewed),
				"user_votes" => getNbVotes(null,$rs->user_id),
				"user_posts" => getNbPosts(null,$rs->user_id)
				);

			foreach ($this->tribes[$tribe_id]['search']['with'] as $key=>$value) {
				# Format the occurences of the search request in the posts list
				$post['content'] = $this->split_balise($value, '<span class="search_content">'.$value.'</span>', $post['content'], 'str_ireplace', 1);
				# Format the occurences of the search request in the posts title
				$post['title'] = $this->split_balise($value, '<span class="search_title">'.$value.'</span>', $post['title'], 'str_ireplace', 1);
			}

			$post_list[$rs->post_id] = $post;
		}

		return $post_list;
	}

	private function generate_SQL(
			$tribe_id,
			$nb_items,
			$num_start = 0,
			$period = null,
			$popular = false,
			$post_status = null)
		{

		if (!is_numeric($nb_items)) {
			throw new Exception(sprintf(T_('%s must be and integer'),$id));
		}

		$tables = $this->prefix."post, ".$this->prefix."user";
		if (!empty($tags)) {
			$tables .= ", ".$this->prefix."post_tag";
		}

		$select = $this->prefix."user.user_id		as user_id,
				user_fullname	as user_fullname,
				user_email		as user_email,
				post_pubdate	as pubdate,
				post_title		as title,
				post_permalink	as permalink,
				post_content	as content,
				post_nbview		as nbview,
				last_viewed		as last_viewed,
				".$this->prefix."post.post_id		as post_id,
				post_score		as score,
				post_status		as status,
				post_comment	as comment";
		$where_clause = $this->prefix."user.user_id = ".$this->prefix."post.user_id
			AND user_status = '1'";

		if (isset($post_status) && is_numeric($post_status)) {
			$where_clause .= " AND post_status = '".$post_status."' ";
		} else {
			$where_clause .= " AND post_status = '1' ";
		}

		$users = $this->tribes[$tribe_id]['users'];
		if (!empty($users)) {
			$where_clause .= $this->__getUsersClause($users);
		}

		$tags = $this->tribes[$tribe_id]['tags'];
		if (!empty($tags)) {
			$where_clause .= $this->__getTagsClause($tags);
		}

		$search = $this->tribes[$tribe_id]['search'];
		if (isset($search) && !empty($search)){
			$where_clause .= $this->__getSearchClause($search);
		}

		if (isset($period) && !empty($period)) {
			$where_clause .= $this->__getPeriodClause($period);
		}

		if ($popular){
			$max = $this->con->select("SELECT
				MAX(post_nbview) as max_view,
				MAX(post_score) as max_score
				FROM ".$this->prefix."post");
			$max_view = $max->f('max_view');
			$max_score = $max->f('max_score');
			# Complete the SQL query
			$select .= ",
				post_score/".$max_score." + post_nbview/".$max_view." as total_score";
			$where_clause .= " AND post_score > 0 ";
			if (!isset($period) || empty($period)) {
				$week = time() - 3600*24*7;
				$where_clause .= "AND post_pubdate > ".$week;
			}
			$fin_sql = " ORDER BY total_score DESC
				LIMIT $num_start,".$nb_items;
		}
		else {
			$fin_sql = " ORDER BY post_pubdate DESC
				LIMIT $num_start,".$nb_items;
		}

		$debut_sql = "SELECT DISTINCT
				".$select."
			FROM ".$tables."
			WHERE ".$where_clause;
		$sql = $debut_sql." ".$fin_sql;

		return $sql;
	}

	private function __getWithWithoutClause($array,$compare) {
		$where_clause = ' ';
		if (count($array['with']) > 0) {
			$sql_with = "(";
			foreach ($array['with'] as $key=>$value) {
				$sql_with .= $compare." = '".$value."'";
				$or = ($key == count($array)-1) ? "" : " OR ";
				$sql_with .= $or;
			}
			$sql_with .= ")";
			$where_clause .= ' AND '.$sql_with.' ';
		}

		if (count($array['without']) > 0) {
			$sql_without = "(";
			foreach ($array['without'] as $key=>$value) {
				$sql_without .= $compare." != '".$value."'";
				$and = ($key == count($array)-1) ? "" : " AND ";
				$sql_without .= $and;
			}
			$sql_without .= ")";
			$where_clause .= ' AND '.$sql_without.' ';
		}

		return $where_clause;
	}

	private function __getUsersClause($users) {
		return $this->__getWithWithoutClause($users, $this->prefix.'post.user_id');
	}

	private function __getTagsClause($tags) {
		$where_clause .= " AND ".$this->prefix."post.post_id = ".$this->prefix."post_tag.post_id";
		$where_clause .= $this->__getWithWithoutClause($tags, $this->prefix.'post_tag.tag_id');
		return $where_clause;
	}

	private function __getSearchClause($search) {
		# Complete the SQL query
		$where_clause = ' ';
		if (count($search['with']) > 0) {
			$sql_search = '(';
			foreach ($search['with'] as $key=>$value) {
				$sql_search .= " (".$this->prefix."post.post_title LIKE '%$value%'
					OR ".$this->prefix."post.post_permalink LIKE '%$value%'
					OR ".$this->prefix."post.post_content LIKE '%$value%'
					OR ".$this->prefix."user.user_fullname LIKE '%$value%')";
				$or = ($key == count($array)-1) ? "" : " OR ";
				$sql_search .= ' '.$or;
			}
			$sql_search .= ')';
			$where_clause = " AND ".$sql_search." ";
		}
		return $where_clause;
	}

	private function __getPeriodClause($period) {
		# Complete the SQL query
		$now = mktime(0, 0, 0, date("m",time()), date("d",time()), date("Y",time()));
		$day = date('Y-m-d', $now).' 00:00:00';
		$week = date('Y-m-d', $now - 3600*24*7).' 00:00:00';
		$month = date('Y-m-d', $now - 3600*24*31).' 00:00:00';
		$filter_class = array(
			"day" => "",
			"week" => "",
			"month" => "");
		switch($period) {
		case "day"		:
			$where_clause = " AND post_pubdate > '".$day."'";
			break;
		case "week"		:
			$where_clause = " AND post_pubdate > '".$week."'";
			break;
		case "month"	:
			$where_clause = " AND post_pubdate > '".$month."'";
			break;
		default			:
			$where_clause = " AND post_pubdate > '".$week."'";
			break;
		}
		return $where_clause;
	}

	private function split_balise($de, $par, $txt, $fct, $flag = 1){
		global $arg;
		$arg = compact('de', 'par', 'fct', 'flag');
		return preg_replace_callback('#((?:(?!<[/a-z]).)*)([^>]*>|$)#si', "mon_rplc_callback", $txt);
	}

}
?>
