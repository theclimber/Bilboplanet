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

# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/admin/prepend.php');

# Check Session
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager') | !$core->hasPermission('configuration')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>
<!-- Javascript -->
<script type="text/javascript" src="meta/js/manage-option.js"></script>
<!-- End Of Javascript -->

<div id="BP_page" class="page">
	<div class="inpage">
		<div id="flash-log" style="display:none;">
			<div id="flash-msg"><!-- spanner --></div>
		</div>
		<div id="options-button-update" class="button br3px">
			<?php echo '<a class="edit" href="#" title="'.T_('Update').'" onclick="javascript:formopt();">'.T_('Update').'</a>';?>
		</div>
		<div id="options-button-close"  class="button br3px">
			<?php echo '<a class="close-button" href="#" title="'.T_('Close').'" onclick="javascript:listopt();">'.T_('Close').'</a>';?>
		</div>
<?php
$joined = $blog_settings->get("planet_joined_community");
if (!$joined) {
?>
		<div id="options-button-join" class="button br3px">
			<?php echo '<a class="join" href="#" title="'.T_('Join Bilboplanet Community').'" onclick="javascript:join();">'.T_('Join Bilboplanet Community').'</a>';?>
		</div>
<?php
}
?>
		<br /><br />
		<fieldset>
			<legend><?php echo T_('Options');?></legend>
				<div class="message">
					<p><?php echo T_('Configuration settings Planet.');?></p>
				</div>
			<br />
			<div id="options-list" style="display:none;"><!-- List --></div>
			<div id="options-form" style="display:none;"><!-- Form To Update --></div>
		</fieldset>
<?php include(dirname(__FILE__).'/footer.php');
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
