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
class bpSite extends bpObject
{
	protected $core;
	protected $con;		///< <b>connection</b> Database connection object
	protected $table;
	protected $prefix;

	protected $site_id;
	protected $site_url;
	protected $site_status;
	protected $site_name;
	protected $site_author;

	public function __construct(&$con, $prefix,$site_id)
	{
		$this->con =& $con;
		$this->prefix = $prefix;
		$this->table = $prefix.'site';
		$this->site_id = $site_id;

		$this->getSite();
	}

	private function getSite()
	{
		$select = "site_id,
				user_id,
				site_name,
				site_url,
				site_status";
		$tables = $this->table;
		$where_clause = "site_id = ".$this->site_id;

		$strReq = "SELECT ".$select."
			FROM ".$tables."
			WHERE ".$where_clause;

		try {
			$rs = $this->con->select($strReq);
		} catch (Exception $e) {
			throw new Exception(T_('Unable to retrieve site:').' '.$this->con->error(), E_USER_ERROR);
		}
		if ($rs->count() == 0) {
			throw new Exception(T_('This site does not exist'));
		}

		$this->site_author		= new bpUser($this->con, $this->prefix ,$rs->f('user_id'));
		$this->site_name		= $rs->f('site_name');
		$this->site_url			= $rs->f('site_url');
		$this->site_status		= $rs->f('site_url') ? true : false;

		return true;
	}

	public function getAuthor() {
		return $this->site_author;
	}

	public function getUrl() {
		return $this->site_url;
	}

	public function getId() {
		return $this->site_id;
	}

	public function isActive() {
		return $this->site_status;
	}

	public function getTitle() {
		return $this->site_name;
	}
}
?>
