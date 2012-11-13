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

$ajax = "";
if (isset($_POST) && isset($_POST['ajax']) ) {
	$ajax = trim($_POST['ajax']);
}
if (isset($_GET) && isset($_GET['ajax']) ) {
	$ajax = trim($_GET['ajax']);
}
if ($ajax != "") {
	# Inclusion des fonctions
	require_once(dirname(__FILE__).'/../../inc/user/prepend.php');

	if (!$core->auth->sessionExists() && !$core->hasRole('user')){
		print 'Permission denied';
		exit;
	}

	switch($ajax) {
	case 'main':
		if (!$core->hasRole('user')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/main_api.php');
		break;
	case 'account':
		if (!$core->hasRole('user')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/account_api.php');
		break;
	case 'feed':
		if (!$core->hasRole('user')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/feed_api.php');
		break;
	case 'social':
		if (!$core->hasRole('user')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/social_api.php');
		break;
	case 'tagging':
		if (!$blog_settings->get('allow_tagging_everything') && !$core->hasRole('user')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/tagging_api.php');
		break;
	case 'post':
		if (!$core->hasRole('user')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/post_api.php');
		break;
	case 'tribes':
		if (!$core->hasRole('user')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/../../admin/api/manage-tribe_api.php');
		break;
	default:
		print '<div class="flash_error">'.T_('bad call, this ajax call does not exist').'</div>';
		break;
	}
}
else {
	print 'forbidden';
}
?>
