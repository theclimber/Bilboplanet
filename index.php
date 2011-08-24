<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/inc/prepend.php');
$scripts = array();
$scripts[] = "javascript/main.js";
$scripts[] = "javascript/jquery.boxy.js";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

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
		if (!$res->isEmpty) {
			$params['title'] .= " - ".$res->f('post_title');
		}

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
			$root_url = $blog_settings->get('planet_url');
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
			$params["search"] = $_GET['search'];
			$search_value = $_GET['search'];
		}
		# On recupere le numero du membre
		if (isset($_GET['user_id']) && !empty($_GET['user_id'])){
			$params["user_id"] = urldecode($_GET['user_id']);
			$params['users'] = $_GET['user_id'];
			$users = !empty($_GET['user_id']) ? getArrayFromList($_GET['user_id']) : array();
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
	}
}

# Terminaison de la commande SQL
$sql = generate_SQL(
	$num_start,
	10,
	$users,
	$tags,
	$search_value,
	$period,
	$popular,
	$post_id);
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

$core->tpl->render('search.box');
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
	if (isset($_GET['search']) && !empty($_GET['search'])){
		$core->tpl->render('search.line');
	}
	if ((isset($_GET['tags']) && !empty($_GET['tags'])) ||
		(isset($_GET['user_id']) && !empty($_GET['user_id']))){
		$core->tpl->render('feed.tags');
		$core->tpl->render('feed.main.button.filter');
	}
}

# Executing sql querry
$rs = $core->con->select($sql);

#######################
# RENDER FILTER MENU
#######################
$core->tpl->render('menu.filter');

#######################
# RENDER PAGINATION
#######################
if (!isset($_GET['post_id']) | empty($_GET['post_id'])){
	if((!isset($params['page']) || $params["page"] == 0) & $rs->count()>=10) {
		# if we are on the first page
		$core->tpl->render('pagination.up.next');
		$core->tpl->render('pagination.low.next');
	} elseif($params["page"] == 0 & $rs->count()<10) {
		# we don't show any button
	} else {
		if($rs->count() == 0 | $rs->count() < 10) {
			# if we are on the last page
			$core->tpl->render('pagination.up.prev');
			$core->tpl->render('pagination.low.prev');
		} else {
			$core->tpl->render('pagination.up.prev');
			$core->tpl->render('pagination.up.next');
			$core->tpl->render('pagination.low.prev');
			$core->tpl->render('pagination.low.next');
		}
	}
}

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
