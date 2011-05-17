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
?><?php

if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){


##########################################################
# List the latest posts
##########################################################
	case 'list':
		#Get basic data
		$num_page = !empty($_POST['num_page']) ? $_POST['num_page'] : 0;
		$nb_items = !empty($_POST['nb_items']) ? $_POST['nb_items'] : 30;
		$num_start = $num_page * $nb_items;
		# Get search value
		$search_value = !empty($_POST['search']) ? $_POST['search'] : '';
		$search_value = htmlentities($search_value, ENT_QUOTES, 'UTF-8');
		$search_value = mysql_real_escape_string($search_value);
		# Get filters on tags and users
		$tags = !empty($_POST['tags']) ? getArrayFromList($_POST['tags']) : array();
		$users = !empty($_POST['users']) ? getArrayFromList($_POST['users']) : array();
		# Get the period
		$period = !empty($_POST['period']) ? trim($_POST['period']) : '';
		# Order by most popular
		$popular = !empty($_POST['popular']) ? true : false;

		# On recupere les informtions sur les membres
		$sql = generate_SQL(
			$num_start,
			$nb_items,
			$users,
			$tags,
			$search_value,
			$period,
			$popular);
		$rs = $core->con->select($sql);

		$tpl = new Hyla_Tpl(dirname(__FILE__).'/../themes/'.$blog_settings->get('planet_theme').'/');
		$tpl->importFile('posts', 'posts.tpl');

		# Liste des articles
		if (isset($popular) && !empty($popular)) {
			$tpl = showPosts($rs, $tpl, $search_value, true);
		} else {
			$tpl = showPosts($rs, $tpl, $search_value, false);
		}

		$result = array(
			"html" => $tpl->render(),
			"nb_items" => $nb_items,
			"page" => $page,
			"users" => $users,
			"tags" => $tags,
			"search" => $search_value
			);
		print json_encode($result);

		break;
##########################################################
# DEFAULT RETURN
##########################################################
	default:
		print '<div class="flash_error">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}

function getArrayFromList($list) {
	$patterns = array( '/, /', '/ ,/');
	$replacement = array(',', ',');
	$list = urldecode($list);
	$list = preg_replace($patterns, $replacement, $list);
	$array = preg_split('/,/',$list, -1, PREG_SPLIT_NO_EMPTY);
	return $array;
}

function generate_SQL(
		$num_start = 0,
		$nb_items = null,
		$users = array(),
		$tags = array(),
		$search = null,
		$period = null,
		$popular = false)
	{
	global $blog_settings, $core;
	if (!isset($nb_items)) {
		$nb_items = $blog_settings->get('planet_nb_post');
	}

	$debut_sql = "SELECT DISTINCT
			".$core->prefix."user.user_id		as user_id,
			user_fullname	as user_fullname,
			user_email		as user_email,
			post_pubdate	as pubdate,
			post_title		as title,
			post_permalink	as permalink,
			post_content	as content,
			post_nbview		as nbview,
			last_viewed		as last_viewed,
			feed_id			as feed_id,
			SUBSTRING(post_content,1,400) as short_content,
			".$core->prefix."post.post_id		as post_id,
			post_score		as score
		FROM ".$core->prefix."post, ".$core->prefix."user, ".$core->prefix."post_tag
		WHERE ".$core->prefix."user.user_id = ".$core->prefix."post.user_id
		AND ".$core->prefix."post.post_id = ".$core->prefix."post_tag.post_id
		AND post_status = '1'
		AND user_status = '1'
		AND post_score > '".$blog_settings->get('planet_votes_limit')."'";

	if (!empty($users)) {
		$sql_users = "(";
		foreach ($users as $key=>$user) {
			$sql_users .= $core->prefix."post.user_id = '".$user."'";
			$or = ($key == count($users)-1) ? "" : " OR ";
			$sql_users .= $or;
		}
		$sql_users .= ")";
		$debut_sql .= ' AND '.$sql_users.' ';
	}

	if (!empty($tags)) {
		$sql_tags = "(";
		foreach ($tags as $key=>$tag) {
			$sql_tags .= $core->prefix."post_tag.tag_id = '".$tag."'";
			$or = ($key == count($tags)-1) ? "" : " OR ";
			$sql_tags .= $or;
		}
		$sql_tags .= ")";
		$debut_sql .= ' AND '.$sql_tags.' ';
	}

	if (isset($search) && !empty($search)){
		# Complete the SQL query
		$debut_sql = $debut_sql." AND (".$core->prefix."post.post_title LIKE '%$search%'
			OR ".$core->prefix."post.post_permalink LIKE '%$search%'
			OR ".$core->prefix."post.post_content LIKE '%$search%'
			OR ".$core->prefix."user.user_fullname LIKE '%$search%')";
	}

	if (isset($period) && !empty($period)) {
		# Complete the SQL query
		$now = mktime(0, 0, 0, date("m",time()), date("d",time()), date("Y",time()));
		$day = date('Y-m-d', $now).' 00:00:00';
		$week = date('Y-m-d', $now - 3600*24*7).' 00:00:00';
		$month = date('Y-m-d', $now - 3600*24*31).' 00:00:00';
		$filter_class = array(
			"day" => "",
			"week" => "",
			"month" => "");
		switch($period) {
		case "day"		:
			$debut_sql = $debut_sql." AND post_pubdate > '".$day."'";
			break;
		case "week"		:
			$debut_sql = $debut_sql." AND post_pubdate > '".$week."'";
			break;
		case "month"	:
			$debut_sql = $debut_sql." AND post_pubdate > '".$month."'";
			break;
		default			:
			$debut_sql = $debut_sql." AND post_pubdate > '".$week."'";
			break;
		}
	}

	if ($popular){
		# Complete the SQL query
		$debut_sql = $debut_sql." AND post_score > 0";
		$fin_sql = " ORDER BY post_score DESC
			LIMIT $num_start,".$nb_items;
	}
	else {
		$fin_sql = " ORDER BY post_pubdate DESC
			LIMIT $num_start,".$nb_items;
	}
	$sql = $debut_sql." ".$fin_sql;

	return $sql;
}

?>
