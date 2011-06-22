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

abstract class AbstractView
{
	protected $tpl;
	protected $prefix;
	protected $con;
	protected $core;
	protected $styles = array();
	protected $scripts = array();
	protected $search;

	public function addStylesheet($css) {
		$this->styles[] = $css;
	}

	public function addJavascript($js) {
		$this->scripts[] = $js;
	}

	#######################
	# RENDER SIDEBAR
	#######################
	protected function renderSidebar() {
		global $blog_settings;
		if ($blog_settings->get('planet_msg_info')) {
			$this->tpl->render('sidebar.alert');
		}
		if($blog_settings->get('planet_vote')) {
			$this->tpl->render('sidebar.popular');
		}

		$sql_side = "SELECT
			user_fullname as fullname,
			".$this->prefix."user.user_id as id,
			".$this->prefix."site.site_url as site_url,
			".$this->prefix."site.site_name as site_name
			FROM ".$this->prefix."user, ".$this->prefix."site
			WHERE ".$this->prefix."user.user_id = ".$this->prefix."site.user_id
			AND user_status = '1'
			ORDER BY lower(user_fullname)";
		$rs_side = $this->con->select($sql_side);

		while ($rs_side->fetch()) {
			$user_info = array(
				"id" => urlencode($rs_side->f('id')),
				"fullname" => $rs_side->f('fullname'),
				"site_url" => $rs_side->f('site_url')
				);
			$this->tpl->setVar("user", $user_info);
			$this->tpl->render("sidebar.users.list");
		}
	}

	#######################
	# RENDER MENU
	#######################
	protected function renderMenu() {
		global $blog_settings;
		if ($blog_settings->get('planet_vote')) {
			$this->tpl->render('menu.votes');
		}
		if ($blog_settings->get('planet_contact_page')) {
			$this->tpl->render('menu.contact');
		}
		if ($blog_settings->get('planet_subscription')) {
			$this->tpl->render('menu.subscription');
		}

		if ($this->core->auth->sessionExists() ) {
			$login = array(
			'username' => $this->core->auth->userID()
				);
			$this->tpl->setVar('login', $login);
			if ($this->core->hasRole('manager')) {
				$this->tpl->render('page.loginadmin');
			}
			$this->tpl->render('page.loginbox');
		}
	}

	######################
	# RENDER STYLES
	######################
	protected function renderStyles() {
		foreach ($this->styles as $css) {
			$this->tpl->setVar('css_file', $css);
			$this->tpl->render('css.import');
		}
	}

	######################
	# RENDER JAVASCRIPT
	######################
	protected function renderJavascript() {
		foreach ($this->scripts as $js) {
			$this->tpl->setVar('js_file', $js);
			$this->tpl->render('js.import');
		}
	}

	#####################
	# RENDER FOOTER
	#####################
	protected function renderFooter() {
		global $blog_settings;
		$this->tpl->setVar('footer1', array(
			'text' => T_('Powered by Bilboplanet'),
			'url' => 'http://www.bilboplanet.com'));
		$this->tpl->setVar('footer2', array(
			'text' => T_('Valid CSS - Xhtml'),
			'url' => 'http://validator.w3.org/check?verbose=1&uri='.$blog_settings->get('planet_url')));
		$this->tpl->setVar('footer3', array(
			'text' => T_('Designed by BilboPlanet'),
			'url' => 'http://www.bilboplanet.com'));
	}

	#####################
	# RENDER WIDGETS
	#####################
	//TODO : it would be more reliable if we put in the settings table the widgets to load and where
	// if we put a json dict in the database with for each element in the list:
	// * the place where we want to place the widget (sidebar, footer)
	// * the file to load
	// * the order in which is have to load
	// After that we need to check, before loading the tpl, if the widget function is returning a well formatted array
	// * if yes => show it
	// * if not => show an error in the widget place
	protected function renderWidgets() {
		global $blog_settings;
		$widget_path = dirname(__FILE__).'/widgets';
		$widget_files = array();
		if ($blog_settings->get('planet_widget_files')) {
			$widget_files = json_decode($blog_settings->get('planet_widget_files'));
		}
		foreach ($widget_files as $file){
			if (is_dir($widget_path) && is_file($widget_path.'/'.$file->{'name'})) {
				# Build an array of available widgets
				require_once($widget_path.'/'.$file->{'name'});
				$wgt = getWidget();
				foreach ($wgt['styles'] as $sty) {
					$styles[] = $sty;
				}
				foreach ($wgt['scripts'] as $spt) {
					$scripts[] = $spt;
				}
				if ($file->{'position'} == "sidebar") {
					$this->tpl->setVar("sidebar-widget", array(
						'title' => $wgt['title'],
						'html' => $wgt['html'],
						'id' => $wgt['id']));
					$this->tpl->render('sidebar.widget');
				}
				if ($file->{"position"} == "footer") {
					$this->tpl->setVar("footer-widget", array(
						'title' => $wgt['title'],
						'html' => $wgt['html'],
						'id' => $wgt['id']));
					$this->tpl->render('footer.widget');
				}
			}
		}
	}

	protected function renderGlobals() {
		$this->renderSidebar();
		$this->renderMenu();
		$this->renderStyles();
		$this->renderJavascript();
		$this->renderFooter();
		$this->renderWidgets();
	}

	abstract public function render();
}
?>
