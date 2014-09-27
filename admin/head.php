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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="ROBOTS" content="noindex, nofollow, noarchive" />
	<title><?php echo T_('Administration');?> - <?php echo $blog_settings->get('planet_title'); ?></title>
	<link rel="shortcut icon" type="image/png" href="./meta/icons/fire.png" />
    <link rel="stylesheet" type="text/css" href="meta/css/styles.css" media="all" />
<?php
if ($blog_settings->get('planet_rtl')) {
?>
    <link rel="stylesheet" type="text/css" href="meta/css/styles-rtl.css" media="all" />

<?php
}
?>
    <link rel="stylesheet" type="text/css" href="meta/css/boxy.css" media="all" />
	<script type="text/javascript" src="meta/js/ed.js"></script>
	<script type="text/javascript" src="meta/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="meta/js/jquery.boxy.js"></script>
	<script type="text/javascript" src="meta/js/jquery.bilboplanet.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".tips").tips({type:'rel', delay: 5});
		});
	</script>
</head>
<body class="admin">
<div id="BP_head" class="toolbar bgbox bdbox"><div class="grad bdinbox">
	<p class="site_info">
	<span class="ctitle"><a class="tips" title="<?php echo $blog_settings->get('planet_title'); ?>" rel="<?php echo T_('Back on the Bilboplanet');?>" href="<?php echo BP_PLANET_URL; ?>" target="_blank"><?php echo T_('Back on the Bilboplanet');?></a></span>
	</p>
	<ul id="BP_userbar">
		<li><a id="BP_Logout" href="#" rel="?logout=<?php echo BP_PLANET_URL; ?>" class="button minbutton br3px"><?php echo T_('Logout');?></a></li>
		<li><a id="BP_About" href="#about" name="modal" class="button minbutton br3px"><?php echo T_('About');?></a></li>
	</ul>
	<hr class="clear" />
	<!-- Fenêtre modal -->
	<div id="boxes">
		<div id="about" class="window">
			<div class="title"><?php echo T_('About');?></div>
			<p>
				<h3><?php echo T_('Bilboplanet was developed by');?></h3>
				<ul>
					<li>Gregoire de Hemptinne (<a href="http://www.theclimber.be" target="_blank">http://www.theclimber.be</a>)</li>
					<li>Thomas Bourcey (<a href="http://www.sckyzo.com" target="_blank">http://www.sckyzo.com</a>)</li>
					<li>Guillaume Oña (<a href="http://www.guiona.com" target="_blank">http://www.guiona.com</a>)</li>
				</ul>
			</p>
			<br />
			<h3><?php echo T_('BilboPlanet : Useful links');?></h3>
			<p>
				<ul>
					<li><?php echo T_('Official WebSite: ');?><a href="http://www.bilboplanet.com" target="_blank">http://www.bilboplanet.com</a></li>
					<li><?php echo T_('Official Forum: ');?><a href="http://www.bilboplanet.com/forum" target="_blank">http://www.bilboplanet.com/forum</a></li>
					<li><?php echo T_('Official Documentation: ');?><a href="http://wiki.bilboplanet.com" target="_blank">http://wiki.bilboplanet.com</a></li>
					<li><?php echo T_('Code and bug tracking: ');?><a href="https://github.com/theclimber/Bilboplanet" target="_blank">https://github.com/theclimber/Bilboplanet</a></li>
				</ul>
			</p>
			<br />
			<hr>
			<div class="buttons">
				<a href="#" class="button br3px close" /><?php echo T_('Close');?></a>
				<br />
				<a href="http://www.bilboplanet.com" target="_blank" rel="http://www.bilboplanet.com" class="tips">BilboPlanet.com</a> - Open Source Feed Agregator - 2012
			</div>
		</div>
		<div id="mask"></div>
	</div>
	<!--Fin fenêtre modal -->
</div></div>
