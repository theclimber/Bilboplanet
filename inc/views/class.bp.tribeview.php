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
include_once (dirname(__FILE__).'/class.bp.abstractview.php');

class TribeView extends AbstractView
{
	protected $tribe;
	protected $page = 0;
	protected $period = null;
	protected $nbitems = 10;
	protected $popular = false;

	public function __construct(&$core)
	{
		$this->instantiateTPL();
		$this->tribe = $core->tribes;
		$this->prefix = $core->prefix;
		$this->con = $core->con;
		$this->page = "tribe";
		$this->core =& $core;
	}


	public function setPage($page) {
		$this->page = $page;
	}

	public function setPeriod($period) {
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
		$this->tpl->render('menu.filter');
	}

	#######################
	# RENDER PAGINATION
	#######################
	protected function renderNavigation($count) {
		if($this->page == 0 && $count >= $this->nbitems) {
			# if we are on the first page
			$this->tpl->render('pagination.up.next');
			$this->tpl->render('pagination.low.next');
		} elseif($this->page == 0 && $count< $this->nbitems) {
			# we don't show any button
		} else {
			if($count == 0 || $count < $this->nbitems) {
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
		global $blog_settings;
		$post_list = $this->tribe->getCurrentTribePosts(
			$this->nbitems,
			$this->page * $this->nbitems,
			$this->period,
			$this->popular
			);
		foreach ($post_list as $id=>$post){
			$post->setSearchWith($this->tribe->getCurrentSearchWith());
			if($this->popular) {
				$post->setStripTags();
			}

			$this->renderSinglePost($post, false);

			# Render summary
			$line = array(
				"date" => $post->getPubdate(),
				"title" => $post->getTitle(),
				"short_title" => $post->getShortTitle(),
				"url" => "#post".$id);
			$this->tpl->setVar('summary', $line);
			$this->tpl->render('summary.line');
		}
		$this->tpl->render('summary.block');
		return count($post_list);
	}

	protected function renderSearchBox() {
		$with = $this->tribe->getCurrentSearchWith();
		$without = $this->tribe->getCurrentSearchWithout();
		if (count($with) > 0 || count($without) > 0) {
			$s_with = $with[0];
			$this->tpl->setVar('search_value',$s_with);
			$this->tpl->render('search.box');
			$this->tpl->render('search.line');
		} else {
			$this->tpl->render('search.box');
		}
	}

	public function render() {
		header('Content-type: text/html; charset=utf-8');
		$this->renderGlobals();
		$nbitems = $this->renderTribe();
		$this->renderNavigation($nbitems);
		$this->renderPeriodFilter();
		$this->renderSearchBox();
		$this->tpl->render("content.posts");
		echo $this->tpl->render();
	}
}
?>
