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
require_once(dirname(__FILE__).'/../inc/admin/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->auth->superUser()){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="BP_page" class="page">
	<div class="inpage">

<div id="flash-log" style="display:none;">
	<div id="flash-msg"><!-- spanner --></div>
</div>


<fieldset><legend><?php echo T_('Manage user permissions');?></legend>
		<div class="message">
			<p><?php echo T_('Check user statuses and configure their permissions');?></p>
		</div>
<div id="users-list"></div>
</fieldset>


<script type="text/javascript" src="meta/js/manage-permissions.js"></script>
<script type="text/javascript" src="meta/js/jquery.boxy.js"></script>
<?php
include(dirname(__FILE__).'/footer.php');
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
