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

class PostController extends AbstractController
{
	public function __construct(&$core) {
		$this->core =& $core;
		$this->con = $core->con;
		$this->prefix = $core->prefix;
	}

	public function view() {
		global $blog_settings, $notFound;

		# if user want to read a unique post
		if (isset($_GET['id']) && !empty($_GET['id'])){
		$post = new bpPost($this->con, $this->prefix, intval($_GET['id']));

			if($post->isActive()) {
				if (
					isset($_GET['go']) &&
					$_GET['go'] == "external" &&
					$blog_settings->get('internal_links')
				){
					$root_url = $blog_settings->get('planet_url');
					$analytics = $blog_settings->get('planet_analytics');

					if(!empty($analytics)) {
						# If google analytics is activated, launch request
						analyze (
							$analytics,
							$root_url.'/post/'.$post->getId(),
							'post:'.$post->getId(),
							$post->getBlogPermalink());
					}
					http::redirect(stripslashes($post->getBlogPermalink()));
				} else {
					$view = new PostView($this->core, $post);
					$view->addJavascript('javascript/main.js');
					$view->addJavascript('javascript/jquery.boxy.js');
					# Print result on screen
					$view->render();
				}
			} else {
				$notFound = true;
			}
		} else {
			$notFound = true;
		}
	}
}
