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
/* Inclusion du fichier de configuration */
require_once(dirname(__FILE__).'/../inc/user/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->hasRole('user')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}

$core->tpl->importFile('index','index.tpl', dirname(__FILE__).'/tpl/');
$core->tpl->setVar('planet', array(
	"url"	=>	$blog_settings->get('planet_url'),
	"theme"	=>	$blog_settings->get('planet_theme'),
	"title"	=>	$blog_settings->get('planet_title'),
	"desc"	=>	$blog_settings->get('planet_desc'),
	"keywords"	=>	$blog_settings->get('planet_keywords'),
	"desc_meta"	=>	$blog_settings->get('planet_desc_meta'),
	"msg_info" => $blog_settings->get('planet_msg_info'),
));
$menu_selected =  array(
	'dashboard' => '',
	'profile'=> '',
	'social' => '',
	'write' => '',
	'tribes' => '');
$page = 'dashboard';

if (isset($_GET)) {
	# if user want to read a unique post
	if (isset($_GET['page']) && !empty($_GET['page'])){
		$page = trim($_GET['page']);
	}
}

$menu_selected[$page] = "selected";
$core->tpl->setVar('title', 'User dashboard');

$core->tpl->setVar('html', render_page($page));
$core->tpl->setVar('menu', $menu_selected);
echo $core->tpl->render();


else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
