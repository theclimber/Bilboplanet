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
require_once(dirname(__FILE__).'/../inc/config.php');
require_once(dirname(__FILE__).'/../inc/lib/simplepie/simplepie.inc');
function showArticleSummary(){
	$content = '<div class="box-dashboard"><div class="top-box-dashboard">'.T_('Latest articles :').'</div>';
	$nb_articles=0;
	connectBD();
	/* On recupere les infomations des articles */
	$sql = "SELECT nom_membre, article_pub, article_titre, article_url
		FROM article, membre
		WHERE article.num_membre = membre.num_membre
		AND article_statut = '1'
		AND statut_membre = '1'
		ORDER BY article_pub DESC
		LIMIT 0,5";
	$request = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
	$list_articles = "<ul>";
	$max_title_length = 50;
	while($article = mysql_fetch_row($request)){
		$nb_articles++;
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
	closeBD();
	$content .= $list_articles;
	$content .= '</div>';
	if ($nb_articles==0){
		return '';
	}
	return $content;
}


function showBlogLastArticles() {
	$content = '';
	$feed = new SimplePie();
	$feed->set_feed_url(array('http://bilboplanet.com/blog/feed/'));
	$feed->set_cache_duration (600);
	$feed->enable_xml_dump(isset($_GET['xmldump']) ? true : false);
	$success = $feed->init();
	$feed->handle_content_type();
	if ($success) {
		$content .= '<div class="box-dashboard"><div class="top-box-dashboard">'.T_('BilboPlanet news - Official Website :').'</div>';
		$content .= '<ul>';
		$itemlimit=0;
		foreach($feed->get_items() as $item) {
			if ($itemlimit==4) {
				break;
			}
			$content .= '<li>'.$item->get_date('j F Y - g:i a').' : ';
			$content .= '<a class="tips" rel="'.$item->get_title().'" href="'.$item->get_permalink().'" target="_blank">'.$item->get_title().'</a>';
			$content .= '</li>';
			$itemlimit = $itemlimit + 1;
		}
		$content .= '</ul></div>';
	}
	return $content;
}

function timeserver($timestamp = 0, $format = '%A %d %B %Y à %H:%M:%S') {

	$timestamp = ($timestamp) ? $timestamp : time();
	return strftime($format, $timestamp);
}

$nb_articles = 0;
$nb_votes = 0;
$nb_members = 0;
$nb_feeds = 0;
#2) Et ce serai bien de rajouter dans le dashboard : nombre d'article sur le planet + nombre de vote au total + nmobre de membre + nombre de flux
connectBD();
$sql = "SELECT COUNT(*) FROM article";
$request = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
if ($request)
	$nb_articles = mysql_fetch_row($request);
$sql = "SELECT COUNT(*) FROM votes";
$request = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
if ($request)
	$nb_votes = mysql_fetch_row($request);
$sql = "SELECT COUNT(*) FROM membre";
$request = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
if ($request)
	$nb_members = mysql_fetch_row($request);
$sql = "SELECT COUNT(*) FROM flux";
$request = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
if ($request)
	$nb_feeds = mysql_fetch_row($request);
closeBD();


include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');

# Version MySQL
$version_mysql = "N/C";
if (function_exists('mysql_get_client_info')){
	$version_mysql = mysql_get_client_info();
}
# Version PHP
$version_php = "N/C";
if (function_exists('phpversion')){
	$version_php = phpversion();
}

?>
<div id="BP_page" class="page">
	<div class="inpage">
	
	<fieldset><legend><?php echo T_('Dashboard');?></legend>
		<div class="message">
			<p><?php echo T_('Quick summary of the planet');?></p>
		</div>
		
<div id="dashboard">
	<div class="box-container-left">
<?php
echo showArticleSummary();
?>
<p class="clear"/>
	<div class="box-dashboard"><div class="top-box-dashboard"><?php echo T_('Statistics :');?></div>
		<ul>

<?php
if (get_cron_running())
	echo '<li><div id="BP_startupdate">'.T_('The update is running').'</div></li>';
else
	echo '<li><div id="BP_stopupdate">'.T_('The update is stopped').'</div></li>';
if (file_exists(dirname(__FILE__).'/../inc/STOP'))
	echo '<li><div id="BP_disableupdate">'.T_('The update is disabled').'</div></li>';
if (BP_INDEX_UPDATE == '1')
	echo '<li><div id="BP_index_update">'.T_('The update on loading of index page enabled').'</div></li>';
?>
			<li><div id="BP_stats_db"><?php echo T_('Current size of the database :'); echo ' <strong>'.formatfilesize(get_database_size()).'</strong>';?></div></li>
			<li><div id="BP_nb_articles"><?php echo T_('Number of articles in the DB :'); echo ' <strong>'.$nb_articles[0].'</strong>';?></div></li>
			<li><div id="BP_nb_votes"><?php echo T_('Number of votes in the DB :'); echo ' <strong>'.$nb_votes[0].'</strong>';?></div></li>
			<li><div id="BP_nb_members"><?php echo T_('Number of members in the DB :'); echo ' <strong>'.$nb_members[0].'</strong>';?></div></li>
			<li><div id="BP_nb_feeds"><?php echo T_('Number of feeds in the DB :'); echo ' <strong>'.$nb_feeds[0].'</strong>';?></div></li>
		</ul>
		</div>
	<p class="clear"/>
	</div>
	<div class="box-container-right">
	<div class="box-dashboard"><div class="top-box-dashboard"><?php echo T_('System information :');?></div>
		<ul>
			<li><div id="BP_system_date"><?php echo T_('Date server :'); echo ' <strong>'. timeserver(0, '%A %d %B %Y - %H:%M:%S') .'</strong>';?></div></li>
			<li><div id="BP_system_os"><?php echo T_('Operating System :'); echo ' <strong>'. PHP_OS .'</strong>';?></div></li>
			<li><div id="BP_system_php"><?php echo T_('PHP version :'); echo ' <strong>'. $version_php .'</strong>';?></div></li>
			<li><div id="BP_system_mysql"><?php echo T_('MySQL version :'); echo ' <strong>'. $version_mysql .'</strong>';?></div></li>
			<li><div id="BP_system_apache"><?php echo T_('Server :');  echo ' <strong>'. $_SERVER['SERVER_SOFTWARE'] .'</strong>';?></div></li>
			<li><div id="BP_system_memory"><?php echo T_('Memory :');  echo ' <strong>'. @ini_get('memory_limit') .'</strong>';?></div></li>
			<li><div id="BP_system_bilboplanet"><?php echo T_('Your BilboPlanet version :'); echo ' <strong>'. $planet_version .'</strong>';?></div></li>
		</ul>
	</div>
<p class="clear"/>
<?php
echo showBlogLastArticles();
?>
		</div>
	<p class="clear"/>
	</div>
</fieldset>

<?php include(dirname(__FILE__).'/footer.php'); ?>
