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
?>

	<h2><?=T_('Dashboard');?></h2>

	<p><?=T_('Voici un résumé de la situation');?></p>

	<div id="box-links"><?=T_('Derniers articles publiés :');?>
	<ul>
		<li><?=T_('lien 1');?></li>
		<li><?=T_('lien 1');?></li>
		<li><?=T_('lien 1');?></li>
		<li><?=T_('lien 1');?></li>
		<li><?=T_('lien 1');?></li>
	</ul>
</div>
<div id="box-stats"><?=T_('Statistiques des visites :');?>
	<div id="stat-graph">xxxxxxxxxxxxxxxxx</div>
	<ul>
	<li><?=T_('Etat du cron');?></li>
		<li><?=T_('Etat de la DB');?></li>
		<li><?=T_('Nombre d\'articles par jour');?></li>
		<li><?=T_('..?');?></li>
		<li><?=T_('lien 1');?></li>
	</ul>
</div>
<div id="box-news"><?=T_('News du bilboplanet (liens du blog du bilboplanet) :');?>
	<ul>
		<li><?=T_('lien 1');?></li>
		<li><?=T_('lien 1');?></li>
		<li><?=T_('lien 1');?></li>
		<li><?=T_('lien 1');?></li>
		<li><?=T_('lien 1');?></li>
	</ul>
</div>

<?php
include(dirname(__FILE__).'/footer.php');
?>
