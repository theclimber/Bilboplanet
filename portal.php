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

$align= 'right';
while ($rs->fetch()) {
	$tribe_name = $rs->tribe_name;
	$tribe_search = comma_to_array($rs->tribe_search);
	$tribe_tags = comma_to_array($rs->tribe_tags);
	$tribe_users = comma_to_array($rs->tribe_users);
	$align = $align=='right'? 'left' : 'right';

	$tribe = array(
		"title" => $rs->tribe_name,
		"align" => $align
		);
	$core->tpl->setVar('tribe', $tribe);

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
		######################
		# RENDER TRIBE LIST
		######################
		$post_permalink = $rs_posts->permalink;
		if ($blog_settings->get('internal_links')) {
			$post_permalink = $blog_settings->get('planet_url').
				"/index.php?post_id=".$rs_posts->post_id.
				"&go=external";
		}

		$entry = array(
			"id" => $rs_posts->post_id,
			"date" => mysqldatetime_to_date("d/m/Y",$rs_posts->pubdate),
			"day" => mysqldatetime_to_date("d",$rs_posts->pubdate),
			"month" => mysqldatetime_to_date("m",$rs_posts->pubdate),
			"year" => mysqldatetime_to_date("Y",$rs_posts->pubdate),
			"hour" => mysqldatetime_to_date("H:i",$rs_posts->pubdate),
			"permalink" => urldecode($post_permalink),
			"title" => html_entity_decode($rs_posts->title, ENT_QUOTES, 'UTF-8'),
			"content" => html_entity_decode($rs_posts->content, ENT_QUOTES, 'UTF-8'),
			"author_id" => $rs_posts->user_id,
			"author_fullname" => $rs_posts->user_fullname,
			"author_email" => $rs_posts->user_email,
			"nbview" => $rs_posts->nbview,
			"last_viewed" => mysqldatetime_to_date('d/m/Y H:i',$rs_posts->last_viewed),
			"user_votes" => getNbVotes(null,$rs_posts->user_id),
			"user_posts" => getNbPosts(null,$rs_posts->user_id)
			);

		$core->tpl->setVar('entry', $entry);

		$core->tpl->render('portal.entry');
	}
	$core->tpl->render('portal.block');

	// For each tribe I'll have to generate a list of 10 posts with their title and permalink
}

$core->tpl->render("content.portal");

# Show result
$analytics_code = getAnalyticsCode();
$core->tpl->setVar('analytics_html', $analytics_code);
echo $core->tpl->render();
?>
