<?php
# Inclusion des fonctions
require_once(dirname(__FILE__).'/inc/prepend.php');
$scripts = array();
$scripts[] = "javascript/main.js";
$current_page="tags";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

# Valeurs par defaut

$params = array(
	'title'=> $blog_settings->get('planet_title')." - ".T_('Users'),
	);
$core->tpl->setVar('params', $params);

$user_id = '';
if ($core->auth->sessionExists()) {
	$user_id = $core->auth->userID();
}

$sql_tribes = "SELECT
		user_id,
		tribe_name,
		tribe_tags,
		tribe_users,
		tribe_search,
		tribe_icon,
		tribe_id
	FROM ".$core->prefix."tribe
	WHERE (user_id = 'root' OR user_id = '".$user_id."')
		AND visibility = 1
	ORDER BY ordering
		";

$rs = $core->con->select($sql_tribes);
while ($rs->fetch()) {
	$sql_post = generate_tribe_SQL(
		$rs->tribe_id,
		0,
		0);
	$rs_post = $core->con->select($sql_post);
    $tribe_icon = getTribeIcon($rs->tribe_id,$rs->tribe_name,$rs->tribe_icon);
	$tribe = array(
		"id" => $rs->tribe_id,
		"user_id" => $rs->user_id,
		"name" => $rs->tribe_name,
		"tags" => implode(', ',getArrayFromList($rs->tribe_tags)),
		"users" => implode(', ',getArrayFromList($rs->tribe_users)),
		"search" => $rs->tribe_search,
		"icon" => $tribe_icon,
		"last" => mysqldatetime_to_date("d/m/Y",$rs_post->last),
		"nb_post" => $rs_post->count
		);
	$core->tpl->setVar('tribe', $tribe);

	$core->tpl->render("tribe.block");
}

$sql_tags = "SELECT
		".$core->prefix."post_tag.tag_id,
		COUNT(".$core->prefix."post_tag.tag_id) as weigth
	FROM ".$core->prefix."post_tag
	GROUP BY ".$core->prefix."post_tag.tag_id
	ORDER BY weigth DESC
	LIMIT 100";
$rs = $core->con->select($sql_tags);
$max = $rs->f('weight');
while ($rs->fetch()) {
	if ($rs->weigth > $max)
		$max = $rs->weigth;
	$weigth = intval($rs->weigth*10/$max);
	$tag = array(
		"id" => $rs->tag_id,
		"weigth" => $weigth
		);
	$core->tpl->setVar('tag', $tag);

	$core->tpl->render("tag.block");
}
$core->tpl->render("content.tags");

# Show result
$analytics_code = getAnalyticsCode();
$core->tpl->setVar('analytics_html', $analytics_code);
$core->renderTemplate();

?>
