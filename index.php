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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/inc/i18n.php');
require_once(dirname(__FILE__).'/inc/fonctions.php');
# On active le cache
debutCache();

# Valeurs par defaut
$num_page = 0;
$num_start = 0;
$num_membre = '';
global $recherche;
$recherche = '';

# Verification du contenu du get
if (isset($_GET) && isset($_GET['page']) && is_numeric(trim($_GET['page']))) {
	# On recuepre la valeur du get
	$num_page = trim($_GET['page']);
	if ($num_page < 1) {
		$num_page = 0;
	}
	$num_start = $num_page * $nb_article;
}

/* On recupere les infomations des articles */
$debut_sql = "SELECT nom_membre, article_pub, article_titre, article_url, article_content, site_membre, num_article, article_score, email_membre, membre.num_membre
	FROM article, membre
	WHERE article.num_membre =  membre.num_membre
	AND article_statut = '1'
	AND statut_membre = '1'
	AND article_score > $planet_votes_limite ";
$fin_sql = " ORDER BY article_pub DESC
	LIMIT $num_start,$nb_article";

if (isset($_GET) && isset($_GET['populaires']) && !empty($_GET['populaires'])) {
	$populaires = $_GET['populaires'];
	$debut_sql = "SELECT nom_membre, article_pub, article_titre, article_url, SUBSTRING(article_content,1,400), site_membre, num_article, article_score, email_membre, membre.num_membre
		FROM article, membre
		WHERE article.num_membre =  membre.num_membre
		AND article_statut = '1'
		AND statut_membre = '1'
		AND article_score > '0'";
	$fin_sql = " ORDER BY article_score DESC LIMIT 0,$nb_article";
	if (isset($_GET) && !(isset($_GET['tri']) && !empty($_GET['tri']))) {
		$day = mktime(0, 0, 0, date("d",time()),  date("m",time()), date("Y",time()));
		$week = time() - 3600*24*7;
		$month = time() - 3600*24*31;
		# On fonction du choix
		switch($populaires) {
			case "day"    : $debut_sql = $debut_sql." AND article_pub > ".$day; break;
			case "week" : $debut_sql = $debut_sql." AND article_pub > ".$week; break;
			case "month"    : $debut_sql = $debut_sql." AND article_pub > ".$month; break;
			default        : $debut_sql = $debut_sql." AND article_pub > ".$week; $tri="week"; break;
		}
	}
}

# Si le lecteur a fait une recherche
if (isset($_GET) && isset($_GET['search']) && !empty($_GET['search'])) {
	# On recupere la chaine de recherche
	$recherche = addslashes(trim($_GET['search']));
	#$sql_search = "AND (article_titre LIKE '%$recherche%' OR article_url LIKE '%$recherche%' OR article_content LIKE '%$recherche%' OR site_membre LIKE '%$recherche%' OR nom_membre LIKE '%$recherche%')";
	#$debut_sql = $debut_sql." ".$sql_search;
	$debut_sql = "SELECT nom_membre, article_pub, article_titre, article_url, article_content,
		site_membre, num_article,article_score, email_membre, membre.num_membre,
		match(article_titre,article_content) against('".$recherche."') as relevance
		FROM article, membre
		WHERE article.num_membre =  membre.num_membre
		AND article_statut = '1'
		AND statut_membre = '1'
		AND article_score > ".$planet_votes_limite."
		AND (match(article_titre, article_content) against('".$recherche."'))";
	$fin_sql = "ORDER BY relevance DESC
		LIMIT 0,$nb_article";
}

# On recupere le numero du membre
if(isset($_GET) && isset($_GET['num_membre']) && is_numeric(trim($_GET['num_membre']))) {
  # On recuepre la valeur du get
  $num_membre = trim($_GET['num_membre']);
  $sql_membre = "AND article.num_membre = '$num_membre'";
  $debut_sql = $debut_sql." ".$sql_membre;
}

# On recupere la valeur du filtre
if (isset($_GET) && isset($_GET['tri']) && !empty($_GET['tri'])) {
	# On recupere la valeur du get
	$tri = trim($_GET['tri']);
	# Calcul des dates au format timestamp
	$day = mktime(0, 0, 0, date("m",time()), date("d",time()), date("Y",time()));
	$week = time() - 3600*24*7;
	$month = time() - 3600*24*31;
	# On fonction du choix
	switch($tri) {
		case "day"    : $debut_sql = $debut_sql." AND article_pub > ".$day; break;
		case "week" : $debut_sql = $debut_sql." AND article_pub > ".$week; break;
		case "month"    : $debut_sql = $debut_sql." AND article_pub > ".$month; break;
		default        : $debut_sql = $debut_sql." AND article_pub > ".$week; $tri="week"; break;
	}
}
	
# Terminaison de la commande SQL
$sql = $debut_sql." ".$fin_sql;

$params = "";
if (isset($_GET) && isset($_GET['search']) && !empty($_GET['search'])){
	$params = $params."search=".$_GET['search']."&";
}
if (isset($_GET) && isset($_GET['page']) && !empty($_GET['page'])){
	$params = $params."page=".$_GET['page']."&";
}
if (isset($_GET) && isset($_GET['num_membre']) && !empty($_GET['num_membre'])){
	$params = $params."num_membre=".$_GET['num_membre']."&";
}
if (isset($_GET) && isset($_GET['populaires']) && !empty($_GET['populaires'])){
	$params = $params."populaires=".$_GET['populaires']."&";
}

include(dirname(__FILE__).'/head.php');
?>
<div id="centre">

<?php
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="centre_centre">

<?php
# Si on veut afficher un message d'information generale

connectBD();
#Affichage de la barre de tri
?>
<div class="tri">
	<b><?=T_('Filter the posts :');?>&nbsp;&nbsp;&nbsp;&nbsp;</b>
	<span <?php if(isset($tri) && $tri=="day") echo 'id="triSelected"'; ?>>
		<a href="index.php?<?php echo $params."tri=day"; ?>"><?=T_('Posts of the day');?></a>
	</span>&nbsp;&nbsp;-&nbsp;&nbsp;  
	<span <?php if(isset($tri) && $tri=="week") echo 'id="triSelected"'; ?>>
		<a href="index.php?<?php echo $params."tri=week"; ?>"><?=T_('Posts of the week');?></a>
	</span>&nbsp;&nbsp;-&nbsp;&nbsp;
	<span <?php if(isset($tri) && $tri=="month") echo 'id="triSelected"'; ?>>
		<a href="index.php?<?php echo $params."tri=month"; ?>"><?=T_('Posts of the month');?></a>
	</span>&nbsp;&nbsp;-&nbsp;&nbsp;
	<span>
		<a href="index.php?<?php echo $params; ?>"><?=T_('All posts');?></a>
	</span>
</div>
<?php
if (isset($_GET) && isset($_GET['search']) && !empty($_GET['search'])){
?>
<div class="search">
<span class="searchText"><?php printf(T_('You are searching for all the posts with <span class="search">%s</span>'),$_GET['search'])?></span>
</div>
<?php
}

# Affichage du sommaire
afficheSommaireArticles($sql);

# Navigation par pages
$nb = mysql_num_rows(mysql_query(trim($sql)));

include(dirname(__FILE__).'/pagination.php');

# Liste des articles
if (isset($_GET) && isset($_GET['populaires']) && !empty($_GET['populaires']))
	afficheListeArticles($sql, 1);
else
	afficheListeArticles($sql, 0, $recherche);

# Navigation par pages
include(dirname(__FILE__).'/pagination.php');

include(dirname(__FILE__).'/footer.php');

# Fermeture de la base de donnees
closeBD(); 

# On termine le cache
finCache();
?>
