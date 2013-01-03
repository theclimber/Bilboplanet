<?php
# Inclusion des fonctions
require_once(dirname(__FILE__).'/inc/prepend.php');
$scripts = array();
$scripts[] = "javascript/main.js";
$scripts[] = "javascript/jquery.boxy.js";
$current_page="users";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

# Valeurs par defaut

$params = array(
	'title'=> $blog_settings->get('planet_title')." - ".T_('Users'),
	);
$core->tpl->setVar('params', $params);


$sql_users = "SELECT
		".$core->prefix."user.user_id,
		".$core->prefix."user.user_fullname,
		".$core->prefix."user.user_email,
		".$core->prefix."site.site_url,
		MAX(".$core->prefix."post.post_pubdate) as pubdate,
		COUNT(".$core->prefix."post.post_id) as nb_post
	FROM ".$core->prefix."user, ".$core->prefix."post, ".$core->prefix."site
	WHERE ".$core->prefix."user.user_status = 1
	AND ".$core->prefix."user.user_id = ".$core->prefix."post.user_id
	AND ".$core->prefix."user.user_id = ".$core->prefix."site.user_id
	GROUP BY ".$core->prefix."user.user_id
	ORDER BY pubdate DESC";

//print $sql_users;
//exit;
$rs = $core->con->select($sql_users);

while ($rs->fetch()) {
	$puser_settings = new bpSettings($core,$rs->user_id);
	if ($puser_settings != null && $puser_settings->get('social.shaarli')) {
		$shaarli = $puser_settings->get('social.shaarli.instance');
		$core->tpl->setVar('user_shaarli', $shaarli);
		$core->tpl->render('user.shaarli');
	}
	$user = array(
		"id" => $rs->user_id,
		"fullname" => $rs->user_fullname,
		"email" => $rs->user_email,
		"website" => $rs->site_url,
		"last" => mysqldatetime_to_date("d/m/Y",$rs->pubdate),
		"nb_post" => $rs->nb_post
		);
	$core->tpl->setVar('user', $user);

	//$avatar_email = strtolower($rs->user_email);
	//$libravatar = "http://cdn.libravatar.org/avatar/".md5($avatar_email)."?default=identicon";
	$libravatar = getUserIcon($rs->user_email);
	$core->tpl->setVar('avatar_url', $libravatar);

	$core->tpl->render("user.block");
}

$core->tpl->render("content.users");

# Show result
$analytics_code = getAnalyticsCode();
$core->tpl->setVar('analytics_html', $analytics_code);
$core->renderTemplate();

?>
