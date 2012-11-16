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
$tribe_id = null;

if (!isset($params)) {
	$params = array(
		'title'=> $blog_settings->get('planet_title'),
		'popular'=> 'false',
		'filter'=> 'week',
		'page' => 0
		);
}

# Verification du contenu du get
if (isset($_GET)) {
	# if user want to read a unique post
	if (isset($_GET['post_id']) && !empty($_GET['post_id'])){
		$params["post_id"] = intval($_GET['post_id']);
		$res = $core->con->select(
			"SELECT
				post_title, post_permalink, post_nbview
			FROM ".$core->prefix."post WHERE post_id = ".$params["post_id"]."
				AND post_status = 1");
		$params['title'] .= " - ".$res->f('post_title');

		# Update the number of viewed times
		$cur = $core->con->openCursor($core->prefix.'post');
		$cur->post_nbview = $res->post_nbview + 1;
		$cur->last_viewed = array('NOW()');
		$cur->update("WHERE post_id = '".$params['post_id']."'");

		$post_id = $params['post_id'];

		if (isset($_GET['go']) &&
			$_GET['go'] == "external" &&
			!$res->isEmpty() &&
			$blog_settings->get('internal_links')){
			$root_url = BP_PLANET_URL;
			$analytics = $blog_settings->get('planet_analytics');

			if(!empty($analytics)) {
				# If google analytics is activated, launch request
				analyze (
					$analytics,
					$root_url.'/post/'.$params['post_id'],
					'post:'.$params['post_id'],
					$res->post_permalink,
					false
				);
			}
			$post_url = stripslashes($res->post_permalink);
			http::head('301');
			http::redirect($post_url);
		}
		if (isset($_GET['uncensored']) && !empty($_GET['uncensored'])){
			if ($blog_settings->get('allow_uncensored_feed')) {
				$params['uncensored'] = true;
				$post_status = 2;
			}
		}
	}
	else {
		if (isset($_GET['page']) && is_numeric(trim($_GET['page']))) {
			$params["page"] = trim($_GET['page']);
			if ($params["page"] < 1) {
				$params["page"] = 0;
			}
			$num_start = $params["page"] * $nb_items;
		}
		if (isset($_GET['tags'])) {
			$params["tags"] = $_GET['tags'];
			$tags = !empty($_GET['tags']) ? getArrayFromList($_GET['tags']) : array();
		}
		# Si le lecteur a fait une recherche
		if (isset($_GET['search']) && !empty($_GET['search'])){
			$search_value = strip_script($_GET['search']);
			$params["search"] = $search_value;
		}
		# On recupere le numero du membre
		if (isset($_GET['user_id']) && !empty($_GET['user_id'])){
			$params["user_id"] = urldecode($_GET['user_id']);
			$params['users'] = $_GET['user_id'];
			$users = !empty($_GET['user_id']) ? getArrayFromList($_GET['user_id']) : array();
		}
		if (isset($_GET['uncensored']) && !empty($_GET['uncensored'])){
			if ($blog_settings->get('allow_uncensored_feed')) {
				$params['uncensored'] = true;
				$post_status = 2;
			}
		}
		if (isset($_GET['popular']) && !empty($_GET['popular']) && $_GET['popular'] = 'true'){
			$params['popular'] = $_GET['popular'];
			$popular = true;
		}
		# If there is a filter call
		if (isset($_GET['filter']) && !empty($_GET['filter'])) {
			$params["filter"] = trim($_GET['filter']);
			$period = trim($_GET['filter']);
			$filter_class = array(
				"day" => "",
				"week" => "",
				"month" => "");
			$filter_class[$period] = "selected";
		}
		# On recupere le numero de la tribe
		if (isset($_GET['tribe_id']) && !empty($_GET['tribe_id'])){
			if ($_GET['tribe_id'] == 'popular') {
				$popular = true;
			} else {
				$params["tribe_id"] = urldecode($_GET['tribe_id']);
				$params["title"] = $params["title"]." - ".sprintf(T_("%s tribe"), $params['tribe_id']);
				$tribe_id = $params["tribe_id"];
			}
		}
	}
}

if ($tribe_id != null) {
	$sql = generate_tribe_SQL(
		$tribe_id,
		$num_start,
		$nb_items);
} else {
	# Terminaison de la commande SQL
	$sql = generate_SQL(
		$num_start,
		$nb_items,
		$users,
		$tags,
		$search_value,
		$period,
		$popular,
		$post_id,
		$post_status);
}
#print $sql;
#exit;

$page_url = '';
foreach ($params as $key => $val) {
	if ($key != "page" && $key != "title") {
		$page_url .= $key."=".$val."&";
	}
}
$filter_url = '';
foreach ($params as $key => $val) {
	if ($key != "page" && $key != "filter" && $key != "title") {
		$filter_url .= $key."=".$val."&";
	}
}
$page_nbr = isset($params["page"]) ? $params['page'] : 0;
$page_vars = array(
	"next" => $page_nbr+1,
	"prev" => $page_nbr-1,
	"params" => $page_url
);
$core->tpl->setVar('search_value', $search_value);
$core->tpl->setVar('params', $params);
$core->tpl->setVar('page', $page_vars);
$core->tpl->setVar('filter_url', $filter_url);

if (isset($_GET)) {
	if (isset($_GET['filter']) && !empty($_GET['filter'])){
		$core->tpl->setVar("filter", $filter_class);
		$core->tpl->render('search.filter');
	}
	if (isset($_GET['user_id']) && !empty($_GET['user_id'])){
		$core->tpl->render('search.user_id');
	}
	if (isset($_GET['popular']) && !empty($_GET['popular'])){
		$core->tpl->render('search.popular');
	}
	if ((isset($_GET['tags']) && !empty($_GET['tags'])) ||
		(isset($_GET['user_id']) && !empty($_GET['user_id']))){
		$core->tpl->render('feed.tags');
	}
}

# Executing sql querry
if ($sql != "") {
	$rs = $core->con->select($sql);
} else {
	print "error";
	exit;
}

#######################
# RENDER FILTER MENU
#######################
$core->tpl->render('menu.filter');


######################
# RENDER POST LIST
######################


# Liste des articles
if (isset($_GET['post_id']) && !empty($_GET['post_id'])){
	$core->tpl = showPosts($rs, $core->tpl, $search_value, false, $popular);
	$core->tpl->render("content.single");
} else {
	$core->tpl = showPosts($rs, $core->tpl, $search_value, true, $popular);
	$core->tpl->render("content.posts");
}

# Show result
$analytics_code = getAnalyticsCode();
$core->tpl->setVar('analytics_html', $analytics_code);
$core->renderTemplate();
?>
