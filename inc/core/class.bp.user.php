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

class bpUser extends bpObject
{
	protected $con;		///< <b>connection</b> Database connection object
	protected $table;
	protected $user_id;		///< <b>string</b> User ID

	protected $user_fullname;
	protected $user_email;
	protected $user_status;
	protected $user_pwd;
	protected $user_token;
	protected $user_lang;

	protected $user_settings;

	protected $user_description;

	public function __construct(&$con, $prefix,$user_id)
	{
		$this->con =& $con;
		$this->table = $prefix.'user';
		$this->prefix = $prefix;
		$this->user_id =& $user_id;

		$this->user_settings = new bpSettings($con, $prefix, $this->user_id);
		$this->getUser();
	}

	private function getUser() {
		$sql = "SELECT
				user_fullname,
				user_email,
				user_pwd,
				user_token,
				user_status,
				user_lang
			FROM ".$this->table. "
			WHERE user_id ='".$this->user_id."'";
		$rs = $this->con->select($sql);

		$this->user_fullname = $rs->f('user_fullname');
		$this->user_email = $rs->f('user_email');
		$this->user_status = $rs->f('user_status');
		$this->user_pwd = $rs->f('user_pwd');
		$this->user_token = $rs->f('user_token');
		$this->user_lang = $rs->f('user_lang');

		$this->user_description = $this->user_settings->get('user_description');
	}

	# Fonction qui retourne le nombre de votes
	public function getUserVotes() {
		$sql = "SELECT
				".$this->prefix."user.user_id as user_id,
				SUM(post_score) AS nb
			FROM ".$this->prefix."post, ".$this->prefix."user, ".$this->prefix."site
			WHERE
				".$this->prefix."site.user_id = ".$this->prefix."user.user_id
				AND ".$this->prefix."user.user_id = ".$this->prefix."post.user_id
				AND ".$this->prefix."user.user_id = '".$this->user_id."'
				AND user_status = 1
			GROUP BY user_id";
		$rs = $this->con->select($sql);
		return $rs->f('nb');
	}

	# Fonction qui retourne le nombre d'articles
	public function getNbPosts() {
		$sql = 'SELECT COUNT(1) as nb FROM '.$this->prefix.'post WHERE post_status = 1 AND user_id = \''.$this->user_id.'\'';
		$rs = $this->con->select($sql);
		return $rs->f('nb');
	}

	public function getId() {
		return $this->user_id;
	}

	public function getFullname() {
		return $this->user_fullname;
	}

	public function getEmail() {
		return $this->user_email;
	}

	public function getDescription() {
		return $this->user_description;
	}

	public function getSites() {
		$sites = array();
		$rs = $this->con->select("SELECT site_id FROM ".$this->prefix."site
			WHERE user_id = '".$this->getId()."'");
		while($rs->fetch()) {
			$sites[] = new bpSite($this->con, $this->prefix, $rs->site_id);
		}
		return $sites;
	}

	public function getLatestPosts($n) {
		$latest_posts = array();
		$rs = $this->con->select("SELECT post_id FROM ".$this->prefix."post
			WHERE user_id = '".$this->getId()."' ORDER BY post_pubdate DESC LIMIT 0, $n");
		while($rs->fetch()) {
			$latest_posts[] = new bpPost($this->con, $this->prefix, $rs->post_id);
		}
		return $latest_posts;
	}
}
?>
