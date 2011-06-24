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
class bpPost extends bpObject
{
	protected $core;
	protected $con;		///< <b>connection</b> Database connection object
	protected $table;
	protected $prefix;
	protected $post_id;

	protected $allow_comments;
	protected $post_status;

	protected $timestamp;
	protected $permalink;
	protected $title;
	protected $content;
	protected $author;
	protected $last_viewed;
	protected $nbviews;
	protected $score;

	protected $tags = array();

	public function __construct(&$con, $prefix,$post_id)
	{
		$this->con = $con;
		$this->prefix = $prefix;
		$this->table = $prefix.'post';
		$this->post_id = $post_id;
		$this->getPost();
	}

	private function getPost()
	{
		$select = $this->prefix."user.user_id		as user_id,
				post_pubdate	as pubdate,
				post_title		as title,
				post_permalink	as permalink,
				post_content	as content,
				post_nbview		as nbview,
				last_viewed		as last_viewed,
				".$this->table.".post_id		as post_id,
				post_score		as score,
				post_status		as status,
				post_comment	as post_comment";
		$tables = $this->table.", ".$this->prefix."user";
		$where_clause = $this->prefix."user.user_id = ".$this->table.".user_id";
		$where_clause .= " AND ".$this->prefix."post.post_id = '".$this->post_id."'";

		$strReq = "SELECT ".$select."
			FROM ".$tables."
			WHERE ".$where_clause;

		try {
			$rs = $this->con->select($strReq);
		} catch (Exception $e) {
			throw new Exception(T_('Unable to retrieve post:').' '.$this->con->error(), E_USER_ERROR);
		}
		if ($rs->count() == 0) {
			throw new Exception(T_('This post does not exist'));
		}

		$this->timestamp = $rs->f('pubdate');
		$this->permalink = urldecode($rs->f('permalink'));
		$this->title = html_entity_decode($rs->f('title'), ENT_QUOTES, 'UTF-8');
		$this->content = html_entity_decode($rs->f('content'), ENT_QUOTES, 'UTF-8');

		$this->author = new bpUser($this->con, $this->prefix, $rs->f('user_id'));
		$this->nbviews = $rs->f('nbview');
		$this->last_viewed = $rs->f('last_viewed');
		$this->score = $rs->f('score');
		$this->allow_comments = $rs->f('post_comment');
		$this->status = $rs->f('status');

		$sql_tags = "SELECT * FROM ".$this->prefix."post_tag WHERE post_id = ".$this->post_id;
		$rs2 = $this->con->select($sql_tags);
		while ($rs2->fetch()) {
			$this->tags[] = $rs2->tag_id;
		}

		return true;
	}

	public function getPermalink() {
		global $blog_settings;
		$post_permalink = $this->permalink;
		if ($blog_settings->get('internal_links')) {
			$post_permalink = $blog_settings->get('planet_url').
				"/index.php?post_id=".$this->post_id.
				"&go=external";
		}
		return $post_permalink;
	}

	public function getPubdateDay() {
		return $this->getPubdateFormat('d');
	}
	public function getPubdateMonth() {
		return $this->getPubdateFormat('m');
	}
	public function getPubdateYear() {
		return $this->getPubdateFormat('Y');
	}
	public function getPubdateHour() {
		return $this->getPubdateFormat('H:i');
	}
	public function getPubdate() {
		return $this->getPubdateFormat('d/m/Y');
	}
	public function getLatestViewedFormat($format) {
		return  $this->getDateFormat($format,$this->last_viewed);
	}
	protected function getPubdateFormat($format) {
		return  $this->getDateFormat($format,$this->timestamp);
	}

	public function setSearchWith($searchs) {
		foreach ($searchs as $key=>$value) {
			# Format the occurences of the search request in the posts list
			$this->content = $this->split_balise($value, '<span class="search_content">'.$value.'</span>', $this->content, 'str_ireplace', 1);
			# Format the occurences of the search request in the posts title
			//$this->title = $this->split_balise($value, '<span class="search_title">'.$value.'</span>', $this->title, 'str_ireplace', 1);
		}
	}

	public function isActive() {
		if ($this->status == 1) {
			return true;
		}
		return false;
	}

	public function updateView() {
		# Update the number of viewed times
		$cur = $this->con->openCursor($this->prefix.'post');
		$cur->post_nbview = $this->nbviews + 1;
		$cur->last_viewed = array('NOW()');
		$cur->update("WHERE post_id = '".$this->id."'");
		$this->nbviews += 1;
	}

	public function setStripTags() {
		$this->content = substr($this->content, 200);
		$this->content .= strip_tags($this->content)."&nbsp;[...]".
			'<br /><a href="'.$this->getPermalink().'" title="'
				.$this->getTitle().'">'.T_('Read more').'</a>';
	}

	private function split_balise($de, $par, $txt, $fct, $flag = 1){
		global $arg;
		$arg = compact('de', 'par', 'fct', 'flag');
		return preg_replace_callback('#((?:(?!<[/a-z]).)*)([^>]*>|$)#si', "mon_rplc_callback", $txt);
	}

	public function getTitle() {
		return $this->title;
	}

	public function getContent() {
		return $this->content;
	}

	public function getNbViews() {
		return $this->nbviews;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function getId() {
		return $this->post_id;
	}

	public function getScore() {
		return $this->score;
	}

	public function getTags() {
		return $this->tags;
	}

	public function allowComments() {
		return $this->allow_comments;
	}

	public function getShortTitle() {
		$max_title_length = 100;
		if (strlen($this->title) > $max_title_length)
			return substr($this->title,0,$max_title_length)."...";
		else
			return $this->title;
	}

	public function getPostArray() {
		$post_array = array(
			"id"				=> $this->post_id,
			"date"				=> $this->getPubdate(),
			"day"				=> $this->getPubdateDay(),
			"month"				=> $this->getPubdateMonth(),
			"year"				=> $this->getPubdateYear(),
			"hour"				=> $this->getPubdateHour(),
			"permalink"			=> $this->getPermalink(),
			"title"				=> $this->getTitle(),
			"content"			=> $this->getContent(),
			"author_id"			=> $this->getAuthor()->getId(),
			"author_fullname"	=> $this->getAuthor()->getFullname(),
			"author_email"		=> $this->getAuthor()->getEmail(),
			"author_votes"		=> $this->getAuthor()->getUserVotes(),
			"author_posts"		=> $this->getAuthor()->getNbPosts(),
			"nbview"			=> $this->getNbViews(),
			"last_viewed"		=> $this->getLatestViewedFormat('d/m/Y H:i')
			);
		return $post_array;
	}
}
?>
