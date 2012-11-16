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
?>
<?php
# Fonction convertion de mois (= date("n")) en fr
function convertMonth($num_month) {
	$month = array(1 => T_("January"), T_("February"), T_("March"), T_("April"), T_("May"), T_("June"), T_("July"), T_("August"), T_("September"), T_("October"), T_("November"), T_("December"));
	return $month[$num_month];
}

require_once(dirname(__FILE__).'/inc/prepend.php');
$scripts = array();
$scripts[] = "javascript/show-hide.js";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

# On recupere les infomations des articles
$sql = "SELECT
			".$core->prefix."post.post_id as post_id,
			user_fullname as fullname,
			post_pubdate as pubdate,
			post_title,
			post_permalink as permalink
		FROM ".$core->prefix."post, ".$core->prefix."user
		WHERE ".$core->prefix."post.user_id = ".$core->prefix."user.user_id
		AND post_status = '1'
		ORDER BY post_pubdate DESC";
$rs = $core->con->select($sql);

$last_month = 0;
/* Boucle d'affichage des archives du mois */
$iter = 0;
while ($rs->fetch()) {
	$current_month = date('n', mysqldatetime_to_timestamp($rs->pubdate));
	$post = array(
			"post_id" => $rs->post_id,
			"permalink" => $rs->permalink,
			"fullname" => $rs->fullname,
			"title" => htmlspecialchars_decode($rs->post_title),
			"head" => "",
		);
	if ($current_month != $last_month && $iter > 0) {
		$post['head'] = "</ul>\n\t";
	}
	/* Si le mois de l'article est different du mois en cours */
	if ($current_month != $last_month) {
		$post['head'] .= '<h3>'.
			convertMonth($current_month).' '.date("Y", mysqldatetime_to_timestamp($rs->pubdate)).
			"</h3>\n\t<ul>";
	}

	$core->tpl->setVar('post', $post);
	$core->tpl->render('archives.line');

	$last_month = $current_month;
	$iter+=1;
}
if ($iter > 0) {
	$core->tpl->render('archives.closure');
}

$core->tpl->setVar('params', $params);
$core->tpl->render('content.archives');
$core->renderTemplate();
?>
