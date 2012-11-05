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

	case 'page':
		$page = $_POST['page'];
		print render_page($page);
		break;

	case 'post':
		$post_id = $_POST['post_id'];
		$search_value = trim($POST['search_value']);

		$sql = generate_SQL(
			0, // nbitems
			10, // nbitems
			array(), // users
			array(), // tags
			'', // search_value
			null, //period
			false, //popular
			$post_id,
			1);
		$rs = $core->con->select($sql);
		
		$tpl = showSinglePost($rs,$core->tpl,$search_value,true,true);
		print $tpl->render('post.block');
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
