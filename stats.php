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
require_once(dirname(__FILE__).'/inc/i18n.php');
require_once(dirname(__FILE__).'/inc/fonctions.php');
include(dirname(__FILE__).'/head.php');

# On active le cache
debutCache();

# Connexion a la base de donnees

?>
<div id="centre">
<?php
include_once(dirname(__FILE__).'/sidebar.php');
connectBD();
$n_articles = getNbArticles();
$n_votes = getNbVotes();
?>

<div id="centre_centre">
<div id="template">

<h2><?=T_('Main statistics');?></h2>
<ul>
<li><?php echo T_('Number of members : ').getNbMembres(); ?></li>
<li><?php echo T_('Number of feeds : ').getNbFlux(); ?></li>
<li><?php echo sprintf(T_('%d posts in here'),$n_articles); ?></li>
<li><?php echo sprintf(T_('%d votes in total'),$n_votes);?></li>
</ul>
<?php

# Nombre de ligne a afficher
$nb = 15;

echo "<br/><br/><h2>".sprintf(T_("List of the %d most active members"),$nb)."</h2>";
echo "<table><tr class='table_th'><th>".T_("Name")."</th><th>".T_("Website")."</th><th>".T_("Qtity of posts")."</th></tr>";

# On recupere la liste et on affiche
$tab = getTopMembreArticles($nb);
while ($liste = mysql_fetch_row($tab))
	echo '<tr><td>'.$liste[0].'</td><td><a href="'.$liste[1].'" title="'.T_('Visit the website').'">
		'.domaine($liste[1]).'</a></td><td>'.$liste[2].'</td></tr>';
echo "</table>";

if ($activate_votes) {
	echo "<br/><br/><h2>".sprintf(T_("List of the %d best ranked members"),$nb)."</h2>";
	echo "<table><tr class='table_th'><th>".T_('Name')."</th><th>".T_("Website")."</th><th>".T_('Total of votes')."</th></tr>";

	# On recupere la liste et on affiche
	$tab = getTopMembreVotes($nb);
	while ($liste = mysql_fetch_row($tab))
		echo '<tr><td>'.$liste[0].'</td><td><a href="'.$liste[1].'" title="'.T_('Visit the website').'">
			'.domaine($liste[1]).'</a></td><td>'.$liste[2].'</td></tr>';
	echo "</table>";
}
?>
</div>
<?php 
closeBD();
include(dirname(__FILE__).'/footer.php');

# Fermeture de la base de donnees

# On termine le cache
finCache();
?>
