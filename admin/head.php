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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/i18n.php');
require_once(dirname(__FILE__).'/../inc/fonctions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?php
if (isset($_GET) && isset($_GET['reload'])){
	echo '<META HTTP-EQUIV="Refresh" CONTENT="300">';
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=T_('Administration');?> - <?php echo $planet_title ?></title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/style_admin.css" rel="stylesheet" type="text/css" />
<link rel="icon" type="image/ico" href="../favicon.ico" />
</head>
<body>
<a href="http://redmine.bilboplanet.org" target=_blank title="<?=T_('Report a bug');?>"><span id="bug"></span></a>
<div id="tour">
<div id="arriere_plan">
	<div id="global">
	<div id="header">
	<div id="title">
<?php
echo T_('Administration').' - <a href="'.$planet_url.'">'.$planet_title.'</a>';
?>
	<div id="description_title">
<?php 
echo '<a href="'.$planet_url.'">'.$planet_desc.'</a>';
?>
	</div>
	</div>
		<ul id="menu">
			<li class="firstLi"><a href="index.php" class="a_header"><?=T_('Dashboard');?></a></li>
			<li><a href="gestion-membre.php" class="a_header"><?=T_('Users');?></a></li>
			<li><a href="gestion-flux.php" class="a_header"><?=T_('Feeds');?></a></li>
			<li><a href="gestion-articles.php" class="a_header"><?=T_('Posts');?></a></li>
			<li><a href="gestion-mysql.php" class="a_header"><?=T_('Backup');?></a></li>
			<li><a href="gestion-logs.php" class="a_header"><?=T_('Log files');?></a></li>
			<li><a href="gestion-update.php" class="a_header"><?=T_('Update');?></a></li>
			<li><a href="gestion-cache.php" class="a_header"><?=T_('Clean the cache');?></a></li>
			<li><a href="gestion-option.php" class="a_header"><?=T_('Options');?></a></li>
			<li><a href="<?php echo $planet_url; ?>" class="a_logout"><?=T_('Go to planet');?></a></li>
		</ul>
	</div>
<div id="centre">
<div id="centre_admin">

