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
echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";

require_once(dirname(__FILE__).'/inc/i18n.php');
require_once(dirname(__FILE__).'/inc/fonctions.php');

# Verification du contenu du get
if (isset($_GET) && isset($_GET['type'])) {
	if ($_GET['type']=="rss"){
		header('Content-Type: application/rss+xml; charset=UTF-8');
	}
	elseif($_GET['type']=="atom") {
		header('Content-Type: application/atom+xml; charset=UTF-8');
	}

	# On active le cache
	debutCache();

	# On recupere les infomations des articles
	if (isset($_GET["popular"]) && !empty($_GET['popular'])){
		# Calcul des dates au format timestamp
		$semaine = time() - 3600*24*7;
		$title = $planet_title." - Popular";
		$sql = "SELECT nom_membre, article_pub, article_titre, article_url, article_content, site_membre, num_article, email_membre
				FROM article, membre
				WHERE article.num_membre =  membre.num_membre
				AND article_statut = '1'
				AND statut_membre = '1'
				AND article_score > '0'
				AND article_pub > $semaine
				ORDER BY article_score DESC 
				LIMIT 0,$nb_article";
	}
	else {
		$title = $planet_title;
		$sql = "SELECT nom_membre, article_pub, article_titre, article_url, article_content, site_membre, num_article, email_membre
			FROM article, membre 
			WHERE article.num_membre =  membre.num_membre
			AND article_statut = '1'
			AND statut_membre = '1'
			AND article_score > $planet_votes_limite
			ORDER BY article_pub DESC
			LIMIT 0,$nb_article_flux";
	}

	# Connexion a la base de donnees
	connectBD();

	# Execution des rqt
	$liste_rapide = mysql_query(trim($sql)) or die("Error with request $sql");
	$liste_articles = mysql_query(trim($sql)) or die("Error with request $sql");

	# Femeture de la base
	closeBD();

	if ($_GET['type']=="rss"){
?>
		<rdf:RDF
			xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
			xmlns:dc="http://purl.org/dc/elements/1.1/"
			xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
			xmlns:admin="http://webns.net/mvcb/"
			xmlns:cc="http://web.resource.org/cc/"
			xmlns:content="http://purl.org/rss/1.0/modules/content/"
			xmlns="http://purl.org/rss/1.0/">

		<channel rdf:about="<?php echo $planet_url; ?>">
		<title><?php echo $title; ?></title>
		<description><?php echo $planet_desc; ?></description>
		<link><?php echo $planet_url; ?></link>
		<dc:language><?php echo $planet_lang; ?></dc:language>
		<dc:creator><?php echo $planet_author; ?></dc:creator>
		<dc:rights></dc:rights>
		<dc:date><?php echo date('Y-m-d\\TH:i:s+00:00'); ?></dc:date>
		<admin:generatorAgent rdf:resource="<?php echo $planet_url; ?>" />

		<items>
		<rdf:Seq>
<?php 
		while ($liste = mysql_fetch_row($liste_rapide)) {
			$url = $liste[3];
			if (strcmp(substr($url,0,7), 'http://') != 0)
				$url = $liste[5].$url;
			echo '<rdf:li rdf:resource="'.$url.'"/>'."\n";
		}
?>
		</rdf:Seq>
		</items>
		</channel>
<?php
	}
	elseif($_GET['type']=="atom") {
?>
		<feed xmlns="http://www.w3.org/2005/Atom">
			<title><?php echo $title; ?></title>
			<subtitle><?php echo $planet_desc; ?></subtitle>
			<id><?php echo $planet_url; ?></id>
			<link rel="alternate" type="text/html" href="<?php echo $planet_url; ?>" />
			<link rel="self" href="<?php echo $planet_url; ?>atom10.php" />
			<updated><?php echo date("Y-m-d\TH:i:s\Z") ?></updated>
			<author><name><?php echo $planet_author; ?></name></author>
<?php 
	}

	while ($liste = mysql_fetch_row($liste_articles)) {
		/* Convertion en UTF8 des valeurs */
		$titre = convert_iso_special_html_char(html_entity_decode(html_entity_decode($liste[2], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
		$nom = convert_iso_special_html_char(html_entity_decode(html_entity_decode($liste[0], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
		$item = convert_iso_special_html_char(html_entity_decode(html_entity_decode($liste[4], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));

		# Gravatar
		if($planet_avatar) {
			$gravatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($liste[7])."&amp;default=".urlencode($planet_url."themes/$planet_theme/images/gravatar.png")."&amp;size=40";
			$gravatar = '<img src="'.$gravatar_url.'" alt="'.sprintf(T_('Gravatar of %s'),$liste[0]).'" class="gravatar" />';
		}

		# Liens divers
		$url = $liste[3];
		if (strcmp(substr($url,0,7), 'http://') != 0)
			$url = $liste[5].$url;
		$links =  '<br/><i>'.sprintf('Original post of <a href="%s" title="Visit the source">%s</a>.',$url, $nom);
		$links .= '<br/>'.sprintf(T_('Vote for this post on <a href="%s" title="Go on the planet">%s</a>.'),$planet_url, $planet_title).'</i>';


		if ($_GET['type']=="rss"){
			# Affichage du contenu de l'item 
			echo '<item rdf:about="'.$url.'">';
			echo '<title>'.$nom.' : '.$titre.'</title>';
			echo '<link>'.$url.'</link>';
			echo '<dc:date>'.date('Y-m-d\\TH:i:s+00:00',$liste[1]).'</dc:date>';
			echo '<dc:language>'.$planet_lang.'</dc:language>';
			echo '<dc:creator>'.$nom.'</dc:creator>';
			echo '<dc:subject></dc:subject>';
			echo '<description></description>';
			if($planet_avatar) {
				#echo '<content:encoded><![CDATA['.$gravatar.$item.$links.']]></content:encoded>';
				echo '<content:encoded><![CDATA['.$item.'<p>'.$gravatar.$links.'</p>'.']]></content:encoded>';
			} else {
				echo '<content:encoded><![CDATA['.$item.'<p>'.$links.'</p>'.']]></content:encoded>';
			}
			echo '</item>'; 
		}
		elseif($_GET['type']=="atom") {
			# Affichage du contenu de l'item
			echo '<entry xmlns="http://www.w3.org/2005/Atom">';
			echo '<title type="html">'.$nom.' : '.$titre.'</title>';
			echo '<id>'.$url.'</id>';
			echo '<link rel="alternate" href="'.$url.'"/>';
			echo '<published>'.date('Y-m-d\\TH:i:s+00:00',$liste[1]).'</published>';
			echo '<updated>'.date('Y-m-d\\TH:i:s+00:00',$liste[1]).'</updated>';
			echo '<author><name>'.$nom.'</name></author>';
			if($planet_avatar) {
				echo '<content type="html"><![CDATA['.$item.'<p>'.$gravatar.$links.'</p>'.']]></content>';
			} else {
				echo '<content type="html"><![CDATA['.$item.'<p>'.$links.'</p>'.']]></content>';
			}
			echo '</entry>';
		}
	}

	if ($_GET['type']=="rss"){
		echo "</rdf:RDF>";
	}
	elseif($_GET['type']=="atom") {
		echo "</feed>";
	}

	/* On termine le cache */
	finCache();
}
else echo T_("There is no feed here");
?>
