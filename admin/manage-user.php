<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
* Blog : blog.bilboplanet.com
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

<div id="add-form" class="button br3px"><a onclick="jQuery('#adduser-field').css('display', '')">
	<?php echo T_('Add an user'); ?></a>
</div></p>

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
form::password('password',30,255, '', 'input').'</label><br />'.

'<label class="required" for="password2">'.T_('Confirm password').' : '.
form::password('password2',30,255, '', 'input').'</label><br />'.

'<label class="required" for="site">'.T_('Website (Optional)').' : '.
form::field('site',30,255,html::escapeHTML(), 'input').'</label>
<span class="description">'.T_('ex: http://www.example.com').'</span><br /><br />'.

'<div class="button br3px"><input type="reset" class="reset" name="reset" onClick="this.form.reset()" value="'.T_('Reset').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_user" class="add_user" value="'.T_('Add').'" /></div>'.
'<div class="button br3px close-button"><a class="close" onclick="jQuery(\'#adduser-field\').css(\'display\', \'none\')">'.T_('Close').'</a></div>'.
'</form>';
?>
	
</fieldset>

<fieldset><legend><?php echo T_('List of the users');?></legend>
		<div class="message">
			<p><?php echo T_('List of members of the Planet');?></p>
		</div>
<div id="users-list"></div>
</fieldset>

<div id="user-edit-form" style="display:none">
<?php
echo '<form>'.

'<label class="required" for="euser_id">'.T_('User id').' : '.
form::field('euser_id',30,255,html::escapeHTML(""), 'input').'</label><br />'.

'<label for="efullname">'.T_('Fullname').' : '.
form::field('efullname',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: Jean Dupont').'</span><br />'.

'<label class="required" for="eemail">'.T_('Email').' : '.
form::field('eemail',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: xxx@yyy.zzz').'</span><br />'.

'<label class="required" for="password">'.T_('New password').' : '.
form::password('password',30,255, '', 'input').'</label><br />'.

'<label class="required" for="password2">'.T_('Confirm new password').' : '.
form::password('password2',30,255, '', 'input').'</label><br/>'.
'<span class="description">'.T_('You can leave the password field blank if you don\'t want to change it').'</span><br /><br />'.

'<div class="button br3px"><input type="button" class="notvalide" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" class="valide" name="add_user" class="add_user" value="'.T_('Update').'" /></div>'.
'</form>';
?>
</div>

<div id="add-site-form" style="display:none">
<?php
echo '<form>'.

'<label class="required" for="suser_id">'.T_('User id').' : '.
form::field('suser_id',30,255,html::escapeHTML(""), 'input').'</label><br />'.

'<label for="s_name">'.T_('Site name').' : '.
form::field('s_name',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: Bilboplanet blog').'</span><br />'.

'<label class="required" for="s_url">'.T_('Site URL').' : '.
form::field('s_url',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: http://blog.bilboplanet.com').'</span><br />'.

'<div class="button br3px"><input type="button" class="notvalide" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_site" class="valide" value="'.T_('Add').'" /></div>'.
'</form>';
?>
</div>


<script type="text/javascript" src="meta/js/manage-user.js"></script>
<script type="text/javascript" src="meta/js/jquery.boxy.js"></script>
<?php
include(dirname(__FILE__).'/footer.php');
finCache();
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('auth.php?came_from='.$page_url);
endif;
?>
