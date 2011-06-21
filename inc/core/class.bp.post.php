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
class bpPost
{
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

	protected $post_tags = array();

	public function __construct(&$core,$post_id)
	{
		$this->con =& $core->con;
		$this->table = $core->prefix.'post';
		$this->prefix = $core->prefix;
		$this->getPost();
	}

	private function getPost()
	{
		$select = $core->prefix."user.user_id		as user_id,
				post_pubdate	as pubdate,
				post_title		as title,
				post_permalink	as permalink,
				post_content	as content,
				post_nbview		as nbview,
				last_viewed		as last_viewed,
				".$this->table.".post_id		as post_id,
				post_score		as score,
				post_status		as status,
				post_comment	as comment";
		$tables = $this->table.", ".$this->prefix."user, ".$this->prefix."post_tag";
		$where_clause = $this->prefix."user.user_id = ".$this->table.".user_id";
		$where_clause .= " AND ".$core->prefix."post.post_id = '".$post_id."'";

		$strReq = "SELECT ".$select."
			FROM ".$tables."
			WHERE ".$where_clause;

		try {
			$rs = $this->con->select($strReq);
		} catch (Exception $e) {
			throw new Exception(T_('Unable to retrieve post:').' '.$this->con->error(), E_USER_ERROR);
		}

		$this->timestamp = $rs->f('pubdate');
		$this->permalink = urldecode($rs->f('permalink'));
		$this->title = html_entity_decode($rs->f('title'), ENT_QUOTES, 'UTF-8');
		$this->content = html_entity_decode($rs->f('content'), ENT_QUOTES, 'UTF-8');

		$this->author = new bpUser($rs->f('user_id'));
		$this->nbviews = $rs->f('nbview');
		$this->last_viewed = $rs->f('last_viewed');
		$this->score = $rs->f('score');
		$this->allow_comments = $rs->f('comments');
		$this->status = $rs->f('status');

		$sql_tags = "SELECT * FROM ".$this->prefix."post_tags WHERE post_id = ".$this->post_id;
		$rs2 = $this->con->select($sql_tags);
		while ($rs2->fetch()) {
			$this->tags[] = $rs2->tag_id;
		}

		return true;
	}

	public getPermalink() {
		$post_permalink = $this->permalink;
		if ($blog_settings->get('internal_links')) {
			$post_permalink = $blog_settings->get('planet_url').
				"/index.php?post_id=".$this->post_id.
				"&go=external";
		}
		return $post_permalink;
	}

	public getPubdateDay() {
		return getPubdateFormat('d');
	}
	public getPubdateMonth() {
		return getPubdateFormat('m');
	}
	public getPubdateYear() {
		return getPubdateFormat('Y');
	}
	public getPubdateHour() {
		return getPubdateFormat('H:i');
	}
	public getPubdate() {
		return getPubdateFormat('d/m/Y');
	}
	protected getLatestViewedFormat($format) {
		return  getDateFormat($format,$this->latest_viewed)
	}
	protected getPubdateFormat($format) {
		return  getDateFormat($format,$this->timestamp)
	}
	protected getDateFormat($format, $timestamp) {
		return  mysqldatetime_to_date($format,$timestamp)
	}

	public function setSearchWith(searchs) {
		foreach ($searchs as $key=>$value) {
			# Format the occurences of the search request in the posts list
			$this->content = $this->split_balise($value, '<span class="search_content">'.$value.'</span>', $this->content, 'str_ireplace', 1);
			# Format the occurences of the search request in the posts title
			$this->title = $this->split_balise($value, '<span class="search_title">'.$value.'</span>', $this->title, 'str_ireplace', 1);
		}
	}

	private function split_balise($de, $par, $txt, $fct, $flag = 1){
		global $arg;
		$arg = compact('de', 'par', 'fct', 'flag');
		return preg_replace_callback('#((?:(?!<[/a-z]).)*)([^>]*>|$)#si', "mon_rplc_callback", $txt);
	}
}
?>
