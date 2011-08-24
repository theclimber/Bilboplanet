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

	case 'fetch':
		##################
		# UPDATE ALGO
		##################
		$cron_file = dirname(__FILE__).'/../inc/cron_running.txt';
		$dodo_interval = 250;
		$done = 'NOK';
		if (!file_exists(dirname(__FILE__).'/../STOP') && $blog_settings->get('planet_index_update')) {
			$fp = fopen($cron_file, "rb");
			$contents = fread($fp, filesize($cron_file));
			$next = (int) $contents + $dodo_interval;
			if ($next <= time()) {
				require_once(dirname(__FILE__).'/../inc/cron_fct.php');
				$fp = @fopen($cron_file,'wb');
				if ($fp === false) {
					throw new Exception(sprintf(__('Cannot write %s file.'),$fichier));
				}
				fwrite($fp,time());
				fclose($fp);
				update($core);
				$done = 'OK';
			}
		}
		print $done;
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
		print $search_value;
		exit;
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

		# On recupere les informtions sur les membres
		$sql = generate_SQL(
			$num_start,
			$nb_items,
			$users,
			$tags,
			$search_value,
			$period,
			$popular);
		//print $sql;
		//exit;
		$rs = $core->con->select($sql);

		$tpl = new Hyla_Tpl(dirname(__FILE__).'/../themes/'.$blog_settings->get('planet_theme').'/');
		$tpl->importFile('index', 'index.tpl');
		$tpl->setVar('planet', array(
			'url' => $blog_settings->get('planet_url'),
			'theme' => $blog_settings->get('planet_theme')
		));

		if($num_page == 0 & $rs->count()>= $nb_items) {
			# if we are on the first page
			$tpl->render('pagination.up.next');
			$tpl->render('pagination.low.next');
		} elseif($num_page == 0 & $rs->count()< $nb_items) {
			# we don't show any button
		} else {
			if($rs->count() == 0 | $rs->count() < $nb_items) {
				# if we are on the last page
				$tpl->render('pagination.up.prev');
				$tpl->render('pagination.low.prev');
			} else {
				$tpl->render('pagination.up.prev');
				$tpl->render('pagination.up.next');
				$tpl->render('pagination.low.prev');
				$tpl->render('pagination.low.next');
			}
		}
		$tpl->render('menu.filter');

		$tpl = showPostsSummary($rs, $tpl);
		$tpl->render('summary.block');

		# Liste des articles
		$tpl = showPosts($rs, $tpl, $search_value, $popular);

		$result = array(
			"posts" => $tpl->render('content.posts'),
			"nb_items" => $nb_items,
			"page" => $num_page,
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
