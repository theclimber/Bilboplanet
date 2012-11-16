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
# Fonction qui retourne le nom de domaine en fonction de l'url
function domain($referer) {
	# On recupere l'adresse sans le protocole
	$referer_light = substr(strstr($referer, "://"), 3);
	# Si il n' y a pas d'url, on retourne rien
	if (empty($referer_light)) return "";
	# On recharche si il y a une / dans l'url et si oui on retourne l'adresse jusqu'a ce caractere
	if (($qm = strpos($referer_light, "/")) !== false) $referer_light = substr($referer_light, 0, $qm);
	# On retourne le resultat
	return $referer_light;
}

require_once(dirname(__FILE__).'/inc/prepend.php');
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

# Nombre de ligne a afficher
$nb = 15;

$sql = "SELECT
		".$core->prefix."user.user_id AS user_id,
		user_fullname AS fullname,
		site_url,
		COUNT( post_id ) AS nb_post
	FROM ".$core->prefix."post, ".$core->prefix."user, ".$core->prefix."site
	WHERE
		".$core->prefix."site.user_id = ".$core->prefix."user.user_id
		AND ".$core->prefix."user.user_id = ".$core->prefix."post.user_id
		AND user_status = 1
	GROUP BY user_fullname
	ORDER BY nb_post DESC
	LIMIT $nb";
$rs = $core->con->select($sql);
while ($rs->fetch()) {
	$core->tpl->setVar("active", array(
		"fullname" => $rs->fullname,
		"site_url" => $rs->site_url,
		"domain_url" => domain($rs->site_url),
		"nb_posts" => $rs->nb_post
		));
	$core->tpl->render('stats.main.line');
}

if ($blog_settings->get('planet_vote')) {
	# On recupere la liste et on affiche
	$sql = "SELECT
			".$core->prefix."user.user_id as user_id,
			user_fullname as fullname,
			site_url,
			SUM(post_score) AS score
		FROM ".$core->prefix."post, ".$core->prefix."user, ".$core->prefix."site 
		WHERE
			".$core->prefix."site.user_id = ".$core->prefix."user.user_id
			AND ".$core->prefix."user.user_id = ".$core->prefix."post.user_id
			AND user_status = 1
		GROUP BY user_fullname
		ORDER BY score DESC
		LIMIT $nb";
	$rs = $core->con->select($sql);

	while ($rs->fetch()){
		$core->tpl->setVar("votes", array(
			"fullname" => $rs->fullname,
			"site_url" => $rs->site_url,
			"domain_url" => domain($rs->site_url),
			"score" => $rs->score));
		$core->tpl->render('stats.votes.line');
	}

	$core->tpl->render('stats.votes');

	$core->tpl->setVar('nb_votes', getNbVotes($core->con));
	$core->tpl->render('stats.votes.resume');
}

$core->tpl->setVar('nb', array(
	"nb_users" => getNbUsers($core->con),
	"nb_feeds" => getNbFeeds($core->con),
	"nb_posts" => getNbPosts($core->con)
));
$core->tpl->setVar('params', $params);
$core->tpl->render('content.stats');
$core->renderTemplate();
?>
