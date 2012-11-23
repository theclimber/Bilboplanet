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
/* Inclusion du fichier de configuration */
require_once(dirname(__FILE__).'/../inc/admin/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}

require_once(dirname(__FILE__).'/../inc/lib/simplepie_1.3.compiled.php');
function showArticleSummary($con){
	global $core;
	$content = '<div class="box-dashboard"><div class="top-box-dashboard">'.T_('Latest articles :').'</div>';
	$nb_articles=0;
	#connectBD();
	/* On recupere les infomations des articles */
	$sql = "SELECT
			user_fullname as fullname,
			post_pubdate as pubdate,
			post_title as title,
			post_permalink as permalink
		FROM ".$core->prefix."post, ".$core->prefix."user
		WHERE ".$core->prefix."post.user_id = ".$core->prefix."user.user_id
		AND post_status = '1'
		AND user_status = '1'
		ORDER BY pubdate DESC
		LIMIT 5";
	$rs = $con->select($sql);
	$list_articles = "<ul>";
	$max_title_length = 50;
	while($rs->fetch()){
		$nb_articles++;
		# Formatage de la date
		$date = mysqldatetime_to_date("d/m/Y",$rs->pubdate);
		# Affichage du lien
		$titre = html_entity_decode($rs->title, ENT_QUOTES, 'UTF-8');
		if (strlen($titre) > $max_title_length)
			$show = substr($titre,0, $max_title_length)."...";
		else
			$show = $titre;
		$list_articles .= '<li>'.$date.' : <a class="tips" href="'.$rs->permalink.'" rel="<b><u>'.T_('User').':</u></b> '.$rs->fullname.' <br><b><u>'.T_('Title').':</u></b> '.$titre.'" target="_blank">'.$show.'</a></li>';
	}
	$list_articles .= "</ul>";
	#closeBD();
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
	$feed->set_feed_url(array('http://bilboplanet.com/feed/'));
	$feed->set_cache_duration (600);
#	$feed->enable_xml_dump(isset($_GET['xmldump']) ? true : false);
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
			$content .= '<li>'.$item->get_date('j/m/y').' : ';
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


$rs = $core->con->select("SELECT COUNT(1) as nb FROM ".$core->prefix."post");
$nb_posts = $rs->nb;

$rs = $core->con->select("SELECT COUNT(1) as nb FROM ".$core->prefix."votes");
$nb_votes = $rs->nb;

$rs = $core->con->select("SELECT COUNT(1) as nb FROM ".$core->prefix."user");
$nb_users = $rs->nb;

$rs = $core->con->select("SELECT COUNT(1) as nb FROM ".$core->prefix."feed");
$nb_feeds = $rs->nb;

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');

# Version SQL
$version_sql = $core->con->version();
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

<?php
if (file_exists(dirname(__FILE__).'/install')) {
	echo '<div class="message_install"><p>'.T_('WARNING : please remove /admin/install folder for maximum security').'</p></div>';
}
?>

<div id="dashboard">
	<div class="box-container-left">
<?php
echo showArticleSummary($core->con);
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
if ($blog_settings->get('planet_index_update'))
	echo '<li><div id="BP_index_update">'.T_('The update on loading of index page enabled').'</div></li>';
?>
			<li><div id="BP_stats_db"><?php echo T_('Current size of the database :'); echo ' <strong>'.formatfilesize(get_database_size()).'</strong>';?></div></li>
			<li><div id="BP_nb_articles"><?php echo T_('Number of articles in the DB :'); echo ' <strong>'.$nb_posts.'</strong>';?></div></li>
			<li><div id="BP_nb_votes"><?php echo T_('Number of votes in the DB :'); echo ' <strong>'.$nb_votes.'</strong>';?></div></li>
			<li><div id="BP_nb_members"><?php echo T_('Number of members in the DB :'); echo ' <strong>'.$nb_users.'</strong>';?></div></li>
			<li><div id="BP_nb_feeds"><?php echo T_('Number of feeds in the DB :'); echo ' <strong>'.$nb_feeds.'</strong>';?></div></li>
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
			<li><div id="BP_system_mysql"><?php echo T_('Database version :'); echo ' <strong>'. $version_sql .'</strong>';?></div></li>
			<li><div id="BP_system_apache"><?php echo T_('Server :');  echo ' <strong>'. $_SERVER['SERVER_SOFTWARE'] .'</strong>';?></div></li>
			<li><div id="BP_system_memory"><?php echo T_('Memory :');  echo ' <strong>'. @ini_get('memory_limit') .'</strong>';?></div></li>
			<li><div id="BP_system_bilboplanet"><?php echo T_('Your BilboPlanet version :'); echo ' <strong>'. $blog_settings->get('planet_version') .'</strong>';?></div></li>
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

<?php
include(dirname(__FILE__).'/footer.php');
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
