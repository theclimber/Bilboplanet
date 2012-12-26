<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : http://chili.kiwais.com/projects/bilboplanet
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
?>
<?php

if (!isset($styles)) {
	$styles = array();
}
if (!isset($scripts)) {
	$scripts = array();
}

$params = array(
	'title'=>$blog_settings->get('planet_title')
	);
#######################
# RENDER MENU
#######################
if ($blog_settings->get('planet_vote')) {
	$core->tpl->render('menu.votes');
}
if ($blog_settings->get('planet_contact_page')) {
	$core->tpl->render('menu.contact');
}
if ($blog_settings->get('planet_subscription')) {
	$core->tpl->render('menu.subscription');
}

if ($core->auth->sessionExists() ) {
	$login = array(
	'username' => $core->auth->userID()
		);
	$core->tpl->setVar('login', $login);
	if ($core->hasRole('manager')) {
		$core->tpl->render('page.loginadmin');
	}

	if ($user_settings != null && $user_settings->get('social.shaarli')) {
		$core->tpl->setVar('shaarli_instance', $user_settings->get('social.shaarli.instance'));
		$core->tpl->render('menu.shaarli');
	}
	$core->tpl->render('page.loginbox');
} else {
	$core->tpl->setVar('came_from',curPageURL());
	$core->tpl->render('login.popup');
}

#######################
# RENDER TRIBES MENU
#######################
$user_id = '';
if ($core->auth->sessionExists()) {
	$user_id = $core->auth->userID();
}

if (!isset($current_page) || in_array($current_page, array('portal', 'list', 'popular', 'users', 'tags', 'stats'))) {
	if ($user_id != '') {
		$user_condition = "OR user_id = '".$user_id."'";
	}
	$sql_tribes = "SELECT
			user_id,
			tribe_name,
			tribe_id,
			tribe_icon
		FROM ".$core->prefix."tribe
		WHERE (user_id = 'root' ".$user_condition.")
			AND visibility = 1
		ORDER BY ordering ASC
			";
	//print $sql_tribes;
	//exit;

	$tribe_id = $_GET['tribe_id'];
	$rs = $core->con->select($sql_tribes);
	while($rs->fetch()) {
		$selected='';
		if (!empty($tribe_id) && $tribe_id==$rs->tribe_id){
			$selected='selected';
		}
        $tribe_icon = getTribeIcon($rs->tribe_id,$rs->tribe_name,$rs->tribe_icon);
		$core->tpl->setVar('tribe', array(
			'id' => $rs->tribe_id,
			'name' => $rs->tribe_name,
			'icon' => $tribe_icon,
			'selected' => $selected
			));
		$core->tpl->render('menu.tribes');
	}

	$nav = array(
		'portal' => '',
		'popular' => '',
		'list' => '');
	if ($tribe_id == '') {
		$nav[$current_page] = 'selected';
	}
	$core->tpl->setVar('nav',$nav);
	$core->tpl->render('menu.nav');
	$core->tpl->render('main.menu');
	$core->tpl->render('sidebar.action');
}

#######################
# RENDER SIDEBAR
#######################
global $current_page;
if ($current_page == "list") {
	if ($blog_settings->get('planet_msg_info')) {
		$core->tpl->render('main.alert');
	}
	$core->tpl->render('postlist.state');
}
if (in_array($current_page, array('list','single'))) {
	$core->tpl->render('search.box');
	$core->tpl->render("content.topbar");
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

$widget_path = dirname(__FILE__).'/widgets';
$widget_files = json_decode($blog_settings->get('planet_widget_files'));
if (is_array($widget_files)) {
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
				$core->tpl->setVar("sidebar-widget", array(
					'title' => $wgt['title'],
					'html' => $wgt['html'],
					'id' => $wgt['id']));
				$core->tpl->render('sidebar.widget');
			}
			if ($file->{"position"} == "footer") {
				$core->tpl->setVar("footer-widget", array(
					'title' => $wgt['title'],
					'html' => $wgt['html'],
					'id' => $wgt['id']));
				$core->tpl->render('footer.widget');
			}
		}
	}
}
#####################
# RENDER SOCIAL
#####################
if ($user_settings != null) {
	if ($user_settings->get('social.google')) {
		$core->tpl->render('social.google.script');
	}
	if ($user_settings->get('social.shaarli')) {
		$type = $user_settings->get('social.shaarli.type');
		if ($type == 'local') {
			$core->tpl->setVar('shaarli_instance',
				$blog_settings->get('planet_url').'/shaarli');
		} else {
			$core->tpl->setVar('shaarli_instance',
				$user_settings->get('social.shaarli.instance'));
		}
		$core->tpl->render('social.shaarli.script');
	}
	if ($user_settings->get('social.twitter')) {
		$core->tpl->render('social.twitter.script');
	}
	if ($user_settings->get('social.reddit')) {
		$core->tpl->render('social.reddit.script');
	}
}

#####################
# RENDER FOOTER
#####################
$core->tpl->setVar('footer1', array(
	'text' => T_('Powered by Bilboplanet'),
	'url' => 'http://www.bilboplanet.com'));
$core->tpl->setVar('footer2', array(
	'text' => T_('Valid CSS - Xhtml'),
	'url' => 'http://validator.w3.org/check?verbose=1&uri='.BP_PLANET_URL));
$core->tpl->setVar('footer3', array(
	'text' => T_('Designed by BilboPlanet'),
	'url' => 'http://www.bilboplanet.com'));


######################
# RENDER STYLES
######################
foreach ($styles as $css) {
	$core->tpl->setVar('css_file', $css);
	$core->tpl->render('css.import');
}



######################
# RENDER JAVASCRIPT
######################
foreach ($scripts as $js) {
	$core->tpl->setVar('js_file', $js);
	$core->tpl->render('js.import');
}
?>
