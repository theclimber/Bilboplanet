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

require dirname(__FILE__).'/inc/admin/prepend.php';
$blog_settings = new bpSettings($core, 'root');

if (isset($_GET['came_from']) && !empty($_GET['came_from'])){
	$came_from = $_GET['came_from'];
}
elseif (isset($_POST['came_from']) && !empty($_POST['came_from'])){
	$came_from = $_POST['came_from'];
} else {
	$came_from = 'index.php';
}

# If we have a session cookie, go to index.php
if (isset($_SESSION['sess_user_id']))
{
	http::redirect($came_from);
}

$page_url = http::getHost().$_SERVER['REQUEST_URI'];

$change_pwd = $core->auth->allowPassChange() && isset($_POST['new_pwd']) && isset($_POST['new_pwd_c']) && isset($_POST['login_data']);
$login_data = !empty($_POST['login_data']) ? $_POST['login_data'] : null;
$recover = $core->auth->allowPassChange() && !empty($_REQUEST['recover']);
$akey = $core->auth->allowPassChange() && !empty($_GET['akey']) ? $_GET['akey'] : null;
$user_id = $user_pwd = $user_key = $user_email = null;
$err = $msg = null;


# If we have POST login informations, go throug auth process
if (!empty($_POST['user_id']) && !empty($_POST['user_pwd']))
{
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
	$user_pwd = !empty($_POST['user_pwd']) ? $_POST['user_pwd'] : null;

	$sql = "SELECT user_id FROM ".$core->prefix."user WHERE lower(user_id) = '".strtolower($user_id)."'";
	$rs = $core->con->select($sql);
	if(!$rs->isEmpty()) {
		$user_id = $rs->f('user_id');
	}
}
# If we have COOKIE login informations, go throug auth process
elseif (isset($_COOKIE['bp_admin']) && strlen($_COOKIE['bp_admin']) == 104)
{
	# If we have a remember cookie, go through auth process with user_key
	$user_id = substr($_COOKIE['bp_admin'],40);
	$user_id = @unpack('a32',@pack('H*',$user_id));
	if (is_array($user_id))
	{
		$user_id = $user_id[1];
		$user_key = substr($_COOKIE['bp_admin'],0,40);
		$user_pwd = null;
	}
	else
	{
		$user_id = null;
	}
}

# Recover password
if ($recover && !empty($_POST['user_id']) && !empty($_POST['user_email']))
{
	$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
	$user_email = !empty($_POST['user_email']) ? $_POST['user_email'] : '';
	try
	{
		$recover_key = $core->auth->setRecoverKey($user_id,$user_email);

		$subject = mail::B64Header('Bilboplanet'.T_('Password reset'));
		$message =
		T_('Someone has requested to reset the password for the following site and username.')."\n\n".
		$page_url."\n".T_('User_id:').' '.$user_id."\n\n".
		T_('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.')."\n".
		$page_url.'?akey='.$recover_key;

		$headers[] = 'From: bilboplanet@'.$_SERVER['HTTP_HOST'];
		$headers[] = 'Content-Type: text/plain; charset=UTF-8;';

		mail::sendMail($user_email,$subject,$message,$headers);
		$msg = sprintf(T_('The e-mail was sent successfully to %s.'),$user_email);
	}
	catch (Exception $e)
	{
		$err = $e->getMessage();
	}
}
# Send new password
elseif ($akey)
{
	try
	{
		$recover_res = $core->auth->recoverUserPassword($akey);

		$subject = mb_encode_mimeheader('Bilboplanet '.T_('Your new password'),'UTF-8','B');
		$message =
		T_('Username:').' '.$recover_res['user_id']."\n".
		T_('Password:').' '.$recover_res['new_pass']."\n\n".
		preg_replace('/\?(.*)$/','',$page_url);

		$headers[] = 'From: bilboplanet@'.$_SERVER['HTTP_HOST'];
		$headers[] = 'Content-Type: text/plain; charset=UTF-8;';

		mail::sendMail($recover_res['user_email'],$subject,$message,$headers);
		$msg = T_('Your new password is in your mailbox.');
	}
	catch (Exception $e)
	{
		$err = $e->getMessage();
	}
}
# Change password and retry to log
elseif ($change_pwd and $data = unserialize(base64_decode($_POST['login_data'])))
{
	# Check login informations
	$check_user = false;
	if (isset($data['cookie_admin']) && strlen($data['cookie_admin']) == 104)
	{
		$user_id = substr($data['cookie_admin'],40);
		$user_id = @unpack('a32',@pack('H*',$user_id));
		if (is_array($user_id))
		{
			$user_id = $user_id[1];
			$user_key = substr($data['cookie_admin'],0,40);
			$check_user = $core->auth->checkUser($user_id,null,$user_key) === true;
		}
	}

	try
	{
		if (!$core->auth->allowPassChange() || !$check_user) {
			$change_pwd = false;
			throw new Exception();
		}

		if ($_POST['new_pwd'] != $_POST['new_pwd_c']) {
			throw new Exception(T_("Passwords don't match"));
		}

		if ($core->auth->checkUser($user_id,$_POST['new_pwd']) === true) {
			throw new Exception(T_("You didn't change your password."));
		}

		$cur = $core->con->openCursor($core->prefix.'user');
		$cur->user_change_pwd = 0;
		$cur->user_pwd = $_POST['new_pwd'];
		$core->updUser($core->auth->userID(),$cur);

		$core->session->start();
		$_SESSION['sess_user_id'] = $user_id;
		$_SESSION['sess_browser_uid'] = http::browserUID('BP_MASTER_KEY');

		if (!empty($data['user_remember']))
		{
			setcookie('bp_admin',$data['cookie_admin'],strtotime('+15 days'),'','');
		}

		http::redirect($came_from);
	}
	catch (Exception $e)
	{
		$err = $e->getMessage();
	}
}
# Try to log
elseif ($user_id !== null && ($user_pwd !== null || $user_key !== null))
{
	if (check_email_address($user_id)) {
		$rs_user = $core->con->select("SELECT user_id FROM ".$core->prefix."user WHERE user_email = '".$user_id."'");
		if ($rs_user->count() == 1) {
			$user_id = $rs_user->user_id;
		}
	}
	# We check the user
	$check_user = $core->auth->checkUser($user_id,$user_pwd,$user_key) === true;

	$cookie_admin = http::browserUID('BP_MASTER_KEY'.$user_id.
		crypt::hmac('BP_MASTER_KEY',$user_pwd)).bin2hex(pack('a32',$user_id));

	if ($check_user)
	{
		$core->session->start();
		$_SESSION['sess_user_id'] = $user_id;
		$_SESSION['sess_browser_uid'] = http::browserUID('BP_MASTER_KEY');

		if (!empty($_POST['user_remember'])) {
			setcookie('bp_admin',$cookie_admin,strtotime('+30 days'),'','');
		}

		$rs = $core->con->select('SELECT user_token, user_email, user_id, user_fullname, user_pwd
			FROM '.$core->prefix.'user WHERE user_id=\''.$user_id.'\'');
		# if no token exists, create one
		$rs->extend('rsExtUser');
		if ($rs->user_token == '') {
			$token = generateUserToken($rs->user_fullname,$rs->user_email,$rs->user_pwd);
			$curt = $core->con->openCursor($core->prefix.'user');
			$curt->user_token = $token;
			$curt->modified = array(' NOW() ');
			$curt->update("WHERE user_id='".$rs->user_id."'");
		}
		http::redirect($came_from);
	}
	else
	{
		if (isset($_COOKIE['bp_admin'])) {
			unset($_COOKIE['bp_admin']);
			setcookie('bp_admin',false,-600,'','');
		}
		$err = T_('Wrong username or password');
	}
}

if (isset($_GET['user'])) {
	$user_id = $_GET['user'];
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<link rel="icon" type="image/ico" href="../favicon.png" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<meta name="ROBOTS" content="NOARCHIVE,NOINDEX,NOFOLLOW" />
	<meta name="GOOGLEBOT" content="NOSNIPPET" />
	<link rel="stylesheet" type="text/css" href="admin/meta/css/auth.css"/>
	<script language="JavaScript" type="text/javascript" src="admin/meta/js/crir.js"></script>
	<title><?php echo html_entity_decode(stripslashes($blog_settings->get('planet_title')), ENT_QUOTES, 'UTF-8'); ?></title>
</head>

<body id="login" class="auth">

<!--<h1 id="title"<?php echo BP_PLANET_URL; ?>"><?php echo html::escapeHTML($blog_settings->get('planet_title')); ?></h1>-->
<h1 id="title">BilboPlanet</h1>
<h2 id="admin">User interface</h2>
<div id="login-body" >

<form action="auth.php" method="post" id="login-form">
<?php
if ($err) {
	echo '<div class="error"><span class="err">'.$err.'</span></div>';
}
if ($msg) {
	echo '<div class="messages"><span class="mess">'.$msg.'</span></div>';
}

if ($akey)
{
	echo '<p><a href="auth.php">'.T_('Back to login screen').'</a></p>';
}
elseif ($recover)
{
	echo
	'<div class="recover"><span class="recov">'.T_('Request a new password').'</span></div>'.
	'<div class="content_front">'.
	'<div class="pad">'.
	'<div class="field">'.
	'<label>'.T_('Username:').'</label><div class=""><span class="input">'.
	form::field(array('user_id'),20,32,html::escapeHTML($user_id),'text',1).'</span></div></div>'.

	'<div class="field">'.
	'<label>'.T_('Email:').'</label><div class=""><span class="input">'.
	form::field(array('user_email'),20,255,html::escapeHTML($user_email),'text',2).'</span></div></div>'.


	'<div class="field">'.
	'<span class="label">&nbsp;</span>'.
	'<div class=""><button type="submit" tabindex="3"/><span>'.T_('recover').'</span></button>'.
	form::hidden(array('recover'),1).'</div></div>'.


	'<span class="label">&nbsp;</span><a style="" href="auth.php">'.T_('Back to login screen').'</a>';

}
elseif ($change_pwd)
{
	echo
	'<fieldset><legend>'.T_('Change your password').'</legend>'.
	'<p><label>'.T_('New password:').' '.
	form::password(array('new_pwd'),20,255,'','',1).'</label></p>'.

	'<p><label>'.T_('Confirm password:').' '.
	form::password(array('new_pwd_c'),20,255,'','',2).'</label></p>'.
	'</fielset>'.

	'<p><input type="submit" value="'.T_('change').'" />'.
	form::hidden('login_data',$login_data).'</p>';
}
else
{
	if (is_callable(array($core->auth,'authForm')))
	{
		echo $core->auth->authForm($user_id);
	}
	else
	{
		if ($core->auth->allowPassChange()) {
			$passw_change = '<a href="auth.php?recover=1">'.T_('I forgot my password').'</a>';
		}
		echo form::hidden('came_from',$came_from).
			'<div class="content_front">'.
			'<div class="pad">'.
			'<div class="field">'.
			'<label>'.T_('Username or email').'</label><div class=""><span class="input">'.
			form::field(array('user_id'),2,32,html::escapeHTML($user_id),'text',1).'</span></div></div>'.

			'<div class="field">'.
			'<label>'.T_('Password').'</label><div class=""><span class="input">'.
			form::password(array('user_pwd'),2,255,'','text',2,'','id=login_password').''.
			$passw_change.'</span></div></div>'.

			'<div class="checkbox">'.
			'<span class="label">&nbsp;</span>'.
			'<label for="checkbox1" style="float:left; padding:0; padding-left: 24px; margin:1px; width:300px;">'.
			form::checkbox(array('user_remember'),1,'','crirHiddenJS',3,'','id="checkbox1"').''.
			T_('Remember me').'</label></div>'.

			'<div class="field">'.
			'<span class="label">&nbsp;</span>'.
			'<div class=""><button type="submit" tabindex="4"/><span>'.T_('login').'</span></button></div>'.
			'</div>';

		if (!empty($_REQUEST['blog'])) {
			echo form::hidden('blog',html::escapeHTML($_REQUEST['blog']));
		}
	}
}
?>
</form>
</div>
</body>
</html>
