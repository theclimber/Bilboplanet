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

# Verification du contenu du get
if (isset($_GET)) {
	# if user want to read a unique post
	if (isset($_GET['id']) && !empty($_GET['id'])){
		$params["id"] = intval($_GET['id']);
		$res = $core->con->select(
			"SELECT
				post_title, post_permalink, post_nbview
			FROM ".$core->prefix."post WHERE post_id = ".$params["id"]."
				AND post_status = 1");
		if (!$res->isEmpty) {
			$params['title'] .= " - ".$res->f('post_title');

			# Update the number of viewed times
			$cur = $core->con->openCursor($core->prefix.'post');
			$cur->post_nbview = $res->post_nbview + 1;
			$cur->last_viewed = array('NOW()');
			$cur->update("WHERE post_id = '".$params['id']."'");
#######################
# RENDER FILTER MENU
#######################
$core->tpl->render('menu.filter');

			$id = $params['id'];

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
						$root_url.'/post/'.$params['id'],
						'post:'.$params['id'],
						$res->post_permalink);
				}
				$post_url = stripslashes($res->post_permalink);
				http::redirect($post_url);
			}
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
	$id);

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

# Executing sql querry
$rs = $core->con->select($sql);

######################
# RENDER POST
######################

# Liste des articles
$core->tpl = showPosts($rs, $core->tpl, $search_value, $popular);
$core->tpl->render("content.posts");

# Show result
echo $core->tpl->render();
?>
