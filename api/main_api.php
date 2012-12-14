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
		if (isset($search_value)){
			$search_value = htmlentities($search_value, ENT_QUOTES, 'UTF-8');
			$search_value = mysql_real_escape_string($search_value);
		}
		# Get filters on tags and users
		$tags = !empty($_POST['tags']) ? getArrayFromList($_POST['tags']) : array();
		$users = !empty($_POST['users']) ? getArrayFromList($_POST['users']) : array();
		# Get the period
		$period = !empty($_POST['period']) ? trim($_POST['period']) : null;
		# Order by most popular
		$popular = !empty($_POST['popular']) ? true : false;
		$order = !empty($_POST['order']) ? $_POST['order'] : 'latest';
		if ($order == 'popular') {
			$popular = true;
		}
		$post_status = !empty($_POST['post_status']) ? trim($_POST['post_status']) : 1;

		$tribe_id = !empty($_POST['tribe']) ? trim($_POST['tribe']) : '';

		# On recupere les informtions sur les membres
		$sql = '';
		if ($tribe_id != '') {
			$rs_tribe = $core->con->select("SELECT
				tribe_tags,
				tribe_notags,
				tribe_users,
				tribe_nousers,
				tribe_search
				FROM ".$core->prefix."tribe
				WHERE tribe_id = '".$tribe_id."'");
			if ($rs_tribe->count() == 1) {
				$tribe_tags = preg_split('/,/',$rs_tribe->f('tribe_tags'), -1, PREG_SPLIT_NO_EMPTY);
				foreach($tribe_tags as $tag) {
					$tags[] = $tag;
				}
				$tribe_users = preg_split('/,/',$rs_tribe->f('tribe_users'), -1, PREG_SPLIT_NO_EMPTY);
				foreach($tribe_users as $user) {
					$users[] = $user;
				}
				$tribe_notags = preg_split('/,/',$rs_tribe->f('tribe_notags'), -1, PREG_SPLIT_NO_EMPTY);
				foreach($tribe_notags as $notag) {
					$key = array_search($notag, $tags);
					unset($tags[$key]);
				}
				$tribe_nousers = preg_split('/,/',$rs_tribe->f('tribe_nousers'), -1, PREG_SPLIT_NO_EMPTY);
				foreach($tribe_nousers as $nouser) {
					$key = array_search($nouser, $users);
					unset($users[$key]);
				}
			}
		}
		# Terminaison de la commande SQL
		if ($tribe_id != null) {
			$sql = generate_tribe_SQL(
				$tribe_id,
				$num_start,
				$nb_items,
				$popular,
				$search_value);
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
				null,
				$post_status);
		}
#		print $sql;
#		exit;
		$rs = $core->con->select($sql);

		$tpl = new Hyla_Tpl(dirname(__FILE__).'/../themes/'.$blog_settings->get('planet_theme').'/');
		$tpl->importFile('index', 'index.tpl');
		$tpl->setVar('planet', array(
			'url' => BP_PLANET_URL,
			'theme' => $blog_settings->get('planet_theme')
		));

		$tpl->render('menu.filter');

		# Liste des articles
		$tpl = showPosts($rs, $tpl, $search_value, true, $popular);

		$result = array(
			"posts" => $tpl->render('content.posts'),
			"nb_items" => $nb_items,
			"page" => $num_page,
			"users" => $users,
			"tags" => $tags,
			"search" => $search_value
			);
		print $result['posts'];
		break;

##########################################################
# REFRESH TRIBE LIST
##########################################################
	case 'tribe':
		$tribe_id = $_POST['tribe_id'];
        $page = intval($_POST['page']);
        $num_start = $page*10;

        $sql_tribe = "SELECT * FROM ".$core->prefix."tribe WHERE tribe_id='".$tribe_id."'";
        if ($tribe_id == "") {
            $sql_tribe = generate_SQL(
                    0, // num start
                    10, // nb items
                    array(), // users
                    array(), // tags
                    null, // search
                    "week", // period
                    true); // popular
        }
        $rs = $core->con->select($sql_tribe);
        if ($rs->count() > 0) {
            $tribe_icon = getTribeIcon($rs->f('tribe_id'),$rs->f('tribe_name'),$rs->f('tribe_icon'));
            $tribe = array(
                "title" => $rs->f('tribe_name'),
                "id" => $rs->f('tribe_id'),
                "icon" => $tribe_icon,
                "page" => $page
                );
            $core->tpl->setVar('tribe', $tribe);

            $sql_posts = generate_tribe_SQL($tribe_id,$num_start);
            $rs_posts = $core->con->select($sql_posts);
            $tpl = showTribe($core->tpl,$rs_posts);
        }
		
		print $tpl->render('portal.block');
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
