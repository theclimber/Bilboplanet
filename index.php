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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/inc/prepend.php');
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

# Valeurs par defaut
$num_start = 0;
$params = array();

/* On recupere les infomations des articles */
$debut_sql = "SELECT 
		".$core->prefix."user.user_id		as user_id,
		user_fullname	as user_fullname,
		user_email		as user_email,
		post_pubdate	as pubdate,
		post_title		as title,
		post_permalink	as permalink,
		post_content	as content,
		SUBSTRING(post_content,1,400) as short_content,
		".$core->prefix."post.post_id		as post_id,
		post_score		as score
	FROM ".$core->prefix."post, ".$core->prefix."user
	WHERE ".$core->prefix."user.user_id = ".$core->prefix."post.user_id
	AND post_status = '1'
	AND user_status = '1'
	AND post_score > '".$blog_settings->get('planet_votes_limit')."'";

# Verification du contenu du get
if (isset($_GET)) {
	if (isset($_GET['page']) && is_numeric(trim($_GET['page']))) {
		# On recuepre la valeur du get
		$params["page"] = trim($_GET['page']);
		if ($params["page"] < 1) {
			$params["page"] = 0;
		}
		$num_start = $params["page"] * $blog_settings->get('planet_nb_post');
	}
	# Si le lecteur a fait une recherche
	if (isset($_GET['search']) && !empty($_GET['search'])){
		$params["search"] = $_GET['search'];

		# Complete the SQL query
		$search_value = addslashes(trim($_GET['search']));
		$debut_sql = $debut_sql." AND (".$core->prefix."post.post_title LIKE '%$search_value%'
			OR ".$core->prefix."post.post_permalink LIKE '%$search_value%'
			OR ".$core->prefix."post.post_content LIKE '%$search_value%'
			OR ".$core->prefix."user.user_fullname LIKE '%$search_value%')";
	}
	# On recupere le numero du membre
	if (isset($_GET['user_id']) && !empty($_GET['user_id'])){
		$params["user_id"] = $_GET['user_id'];

		# Complete the SQL query
		$debut_sql = $debut_sql." AND ".$core->prefix."post.user_id = '".$params["user_id"]."'";
	}
	if (isset($_GET['popular']) && !empty($_GET['popular'])){
		$params['popular'] = $_GET['popular'];
	}

	# If there is a filter call
	if (isset($_GET['filter']) && !empty($_GET['filter']) && !isset($_GET['popular'])) {
		$params["filter"] = trim($_GET['filter']);

		# Complete the SQL query
		$now = mktime(0, 0, 0, date("m",time()), date("d",time()), date("Y",time()));
		$day = date('Y-m-d', $now).' 00:00:00';
		$week = date('Y-m-d', $now - 3600*24*7).' 00:00:00';
		$month = date('Y-m-d', $now - 3600*24*31).' 00:00:00';
		$filter_class = array(
			"day" => "",
			"week" => "",
			"month" => "");
		switch($params["filter"]) {
		case "day"		:
			$filter_class["day"] = "selected";
			$debut_sql = $debut_sql." AND post_pubdate > '".$day."'";
			break;
		case "week"		:
			$filter_class["week"] = "selected";
			$debut_sql = $debut_sql." AND post_pubdate > '".$week."'";
			break;
		case "month"	:
			$filter_class["month"] = "selected";
			$debut_sql = $debut_sql." AND post_pubdate > '".$month."'";
			break;
		default			:
			$debut_sql = $debut_sql." AND post_pubdate > '".$week."'";
			$params["filter"] = "week";
			break;
		}
	}
}

if (array_key_exists('popular', $params)){
	# Complete the SQL query
	$fin_sql = " ORDER BY post_score DESC LIMIT $num_start,".$blog_settings->get('planet_nb_post');
}
else {
	$fin_sql = " ORDER BY post_pubdate DESC
		LIMIT $num_start,".$blog_settings->get('planet_nb_post');
}

# Terminaison de la commande SQL
$sql = $debut_sql." ".$fin_sql;

$page_url = '';
foreach ($params as $key => $val) {
	if ($key != "page") {
		$page_url .= $key."=".$val."&";
	}
}
$filter_url = '';
foreach ($params as $key => $val) {
	if ($key != "page" && $key != "filter") {
		$filter_url .= $key."=".$val."&";
	}
}
$page_vars = array(
	"next" => $params["page"]+1,
	"prev" => $params["page"]-1,
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
if($params["page"] == 0) {
	# if we are on the first page
	$core->tpl->render('pagination.up.next');
	$core->tpl->render('pagination.low.next');
} else {
	if(!$rs->count()) {
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

######################
# RENDER POST LIST
######################
$core->tpl = showPostsSummary($rs, $core->tpl);

# Liste des articles
if (isset($_GET) && isset($_GET['popular']) && !empty($_GET['popular'])) {
	$core->tpl = showPosts($rs, $core->tpl, $search_value, true);
}
else {
	$core->tpl = showPosts($rs, $core->tpl, $search_value, false);
}
$core->tpl->render("content.posts");

# Show result
echo $core->tpl->render();

##################
# UPDATE ALGO
##################
$cron_file = dirname(__FILE__).'/inc/cron_running.txt';
$dodo_interval = 250;
if (!file_exists(dirname(__FILE__).'/STOP') && $blog_settings->get('planet_index_update')) {
	$fp = fopen($cron_file, "rb");
	$contents = fread($fp, filesize($cron_file));
	$next = (int) $contents + $dodo_interval;
	if ($next <= time()) {
		require_once(dirname(__FILE__).'/inc/cron_fct.php');
		$fp = @fopen($cron_file,'wb');
		if ($fp === false) {
			throw new Exception(sprintf(__('Cannot write %s file.'),$fichier));
		}
		fwrite($fp,time());
		fclose($fp);
		update($core);
	}
}
?>
