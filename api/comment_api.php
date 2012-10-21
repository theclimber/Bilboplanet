<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : http://chili.kiwais.com/projects/bilboplanet
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

	case 'post_comment':
		$post_id = $_POST['post_id'];
		$fullname = $_POST['user_fullname'];
		if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
			$email = $_POST['user_email'];
		} else {
			$email = null;
		}
		if (!filter_var($_POST['user_site'], FILTER_VALIDATE_URL)) {
			$site = $_POST['user_site'];
		} else {
			$site = '';
		}
		$content = $_POST['content'];

		$sql = "SELECT post_id FROM ".$core->prefix."post
			WHERE post_id =".$post_id."
			AND post_status = 1
			AND post_comment = 1";
		$rs = $core->con->select($sql);
		if (!$rs->isEmpty() && !empty($fullname) && !empty($email)){
			$rs3 = $core->con->select(
				'SELECT MAX(comment_id) '.
				'FROM '.$core->prefix.'comment '
				);
			$next_comment_id = (integer) $rs3->f(0) + 1;

			$cur = $core->con->openCursor($core->prefix.'comment');
			$cur->comment_id = $next_comment_id;
			$cur->post_id = $post_id;
			$cur->user_fullname = $fullname;
			$cur->user_site = $site;
			$cur->user_email = $email;
			$cur->content = $content;
			$cur->created = array( 'NOW()' );
			$cur->insert();
		} else {
			print T_('This action is forbidden');
		}

		break;

##########################################################
# List the latest posts
##########################################################
	case 'list':
		#Get basic data
		$num_page = !empty($_POST['page']) ? $_POST['page'] : 0;
		$nb_items = !empty($_POST['nb_items']) ? $_POST['nb_items'] : 30;
		$num_start = $num_page * $nb_items;
		# Get search value
		$search_value = !empty($_POST['search']) ? $_POST['search'] : null;
		if (isset($search_value)){
			$search_value = htmlentities($search_value, ENT_QUOTES, 'UTF-8');
			$search_value = mysql_real_escape_string($search_value);
		}
		# Get filters on tags and users
		$tags = !empty($_POST['tags']) ? getArrayFromList($_POST['tags']) : array();
		$users = !empty($_POST['users']) ? getArrayFromList($_POST['users']) : array();
		# Get the period
		$period = !empty($_POST['period']) ? trim($_POST['period']) : '';
		# Order by most popular
		$popular = !empty($_POST['popular']) ? true : false;
		$post_status = !empty($_POST['post_status']) ? 2 : 1;

		# On recupere les informtions sur les membres
		$sql = generate_SQL(
			$num_start,
			$nb_items,
			$users,
			$tags,
			$search_value,
			$period,
			$popular,
			null,
			$post_status);
		//print $sql;
		//exit;
		$rs = $core->con->select($sql);

		$tpl = new Hyla_Tpl(dirname(__FILE__).'/../themes/'.$blog_settings->get('planet_theme').'/');
		$tpl->importFile('index', 'index.tpl');

		$tpl->render('menu.filter');

		# Liste des articles
		$tpl = showPosts($rs, $tpl, $search_value, true,$popular);

		$result = array(
			"posts" => $tpl->render('content.posts'),
			"nb_items" => $nb_items,
			"page" => $page,
			"users" => $users,
			"tags" => $tags,
			"search" => $search_value
			);
#		print json_encode($result);
		print $result['posts'];
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
?>
