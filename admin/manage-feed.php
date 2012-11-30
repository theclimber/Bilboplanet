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
debutCache();

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>
<div id="BP_page" class="page">
	<div class="inpage">

<div id="flash-log" style="display:none;">
	<div id="flash-msg"><!-- spanner --></div>
</div>

<div id="add-user-form" class="button br3px" style="padding: 5px;"><a onclick="javascript:openUserAdd()">
	<?php echo T_('Add an user'); ?></a>
</div>
<div class="button br3px" id="add-form"><a onclick="javascript:openAdd()">
	<?php echo T_('Add a feed'); ?></a>
</div>
<div class="button br3px" id="filter-form"><a onclick="javascript:openFilter()">
	<?php echo T_('Filter feedlist'); ?></a>
</div></p>
</p>

<fieldset id="adduser-field" style="display:none"><legend><?php echo T_('Add an user');?></legend>
	<div class="message">
		<p><?php echo T_('To add a new member, fill out the form below.');?></p>
	</div><br />

<?php
echo
'<form id="adduser_form">'.

'<label class="required" for="user_id">'.T_('User id').' : '.
form::field('user_id',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: Frankenstein').'</span><br />'.

'<label for="fullname">'.T_('Fullname').' : '.
form::field('fullname',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: Jean Dupont').'</span><br />'.

'<label class="required" for="email">'.T_('Email').' : '.
form::field('email',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: xxx@yyy.zzz').'</span><br />'.

'<label class="required" for="password">'.T_('Password').' : '.
form::password('password',30,255, '', 'input').'</label>
<span class="description">'.T_('Minimum 4 chars').'</span><br />'.

'<label class="required" for="password2">'.T_('Confirm password').' : '.
form::password('password2',30,255, '', 'input').'</label><br />'.

'<label class="required" for="site">'.T_('Website (Optional)').' : '.
form::field('site',30,255,html::escapeHTML(), 'input').'</label>
<span class="description">'.T_('ex: http://www.example.com').'</span><br /><br />'.

'<div class="button br3px"><input type="reset" class="reset" name="reset" onClick="this.form.reset()" value="'.T_('Reset').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_user" class="add_user" value="'.T_('Add').'" /></div>'.
'<div class="button br3px close-button"><a class="close" onclick="javascript:closeUserAdd()">'.T_('Close').'</a></div>'.
'</form>';
?>

</fieldset>

<fieldset id="filterfeed-field" style="display:none"><legend><?php echo T_('Filter');?></legend>
	<br/>

<?php

# Traitement de la liste
$rs = $core->con->select('SELECT DISTINCT
		'.$core->prefix.'user.user_id,
		user_fullname,
		lower(user_fullname) AS user_fullname_lower
	FROM '.$core->prefix.'user, '.$core->prefix.'feed
	WHERE '.$core->prefix.'feed.user_id = '.$core->prefix.'user.user_id
	ORDER BY lower(user_fullname) ASC;');
$users = array();
while($rs->fetch()) {
	$users["$rs->user_fullname"] = urlencode($rs->user_id);
}
$users['-- '.T_('All').' --'] = 'all';

$status = array();
$status[T_('Active feeds')] = 1;
$status[T_('Inactive feeds')] = 0;
$status['-- '.T_('All').' --'] = "all";
if ($blog_settings->get('auto_feed_disabling')){
	$status[T_('Auto disabled feeds')] = 2;
}

echo
'<form id="filterfeed_form">'.

'<label class="required" for="fuser_id">'.T_('User id').' : '.
form::combo('fuser_id',$users,0, 'input','','').'</label><br /><br />'.

'<label class="required" for="feed_status">'.T_('Feed status').' : '.
form::combo('feed_status',$status,0, 'input','','').'</label><br /><br />';

echo
'<div class="button br3px"><input type="submit" name="filter_feed" class="valide" value="'.T_('Filter').'" /></div>'.
'<div class="button br3px close-button"><a class="close" onclick="javascript:closeFilter()">'.T_('Close').'</a></div>'.
'</form>';
?>
</fieldset>


<fieldset id="addfeed-field" style="display:none"><legend><?php echo T_('Add a feed');?></legend>
	<div class="message">
		<p><?php echo T_("Manage member's feeds. Notice that you can only add a feed to a user who has a website. So you first need to create the user and add a site to him"); ?></p>
	</div><br/>

<?php


# Traitement de la liste
$rs = $core->con->select('SELECT DISTINCT
		'.$core->prefix.'user.user_id,
		user_fullname,
		lower(user_fullname) AS user_fullname_lower
	FROM '.$core->prefix.'user, '.$core->prefix.'site
	WHERE '.$core->prefix.'site.user_id = '.$core->prefix.'user.user_id
	ORDER BY lower(user_fullname) ASC;');
$users = array();
$users[T_('-- Choose an user --')] = '';
while($rs->fetch()) {
	$users["$rs->user_fullname"] = urlencode($rs->user_id);
}

echo
'<form id="addfeed_form">'.

'<label class="required" for="user_id">'.T_('User id').' : '.
form::combo('user_id',$users,'', 'input','','',"onchange=\"javascript:updateSiteCombo()\"").'</label><br /><br />'.

'<label class="required" for="site_id">'.T_('Site id').' : '.
form::combo('site_id',array(T_('-- Choose an user id --') => ''),'', 'input').'</label>
<span class="description">'.T_('Choose the website of the feed').'</span><br />'.

'<label class="required" for="feed_url">'.T_('Full feed URL').' : '.
form::field('feed_url',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: http://www.bilboplanet.com/feed/atom/').'</span><br />'.

'<label class="required" for="feed_name">'.T_('Feed name').' : '.
form::field('feed_name',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: gnu/Linux posts').'</span><br />';

if ($blog_settings->get('planet_moderation')) {
	echo '<label class="required" for="feed_trust">'.T_('Trusted URL').' : '.
	form::combo('feed_trust',array('true' => '1', 'false' => '0'),'true', 'input').'</label><br /><br />';
}

echo
'<div class="button br3px"><input type="reset" class="reset" name="reset" value="'.T_('Reset').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_feed" class="valide" value="'.T_('Add').'" /></div>'.
'<div class="button br3px close-button"><a class="close" onclick="javascript:closeAdd()">'.T_('Close').'</a></div>'.
'</form>';
?>
</fieldset>

<fieldset><legend><?php echo T_('Manage feeds');?></legend>
	<div class="message">
		<p><?php echo T_('Manage member feed.');?></p>
	</div>
	<div id="feed-list"></div>
</fieldset>

<div id="feed-edit-form" style="display:none">
<?php
echo '<form>'.
form::hidden('ef_id','').

'<label class="required" for="ef_user_id">'.T_('User id').' : '.
form::field('ef_user_id',30,255,html::escapeHTML(""), 'input').'</label><br />'.

'<label for="ef_name">'.T_('Feed name').' : '.
form::field('ef_name',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: GNU/Linux posts').'</span><br />'.

'<label class="required" for="ef_url">'.T_('Feed URL').' : '.
form::field('ef_url',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: http://www.bilboplanet.com/feed/').'</span><br />'.

'<div class="button br3px"><input type="button" class="notvalide" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_user" class="valide" value="'.T_('Update').'" /></div>'.
'</form>';
?>
</div>

<div id="tag-feed-form" style="display:none">
<?php
echo '<form>'.

'<label class="required" for="content">'.T_('Tags').' : <br />'.
form::field('tags',30,255,html::escapeHTML(""), 'input').'</label><br/>
<span class="description"><i>'.T_('Comma separated tags (ex: linux,web,event)').'</i></span><br /><br />'.

'<div class="button"><input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>'.
'<div class="button"><input type="submit" name="send" class="add_site" value="'.T_('Send').'" /></div>'.
'</form>';
?>
</div>


<script type="text/javascript" src="meta/js/manage-feed.js"></script>
<script type="text/javascript" src="meta/js/jquery.boxy.js"></script>
<?php
include(dirname(__FILE__).'/footer.php');
finCache();
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
