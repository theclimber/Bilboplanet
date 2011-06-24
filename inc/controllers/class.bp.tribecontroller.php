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

class TribeController extends AbstractController
{
	public function __construct(&$core) {
		$this->core =& $core;
		$this->con = $core->con;
		$this->prefix = $core->prefix;
	}

	public function run() {
		global $blog_settings;

		$tribe_id = $blog_settings->get('planet_main_tribe');
		$witags = '';
		$wiusers = '';
		$wisearch = '';
		$wotags = '';
		$wousers = '';
		$wosearch = '';

		# Customizing the tribe
		if (isset($_GET)) {
			# if user want to read a unique tribe
			if (isset($_GET['id']) && !empty($_GET['id'])){
				$tribe_id = $_GET['id'];
			}
			if (isset($_GET['witags'])) {
				$witags = $_GET['witags'];
			}
			if (isset($_GET['wiusers'])) {
				$wiusers = $_GET['wiusers'];
			}
			if (isset($_GET['wisearch'])) {
				$wisearch = $_GET['wisearch'];
			}
			if (isset($_GET['wotags'])) {
				$wotags = $_GET['wotags'];
			}
			if (isset($_GET['wousers'])) {
				$wousers = $_GET['wousers'];
			}
			if (isset($_GET['wosearch'])) {
				$wosearch = $_GET['wosearch'];
			}

		}

		$this->core->tribes->setCurrentTribe($tribe_id);

		$this->core->tribes->setCurrentTags($witags, 'with');
		$this->core->tribes->setCurrentUsers($wiusers, 'with');
		$this->core->tribes->setCurrentSearch($wisearch, 'with');
		$this->core->tribes->setCurrentTags($wotags, 'without');
		$this->core->tribes->setCurrentUsers($wousers, 'without');
		$this->core->tribes->setCurrentSearch($wosearch, 'without');

		$view = new TribeView($this->core);
		$view->addJavascript('javascript/main.js');
		$view->addJavascript('javascript/jquery.boxy.js');

		# Customizing the view
		if (isset($_GET)) {
			if (isset($_GET['page']) && is_numeric($_GET['page'])) {
				$view->setPage($_GET['page']);
			}
			if (isset($_GET['nbitems']) && is_numeric($_GET['nbitems'])) {
				$view->setNbItems($_GET['nbitems']);
			}
			if (isset($_GET['popular'])) {
				$view->setPopular();
			}
			if (isset($_GET['filter']) &&
				in_array($_GET['filter'], array('day', 'week', 'month', 'all'))) {
				$view->setPeriod($_GET['filter']);
			}
		}

		# Print result on screen
		$view->render();
	}
}
