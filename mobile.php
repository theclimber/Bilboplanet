<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
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
require_once(dirname(__FILE__).'/inc/i18n.php');
require_once(dirname(__FILE__).'/inc/fonctions.php');

# On active le cache
debutCache();

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Cache-Control" content="public"/>
<meta http-equiv="content-language" content="fr-fr" />
<meta name="description" content="<?php echo $planet_desc_meta; ?>" />
<meta name="keywords" content="<?php echo $planet_keywords; ?>" />
<link href="themes/<?php echo $planet_theme; ?>/style_mobile.css" rel="stylesheet" type="text/css" />
<link rel="alternate" type="application/rss+xml"  title="RSS"  href="feed.php?type=rss" />
<link rel="alternate" type="application/atom+xml" title="ATOM" href="feed.php?type=atom" />
<title><?php echo $planet_title; ?> - <?=T_('Mobile version');?></title>
</head>
<body>
<div id="header">
<?php echo $planet_title; ?> <small>- <?=T_('Mobile version');?></small>
</div>
<?php

# On recupere le mode d'affichage
$affichage = "sommaire";
if(isset($_GET) && isset($_GET['affichage']) && (trim($_GET['affichage']) == "sommaire" || trim($_GET['affichage']) == "detail") ) {
	$affichage = trim($_GET['affichage']);
}

# On recupere le nombre d'article si definit
if(isset($_GET) && isset($_GET['nb_articles']) && is_numeric(trim($_GET['nb_articles'])) ) {
	$nb_article_mobile = addslashes(trim($_GET['nb_articles']));
}

# Calcule du nombre d'articles possibles a afficher (max = 20)
$articles_mobile_max = 20;
$nb_articles_affiche = $nb_article_mobile + 5;
if($nb_articles_affiche > $articles_mobile_max) $nb_articles_affiche = $articles_mobile_max;
if($nb_article_mobile > $articles_mobile_max) $nb_article_mobile = $articles_mobile_max;

# Affichage du start_over
echo '<div id="start_over">';
echo '<a href="?affichage=sommaire" title="'.T_('Go to the summary').'">'.T_('Summary').'</a> ';
echo '| <a href="?affichage=detail" title="'.T_('Show posts content').'">'.T_('Posts detail').'</a> ';
echo '| <a href="?nb_articles='.$nb_articles_affiche.'" title="'.T_('See more posts').'">'.T_('See more posts').'</a> ';
echo '| <a href="'.$planet_url.'" title="'.T_('Back to site').'">'.T_('Back to site').'</a>';
echo '</div>';

# On recupere les infomations des articles
$sql = "SELECT nom_membre, article_pub, article_titre, article_url, article_content, site_membre, num_article, article_score
	FROM article, membre 
	WHERE article.num_membre =  membre.num_membre
	AND article_statut = '1'
	AND statut_membre = '1'
	AND article_score > $planet_votes_limite
	ORDER BY article_pub DESC
	LIMIT 0,$nb_article_mobile";

# Connexion a la base de donnees
connectBD();

# Execution de la rqt
$liste_articles = mysql_query(trim($sql)) or die("Error with request $sql");

# On affiche le contenu
echo '<div id="sp_results">';
echo "<p>".sprintf("The %s last published posts on %s",$nb_article_mobile,$plannet_title)."</p>";
while ($liste = mysql_fetch_row($liste_articles)) {

	# Convertion en UTF8 des valeurs
	$titre = convert_iso_special_html_char(html_entity_decode(html_entity_decode($liste[2], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
	$nom = convert_iso_special_html_char(html_entity_decode(html_entity_decode($liste[0], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
	$item = convert_iso_special_html_char(html_entity_decode(html_entity_decode($liste[4], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));

	# Formatage de la date et heure
	$date = date("d/m/Y",$liste[1]);
	$heure = date("H:i",$liste[1]);

	# On vire les balises images
	$item = preg_replace('`<img[^>]*>`', '', $item);
	#  $item = ereg_replace("<img.*src=\"(.*)\".*>","<a href=\"\\1\" title=\"Visioner l'image\"><img src=\"monimage\" alt=\"Image\"/></a>",$item);

	echo '<hr/>';
	echo '<div class="chunk">';
	if ($affichage == "sommaire") {

		# Affichage du sommaire
		echo '<p class="topic"><a href="'.$liste[3].'" title="'.T_('Visit source').'" rel="nofollow">';
		echo $nom.' : '.$titre'.</a></p>';

	} else {

		# Affichage de tout
		echo '<p class="topic"><a href="'.$liste[5].$liste[3].'" title="'.T_('Visit source').'" rel="nofollow">';
		echo $nom.' : '.$titre.'</a></p><br/>'.$item;
	}
	echo '<p class="datestamp">'.$date.' : '.$heure.' | '.sprintf(T_("%d vote(s)"), $liste[7]).'</p></div>';
}

# Femeture de la base
closeBD();

# Pied de page
include(dirname(__FILE__).'footer.php');

# On termine le cache
finCache();
?>
