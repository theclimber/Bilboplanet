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

class PostView extends AbstractView
{
	protected $post;

	public function __construct(&$core, $post)
	{
		$this->core =& $core;
		$this->theme =& $theme;
		$this->post = $post;

		# Create the Hyla_Tpl object
		$this->tpl = new Hyla_Tpl();
		$this->tpl->setL10nCallback('T_');
		$this->tpl->importFile('index','index.tpl', dirname(__FILE__).'/../themes/'.$blog_settings->get('planet_theme'));
		$this->tpl->setVar('planet', array(
			"url"	=>	$blog_settings->get('planet_url'),
			"theme"	=>	$blog_settings->get('planet_theme'),
			"title"	=>	$blog_settings->get('planet_title'),
			"desc"	=>	$blog_settings->get('planet_desc'),
			"keywords"	=>	$blog_settings->get('planet_keywords'),
			"desc_meta"	=>	$blog_settings->get('planet_desc_meta'),
			"msg_info" => $blog_settings->get('planet_msg_info'),
		));
	}

	public function renderPost() {
	}

	public function render() {
	}
}
?>
