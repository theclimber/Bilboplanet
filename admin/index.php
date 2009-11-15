<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.org
* Website : www.bilboplanet.org
* Tracker : redmine.bilboplanet.org
* Blog : blog.bilboplanet.org
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
		$list_articles .= '<li>'.$date.' : <a href="'.$article[3].'" title="'.$titre.' ('.$article[0].')" target="_blank">'.$show.'</a></li>';
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

			<li><?=T_('Current size of the database :'); echo ' <strong>'.formatfilesize(get_database_size()).'</strong>';?></li>
			<li><?=T_('Posts of the day :');?></li>
			<li><?=T_('Mean posts per day :');?></li>
			<li><?=T_('Votes of the day :');?></li>
			<li><?=T_('Mean votes per day :');?></li>
		</ul>
	</div>
</div>
</fieldset>

<?php include(dirname(__FILE__).'/footer.php'); ?>
