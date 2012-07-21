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
	if (!$core->hasRole('manager') | !$core->hasPermission('administration')){
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
		<fieldset>
			<legend><?php echo T_('List of pending feeds');?></legend>
			<div class="message">
				<p><?php echo T_('List of pending feeds for the Planet');?></p>
			</div>
			<div id="pendingfeed-list"></div>
	</fieldset>

	<div id="refuse-subscription-form" style="display:none">
	<?php
	echo '<form>'.form::hidden('from',$core->auth->getInfo('user_email')).
	'<label class="required" for="subject">'.T_('Subject').' : <br />'.
	form::field('subject',30,255,html::escapeHTML(T_("Refuse subscription")), 'input').'</label><br />'.
	'<label class="required" for="content">'.T_('Comment').' : <br />'.
	form::textArea('content',80,14,html::escapeHTML(""), 'input').'</label><br />'.
	'<div class="button"><input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>'.
	'<div class="button"><input type="submit" name="send" class="add_site" value="'.T_('Send').'" /></div>'.
	'</form>';
	?>
	</div>

	<div id="accept-subscription-form" style="display:none">
	<?php
	echo '<form>'.form::hidden('from',$core->auth->getInfo('user_email')).
	'<label class="required" for="subject">'.T_('Subject').' : <br />'.
	form::field('subject',30,255,html::escapeHTML(T_("Accept subscription")), 'input').'</label><br />'.
	'<label class="required" for="content">'.T_('Comment').' : <br />'.
	form::textArea('content',80,14,html::escapeHTML(""), 'input').'</label><br />'.
	'<div class="button"><input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>'.
	'<div class="button"><input type="submit" name="send" class="add_site" value="'.T_('Send').'" /></div>'.
	'</form>';
	?>
	</div>

	<script type="text/javascript" src="meta/js/manage-pendingfeed.js"></script>
<?php
include(dirname(__FILE__).'/footer.php');

else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('auth.php?came_from='.$page_url);
endif;
?>
