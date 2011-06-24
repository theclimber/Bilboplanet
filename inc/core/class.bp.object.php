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

abstract class bpObject
{

	protected function getDateFormat($format, $timestamp) {
		return mysqldatetime_to_date($format,$timestamp);
	}


	public function postNewsOnSocialNetwork($title, $author, $post_id) {
		global $blog_settings;
		$post_url = $blog_settings->get('planet_url').'/?post_id='.$post_id;
		$formating = $blog_settings->get('statusnet_post_format');
		$textlimit = $blog_settings->get('statusnet_textlimit');

	//	$title_length = $textlimit - strlen($post_url) - strlen($formating);
	//	$short_title = substr($title,0,$title_length)."...";

		$content = sprintfn($formating, array(
			"title" => $title,
			"author" => $author));
		$content_max_length = $textlimit - strlen($post_url) - 4;
		$short_message = substr($content,0,$content_max_length)."...";
		$status = $short_message.' '.$post_url;

		if ($blog_settings->get('statusnet_auto_post')) {
			postToStatusNet(
				$blog_settings->get('statusnet_host'),
				$blog_settings->get('statusnet_username'),
				$blog_settings->get('statusnet_password'),
				$status);
		}
	}
}
