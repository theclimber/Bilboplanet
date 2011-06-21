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

class TribeView extends AbstractView
{
	protected $tribe;
	protected $page;
	protected $period;
	protected $nbitems;
	protected $popular = false;

	public function __construct(&$core)
	{
		$this->tribes = $core->tribes;

		$this->core =& $core;
		$this->theme =& $theme;

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


	public function setPage($page) {
		$this->page = $page;
	}

	public function setPeriodFilter($period) {
		$this->period = $period;
	}

	public function setNbItems($nb) {
		$this->nbitems = $nb;
	}

	public function setPopular() {
		$this->popular = true;
	}

	#######################
	# RENDER FILTER MENU
	#######################
	protected function renderPeriodFilter() {
		$this->tpl->setVar('filter_url', '&'.$this->period);
		$this->tpl->render('menu.filter');
	}

	#######################
	# RENDER PAGINATION
	#######################
	protected function renderNavigation() {
		$this->tpl->setVar('page', '&'.$this->page);
		if($params["page"] == 0 & $rs->count()>=10) {
			# if we are on the first page
			$this->tpl->render('pagination.up.next');
			$this->tpl->render('pagination.low.next');
		} elseif($params["page"] == 0 & $rs->count()<10) {
			# we don't show any button
		} else {
			if($rs->count() == 0 | $rs->count() < 10) {
				# if we are on the last page
				$this->tpl->render('pagination.up.prev');
				$this->tpl->render('pagination.low.prev');
			} else {
				$this->tpl->render('pagination.up.prev');
				$this->tpl->render('pagination.up.next');
				$this->tpl->render('pagination.low.prev');
				$this->tpl->render('pagination.low.next');
			}
		}
	}

	protected function renderTribe() {
		$posts = $this->tribe->getCurrentTribePosts(10);
		$this->tpl = showPosts($rs, $this->tpl, $search_value, $popular);
		$this->tpl->render("content.posts");
	}

	public function render() {
		header('Content-type: text/html; charset=utf-8');
		$this->renderGlobals();
		$this->renderNavigation();
		$this->renderPeriodFilter();
		$this->renderTribe();
		echo $this->tpl->render();
	}
}
?>
