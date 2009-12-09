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
/* Inclusion du fichier de configuration */
require_once(dirname(__FILE__).'/../inc/fonctions.php');

$nb_articles = 0;
$nb_votes = 0;
$nb_members = 0;
$nb_feeds = 0;
#2) Et ce serai bien de rajouter dans le dashboard : nombre d'article sur le planet + nombre de vote au total + nmobre de membre + nombre de flux
connectBD();
$sql = "SELECT COUNT(*) FROM article";
$request = mysql_query($sql) or die("Error with request $sql");
if ($request)
	$nb_articles = mysql_fetch_row($request);
$sql = "SELECT COUNT(*) FROM votes";
$request = mysql_query($sql) or die("Error with request $sql");
if ($request)
	$nb_votes = mysql_fetch_row($request);
$sql = "SELECT COUNT(*) FROM membre";
$request = mysql_query($sql) or die("Error with request $sql");
if ($request)
	$nb_members = mysql_fetch_row($request);
$sql = "SELECT COUNT(*) FROM flux";
$request = mysql_query($sql) or die("Error with request $sql");
if ($request)
	$nb_feeds = mysql_fetch_row($request);
closeBD;


include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');

function showArticleSummary(){
	connectBD();
	/* On recupere les infomations des articles */
	$sql = "SELECT nom_membre, article_pub, article_titre, article_url
		FROM article, membre
		WHERE article.num_membre = membre.num_membre
		AND article_statut = '1'
		AND statut_membre = '1'
		ORDER BY article_pub DESC
		LIMIT 0,5";
	$request = mysql_query($sql) or die("Error with request $sql");
	$list_articles = "<ul>";
	$max_title_length = 50;
	while($article = mysql_fetch_row($request)){
		# Formatage de la date
		$date = date("d/m/Y",$article[1]);
		# Affichage du lien
		$titre = html_entity_decode($article[2], ENT_QUOTES, 'UTF-8');
		if (strlen($titre) > $max_title_length)
			$show = substr($titre,0, $max_title_length)."...";
		else
			$show = $titre;
		$list_articles .= '<li>'.$date.' : <a class="tips" href="'.$article[3].'" rel="<b><u>'.T_('User').':</u></b> '.$article[0].' <br><b><u>'.T_('Title').':</u></b> '.$titre.'" target="_blank">'.$show.'</a></li>';
	}
	$list_articles .= "</ul>";
	closeBD;
	return $list_articles;
}

?>
<div id="BP_page" class="page">
	<div class="inpage">
	
	<fieldset><legend><?=T_('Dashboard');?></legend>
		<div class="message">
			<p><?=T_('Quick summary of the planet');?></p>
		</div>
		
<div id="dashboard">
	<div class="box-dashboard"><div class="top-box-dashboard"><?=T_('Latest articles :');?></div>
<?php
echo showArticleSummary();
?>
	</div>
	<div class="box-dashboard"><div class="top-box-dashboard"><?=T_('Statistics :');?></div>
		<ul>

<?php
if (get_cron_running())
	echo '<li><div id="BP_startupdate">'.T_('The update is running').'</div></li>';
else
	echo '<li><div id="BP_stopupdate">'.T_('The update is stopped').'</div></li>';
if (file_exists(dirname(__FILE__).'/../inc/STOP'))
	echo '<li><div id="BP_disableupdate">'.T_('The update is disabled').'</div></li>';
?>

			<li><div id="BP_stats_db"><?=T_('Current size of the database :'); echo ' <strong>'.formatfilesize(get_database_size()).'</strong>';?></div></li>
			<li><div id="BP_nb_articles"><?=T_('Number of articles in the DB :'); echo ' <strong>'.$nb_articles[0].'</strong>';?></div></li>
			<li><div id="BP_nb_votes"><?=T_('Number of votes in the DB :'); echo ' <strong>'.$nb_votes[0].'</strong>';?></div></li>
			<li><div id="BP_nb_members"><?=T_('Number of members in the DB :'); echo ' <strong>'.$nb_members[0].'</strong>';?></div></li>
			<li><div id="BP_nb_feeds"><?=T_('Number of feeds in the DB :'); echo ' <strong>'.$nb_feeds[0].'</strong>';?></div></li>
		</ul>
	</div>
</div>
</fieldset>

<?php include(dirname(__FILE__).'/footer.php'); ?>
