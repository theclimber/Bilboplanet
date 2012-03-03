<?php
# Inclusion des fonctions
require_once(dirname(__FILE__).'/inc/prepend.php');
$scripts = array();
$scripts[] = "javascript/main.js";
$scripts[] = "javascript/jquery.boxy.js";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

# Valeurs par defaut

$params = array(
	'title'=> $blog_settings->get('planet_title')." - ".T_('Users'),
	);
$core->tpl->setVar('params', $params);


$sql_users = "SELECT
		user_id,
		user_fullname,
		user_email
	FROM ".$core->prefix."user
	WHERE user_status = 1
	ORDER BY user_fullname DESC";
//print $sql_tribes;
//exit;
$rs = $core->con->select($sql_users);

while ($rs->fetch()) {
	$user = array(
		"id" => $rs->user_id,
		"fullname" => $rs->user_fullname,
		"email" => $rs->user_email,
		"website" => ''
		);
	$core->tpl->setVar('user', $user);

	$avatar_email = strtolower($rs->user_email);
	$core->tpl->setVar('avatar_url', "http://cdn.libravatar.org/avatar/".md5($avatar_email)."?d=".urlencode($blog_settings->get('planet_url')."/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png"));

	$core->tpl->render("user.block");
}

$core->tpl->render("content.users");

# Show result
$analytics_code = getAnalyticsCode();
$core->tpl->setVar('analytics_html', $analytics_code);
echo $core->tpl->render();

?>
