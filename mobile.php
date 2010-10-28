<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
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
# Create the Hyla_Tpl object
$core->tpl = new Hyla_Tpl(dirname(__FILE__).'/themes/'.$blog_settings->get('planet_theme'));
$core->tpl->setL10nCallback('T_');
$core->tpl->importFile('mobile','mobile.tpl');
$core->tpl->displayError(true);
#$core->tpl->setCurrentFile('mobile.tpl');
$core->tpl->setVar('planet', array(
	"url"	=>	$blog_settings->get('planet_url'),
	"theme"	=>	$blog_settings->get('planet_theme'),
	"title"	=>	$blog_settings->get('planet_title'),
	"desc"	=>	$blog_settings->get('planet_desc'),
	"keywords"	=>	$blog_settings->get('planet_keywords'),
	"desc_meta"	=>	$blog_settings->get('planet_desc_meta'),
	"msg_info" => $blog_settings->get('planet_msg_info'),
));

# On recupere le nombre d'article si definit
if(isset($_GET) && isset($_GET['nb_posts']) && is_numeric(trim($_GET['nb_posts'])) ) {
	$nb_posts = addslashes(trim($_GET['nb_posts']));
	if ($nb_posts > 50) { # Max 50 posts shown
		$nb_posts = 50;
	}
} else {
	$nb_posts = $blog_settings->get('planet_nb_art_mob');
}
$core->tpl->setVar("more", $nb_posts+5);
$core->tpl->setVar("nb_posts", $nb_posts);
$core->tpl->setVar("mobile", array(
	"title" => sprintf("The %s last published posts on %s",$nb_posts,$blog_settings->get('planet_title')),
	"params" => '&nb_posts='.$nb_posts,
));

# On recupere le mode d'affichage
$layout = "summary";
if(isset($_GET['layout']) && (trim($_GET['layout']) == "summary" || trim($_GET['layout']) == "detail") ) {
	$layout = trim($_GET['layout']);
}

# On recupere les infomations des articles
$sql = "SELECT
		user_fullname,
		post_pubdate,
		post_title,
		post_permalink,
		post_content,
		".$core->prefix."user.user_id as user_id,
		post_id,
		post_score
	FROM ".$core->prefix."post, ".$core->prefix."user
	WHERE ".$core->prefix."post.user_id = ".$core->prefix."user.user_id
	AND post_status = '1'
	AND user_status = '1'
	AND post_score > ".$blog_settings->get('planet_votes_limit')."
	ORDER BY post_pubdate DESC
	LIMIT 0,$nb_posts";
$rs = $core->con->select($sql);

$i = 0;
while ($rs->fetch()) {
	# Convertion en UTF8 des valeurs
	$title = convert_iso_special_html_char(html_entity_decode(html_entity_decode($rs->post_title, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
	$author = convert_iso_special_html_char(html_entity_decode(html_entity_decode($rs->user_fullname, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
	$content = convert_iso_special_html_char(html_entity_decode(html_entity_decode($rs->post_content, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
	$pubdate = mysqldatetime_to_date('d/m/Y : H:i',$rs->post_pubdate);

	# On vire les balises images
	if ($_GET['img'] == 'no') {
		$item = preg_replace('`<img[^>]*>`', '', $item);
	}

	$class='chunk';
	if ($i%2==0) {
		$class .= " diff";
	}
	$i++;

	$post = array(
		"id" => $rs->post_id,
		"title" => $title,
		"content" => $content,
		"author" => $author,
		"permalink" => $rs->post_permalink,
		"votes" => sprintf(T_("%d vote(s)"), $rs->post_score),
		"pubdate" => $pubdate
		);

	$core->tpl->setVar('post_class', $class);
	$core->tpl->setVar('post', $post);

	if ($layout == "summary") {
		$core->tpl->render("line");
	} else { # Affichage de tout
		$core->tpl->render("detail");
	}
}
echo $core->tpl->render();
?>
