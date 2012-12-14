<?php

# Valeurs par defaut
$num_start = 0;
$nb_items = $blog_settings->get('planet_nb_post');
$popular = false;
$period = null;
$users = array();
$tags = array();
$page = 0;
$search_value = null;
$post_id = null;
$post_status = 1;

$params = array(
	'title'=> $blog_settings->get('planet_title')." - ".T_('Portal'),
	'popular'=> 'false',
	'filter'=> 'week',
	'page' => 0
	);
$core->tpl->setVar('params', $params);


if ($core->auth->sessionExists() ) {
	$user_condition = "OR user_id = '".$core->auth->userID()."'";
}
$sql_tribes = "SELECT
		tribe_id,
		user_id,
		tribe_icon,
		tribe_name
	FROM ".$core->prefix."tribe
	WHERE visibility = 1
	AND (user_id = 'root' ".$user_condition.")
	ORDER BY ordering DESC";
//print $sql_tribes;
//exit;
$rs = $core->con->select($sql_tribes);

$align= 'right';
while ($rs->fetch()) {
	$align = $align=='right'? 'left' : 'right';
    $tribe_icon = getTribeIcon($rs->tribe_id,$rs->tribe_name,$rs->tribe_icon);
	$tribe = array(
		"title" => $rs->tribe_name,
		"id" => $rs->tribe_id,
		"icon" => $tribe_icon,
        "align" => $align,
        "page" => 0
		);
	$core->tpl->setVar('tribe', $tribe);

	// Generating the SQL request
	$sql_posts = generate_tribe_SQL($rs->tribe_id);
	$rs_posts = $core->con->select($sql_posts);
    if ($rs_posts->count() > 0) {
        $core->tpl = showTribe($core->tpl,$rs_posts);
        $core->tpl->render('portal.block');
    }

	// For each tribe I'll have to generate a list of 10 posts with their title and permalink
}

$align = $align=='right'? 'left' : 'right';
$tribe = array(
	"title" => T_('Popular posts'),
	"id" => 'popular',
	"icon" => '',
	"align" => $align
	);
$core->tpl->setVar('tribe', $tribe);

$popular_sql = generate_SQL(
		0, // num start
		10, // nb items
		array(), // users
		array(), // tags
		null, // search
		"week", // period
		true); // popular
$rs_posts = $core->con->select($popular_sql);
if ($rs_posts->count() > 0) {
    $core->tpl = showTribe($core->tpl,$rs_posts, true);
    $core->tpl->render('portal.block');
}

$core->tpl->render("content.portal");

# Show result
$analytics_code = getAnalyticsCode();
$core->tpl->setVar('analytics_html', $analytics_code);
$core->renderTemplate();

?>
