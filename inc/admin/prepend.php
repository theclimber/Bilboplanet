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

if (isset($_SERVER['BP_CONFIG_PATH'])) {
	define('BP_CONFIG_PATH',$_SERVER['BP_CONFIG_PATH']);
} elseif (isset($_SERVER['REDIRECT_BP_CONFIG_PATH'])) {
	define('BP_CONFIG_PATH',$_SERVER['REDIRECT_BP_CONFIG_PATH']);
} else {
	define('BP_CONFIG_PATH',dirname(__FILE__).'/../config.php');
}

if (!is_file(BP_CONFIG_PATH))
{
	$url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']."../admin/install/";
	header("Location: ".$url,TRUE,302);
	exit();
}

require_once dirname(__FILE__).'/../prepend.php';
// HTTP/1.1
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

// HTTP/1.0
header("Pragma: no-cache");

define('BP_CONTEXT_ADMIN',true);


function showNextButton($next_page, $nb_items, $script) {
	return '<a href="#" onclick="javascript:'.$script.'(\''.$next_page.'\', \''.$nb_items.'\')" class="page_svt">'.T_('Next Page').' &raquo;</a>';
}

function showPrevButton($prev_page, $nb_items, $script) {
	return '<a href="#" onclick="javascript:'.$script.'(\''.$prev_page.'\', \''.$nb_items.'\')" class="page_prc">&laquo; '.T_('Previous page').'</a>';
}

function showPagination($count, $num_page, $nb_items, $script) {
	$next_page = $num_page + 1;
	$prev_page = $num_page - 1;

	$output = '<div class="navigation">';
	if($num_page == -1) {
		# this page has no next or previous page
	} elseif($num_page == 0 & $count >= $nb_items) {
		# if we are on the first page
		$output .= showNextButton($next_page, $nb_items, $script);
	} elseif($num_page == 0 & $count< $nb_items) {
		# we don't show any button
	} else {
		if($count == 0 | $count < $nb_items) {
			# if we are on the last page
			$output .= showPrevButton($prev_page, $nb_items, $script);
		} else {
			$output .= showPrevButton($prev_page, $nb_items, $script);
			$output .= showNextButton($next_page, $nb_items, $script);
		}
	}
	$output .= '</div><!-- fin pagination -->';
	return $output;
}

?>
