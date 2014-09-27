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
if(isset($_POST) && isset($_POST['ajax'])) {
	# Inclusion des fonctions
	require_once(dirname(__FILE__).'/../../inc/admin/prepend.php');

	if (!$core->auth->sessionExists() && !$core->hasRole('manager')){
		print 'Permission denied';
		exit;
	}

	switch(trim($_POST['ajax'])) {
	case 'database':
		if (!$core->hasPermission('configuration')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-database_api.php');
		break;
	case 'user':
		if (!$core->hasPermission('administration')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-user_api.php');
		break;
	case 'account':
		if (!$core->hasRole('manager')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-account_api.php');
		break;
	case 'site':
		if (!$core->hasPermission('administration')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-site_api.php');
		break;
	case 'feed':
		if (!$core->hasPermission('administration')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-feed_api.php');
		break;
	case 'tribe':
		if (!$core->hasPermission('administration')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-tribe_api.php');
		break;
	case 'moderation':
		if (!$core->hasPermission('moderation')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-moderation_api.php');
		break;
	case 'tagging':
		if (!$core->hasPermission('moderation')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-tagging_api.php');
		break;
	case 'selection':
		if (!$core->hasPermission('moderation')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-selection_api.php');
		break;
	case 'permissions':
		if (!$core->auth->superUser()){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-permissions_api.php');
		break;
	case 'option':
		if (!$core->hasPermission('configuration')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-option_api.php');
		break;
	case 'pendingfeed':
		if (!$core->hasPermission('administration')){
			print 'Permission denied';
			exit;
		}
		require_once(dirname(__FILE__).'/manage-pendingfeed_api.php');
		break;
	default:
		print '<div class="flash error">'.T_('bad call, this ajax call does not exist').'</div>';
		break;
	}
}
else {
	print '- forbidden -';
}
?>
