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
# Force no-cache
header("Expires : 0");

global $recherche;

# Si on est en mode maintenance
if($maintenance) {
	echo "<html><body><br/><br/><br/><br/><br/><br/><br/>"."\n";
	echo "<center><img src=\"/bilboplanet/themes/$planet_theme/images/logo.png\" alt=\"\" class=\"logo\"/>"."\n";
	echo "<h3>".T_("Site down for maintenance, come back in a few moments...")."</h3>"."\n";
	echo sprintf(T_('While waiting you can vist the website of the <a href="%s">author</a>'),$planet_author_site)."\n";
	echo "</center></body></html>";
	exit(0);
}
global $show_contact, $activate_votes;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="<?php echo $planet_desc_meta; ?>" />
<meta name="keywords" content="<?php echo $planet_keywords; ?>" />
<link href="themes/<?php echo $planet_theme; ?>/style.css" rel="stylesheet" type="text/css" />
<link href="javascript/fancybox/fancybox.css" rel="stylesheet" type="text/css" />
<link rel="alternate" type="application/rss+xml"  title="RSS"  href="feed.php?type=rss" />
<link rel="alternate" type="application/atom+xml" title="ATOM" href="feed.php?type=atom" />
<link rel="icon" type="image/ico" href="./favicon.png" />
<title><?php echo $planet_title; ?></title>
<script type="text/javascript" src="javascript/jquery.js"></script>
<script type="text/javascript" src="javascript/votes.js" ></script>
<script type="text/javascript" src="javascript/mobile.js" ></script>
<script type="text/javascript" src="javascript/jquery.easing.1.3.js" ></script>
<script type="text/javascript" src="javascript/jquery.fancybox-1.2.1.pack.js" ></script>
<script type="text/javascript" src="javascript/smothscroll.js" ></script>
</head>
<body>
<div id="tour">
<div id="arriere_plan">
  <div id="global">

	<div id="header">
	<div id="title"><?php 
echo "<a href=\"$planet_url\" name=\"top\">$planet_title</a>";
?>
	<div id="description_title"><?php 
echo "<a href=\"$planet_url\">$planet_desc</a>";
?></div>
	</div>
		<ul id="menu">
		<li class="firstLi"><a href="index.php" class="a_header"><?php echo T_('Home');?></a></li>
			<?php
			if ($activate_votes) {
				echo '<li><a href="index.php?populaires=week" class="a_header">'.T_('Top 10').'</a>';
			}
			?>
			
		<li><a href="stats.php" class="a_header"><?php echo T_('Statistics');?></a>
		<li><a href="inscription.php" class="a_header"><?php echo T_('Registration');?></a>
			<?php
			if ($show_contact){
				echo '<li><a href="archives.php" class="a_header">'.T_('Archives').'</a>';
				echo '<li class="preLastLi"><a href="contact.php" class="a_header">'.T_('Contact').'</a>';
			}
			else echo '<li class="preLastLi"><a href="archives.php" class="a_header">'.T_('Archives').'</a>';
			?>
			<li class="lastLi">
				<form id="recherche_global" action="index.php" method="get">
	<?php 
	if (isset($_GET) && isset($_GET['tri']) && !empty($_GET['tri'])){
		echo '<input type="hidden" id="tri" name="tri" value="'.$_GET['tri'].'" />';
	}
	if (isset($_GET) && isset($_GET['num_membre']) && !empty($_GET['num_membre'])){
		echo '<input type="hidden" id="num_membre" name="num_membre" value="'.$_GET['num_membre'].'" />';
	}
	if (isset($_GET) && isset($_GET['populaires']) && !empty($_GET['populaires'])){
		echo '<input type="hidden" id="populaires" name="populaires" value="'.$_GET['populaires'].'" />';
	}
	?>
					<fieldset>
						<input type="text" id="recherche" name="search" value="<?php echo $recherche; ?>" /><input type="submit" id="recherche_global_btn" value="" />
					</fieldset>
				</form>
			</li>
        </ul>

