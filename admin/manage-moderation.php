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
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager') | !$core->hasPermission('moderation')){
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

<fieldset><legend><?php echo T_('Moderation interface');?></legend>
		<div class="message">
			<p><?php echo T_('Moderate some posts.');?></p>
		</div><br />
		<?php
		if(!$blog_settings->get('planet_moderation')) {
			echo T_('The moderation functionnality is disabled. If you want to enable it, please go to the configuration page')." (";
			echo "<a href=\"manage-option.php\" class=\"tips\" rel=\"".T_('Planet Configuration')."\" id=\"planet\" >".T_('Planet')."</a>";
			echo ").";
			exit;
		}
		?>


	<div class="selector">
	<form id="filter-form">
		<input type="hidden" name="ajax" value="moderation" />
		<input type="hidden" name="action" value="filter" />
		<label for="nb_items"><?php echo T_("Elements to display"); ?></label>
		<input type="text" name="nb_items" value="10" class="input" size=3 />
		<label for="user_id"><?php echo T_("Filter by user"); ?></label>
		<select name="user_id" class="userscombo">
<?php
$rs = $core->con->select("SELECT user_id, user_fullname FROM ".$core->prefix."user ORDER BY lower(user_fullname) ASC");

while($rs->fetch()) {
	if($_POST['user_id'] == urlencode($rs->user_id)) {
		echo "\t\t".'<option value="'.urlencode($rs->user_id).'" selected>'.$rs->user_fullname.'</option>';
	} else {
		echo "\t\t".'<option value="'.urlencode($rs->user_id).'">'.$rs->user_fullname.'</option>';
	}
	echo "\n";
}
if(!isset($_POST['user_id']) || $_POST['user_id'] == "0") {
	echo "\t\t".'<option value="" selected>Tous</option>';
} else {
	echo "\t\t".'<option value="">Tous</option>';
}
?>

		</select>
		<div class="button">
		<input type="submit" value="<?php echo T_('Filter'); ?>" name="filter" />
		</div>
	</form>
	</div>
	<div id="post-list" class="post-list"></div>
</fieldset>

<div id="refuse-post-form" style="display:none">
<?php

echo '<form>'.form::hidden('from',$core->auth->getInfo('user_email')).

'<label class="required" for="subject">'.T_('Subject').' : <br />'.
form::field('subject',30,255,html::escapeHTML(T_("Moderation of your post")), 'input').'</label><br />'.

'<label class="required" for="content">'.T_('Comment').' : <br />'.
form::textArea('content',80,14,html::escapeHTML(""), 'input').'</label><br />'.

'<div class="button"><input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>'.
'<div class="button"><input type="submit" name="send" class="add_site" value="'.T_('Send').'" /></div>'.
'</form>';
?>
</div>

<script type="text/javascript" src="meta/js/manage-moderation.js"></script>
<?php
include(dirname(__FILE__).'/footer.php');
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
