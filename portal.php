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

if (!isset($params)) {
	$params = array(
		'title'=> $blog_settings->get('planet_title'),
		'popular'=> 'false',
		'filter'=> 'week',
		'page' => 0
		);
}

$sql_tribes = "SELECT
		tribe_id,
		user_id,
		tribe_name,
		tribe_search,
		tribe_tags,
		tribe_users
	FROM ".$core->prefix."tribe
	WHERE user_id = 'root'
	AND visibility = 1
	ORDER BY ordering DESC";
$rs = $core->con->select($sql_tribes);

while ($rs->fetch()) {
	$tribe_name = $rs->tribe_name;
	$tribe_search = comma_to_array($rs->tribe_search);
	$tribe_tags = comma_to_array($rs->tribe_tags);
	$tribe_users = comma_to_array($rs->tribe_users);

	// Generating the SQL request
	$sql_posts = generate_SQL(
		$num_start,
		$nb_items,
		$tribe_users,
		$tribe_tags,
		$tribe_search,
		$period,
		$popular,
		$post_id,
		$post_status);

	$rs_posts = $core->con->select($sql_posts);
	while ($rs_posts->fetch()) {
		print_r("\t>>".$rs_posts->permalink." ".$rs_posts->title."\n");
	}
	print_r($sql_posts."\n");

	// For each tribe I'll have to generate a list of 10 posts with their title and permalink
}
exit;

# Terminaison de la commande SQL
$sql = generate_SQL(
	$num_start,
	10,
	$users,
	$tags,
	$search_value,
	$period,
	$popular,
	$post_id,
	$post_status);
#print $sql;
#exit;


$core->tpl->render('search.box');

# Executing sql querry
$rs = $core->con->select($sql);



######################
# RENDER POST LIST
######################

if (!isset($_GET['post_id']) | empty($_GET['post_id'])){
	$core->tpl = showPostsSummary($rs, $core->tpl);
	$core->tpl->render('summary.block');
}

# Liste des articles
$core->tpl = showPosts($rs, $core->tpl, $search_value, $popular);
$core->tpl->render("content.posts");

# Show result
$analytics_code = getAnalyticsCode();
$core->tpl->setVar('analytics_html', $analytics_code);
echo $core->tpl->render();
?>
