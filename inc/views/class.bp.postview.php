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
		$this->instantiateTPL();
		$this->core =& $core;
		$this->con = $core->con;
		$this->prefix = $core->prefix;
		$this->post = $post;
		$this->page = "post";
	}

	protected function renderSingleSidebar() {
		global $blog_settings;
		foreach ($this->post->getTags() as $tag) {
			$this->tpl->setVar('post_tag', $tag);
			$this->tpl->render('post.tags');
		}

		$author_sites = $this->post->getAuthor()->getSites();
		foreach ($author_sites as $site) {
			$this->tpl->setVar('author_site', $site->getUrl());
			$this->tpl->render("author.sites");
		}

		if ($blog_settings->get('planet_vote')) {
			$votes = array("html" => $this->afficheVotes($this->post->getScore(), $this->post->getId()));
			$this->tpl->setVar('votes', $votes);
			$this->tpl->render('side.votes');
		}

		$latest_posts = $this->post->getAuthor()->getLatestPosts(3);
		foreach ($latest_posts as $post) {
			$this->tpl->setVar('same_post', array(
				'title' => $post->getTitle(),
				'permalink' => $post->getPlanetPermalink()));
			$this->tpl->render('author.same');
		}

		$this->tpl->setVar('topNavSelected', array('class="selected"', '', '', '', ''));
	}

	public function renderPost() {
		$this->renderSinglePost($this->post);

		// render single post sidebar
		$this->renderSingleSidebar();
	}

	public function render() {
		header('Content-type: text/html; charset=utf-8');
		$this->renderGlobals();
		$this->renderPost();
		$this->tpl->render('nav.single');
		$this->tpl->setVar('page', 'single');
		$this->tpl->render("content.single");
		echo $this->tpl->render();
	}
}
?>
