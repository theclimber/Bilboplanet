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
require_once(dirname(__FILE__).'/inc/i18n.php');
require_once(dirname(__FILE__).'/inc/fonctions.php');
include(dirname(__FILE__).'/head.php');
?>
<script type="text/javascript" src="javascript/show-hide.js"></script>
<div id="centre">

<?php
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="centre_centre">
<div id="template">
<div id="archive_page">
<?php

# On active le cache
debutCache();

# Connexion a la base de donnees
connectBD();

# On recupere les infomations des articles
$sql = "SELECT nom_membre, article_pub, article_titre, article_url, site_membre
        FROM article, membre 
        WHERE article.num_membre =  membre.num_membre
        AND article_statut = '1'
        ORDER BY article_pub DESC";
$rqt = mysql_query(trim($sql)) or die("Error with request $sql");

# On recupere le mois en cours
$mois_en_cours = date("n");

?>

<h3><?php echo convertMois($mois_en_cours)." ".date("Y"); ?></h3>

<?php

/* Boucle d'affichage des archives du mois */
while ($liste = mysql_fetch_row($rqt)) {
	/* Si le mois de l'article est different du mois en cours */
	if (($mois = date('n', $liste[1])) != $mois_en_cours) {
		$mois_en_cours = $mois;
		echo '<h3>'.convertMois($mois).' '.date("Y", $liste[1]).'</h3>';
	}
	echo '<a href="'.$liste[3].'" title="'.T_("Read the article").'">'.$liste[0].' : '.htmlspecialchars_decode($liste[2]).'</a>';
}
?>
</div>
</div>
<?php include(dirname(__FILE__).'/footer.php'); ?>
<?php 
# Fermeture de la base
closeBD();

# On termine le cache
finCache();


# Fonction convertion de mois (= date("n")) en fr
function convertMois($num_mois) {

	$month = array(1 => T_("January"), T_("February"), T_("March"), T_("April"), T_("May"), T_("June"), T_("July"), T_("August"), T_("September"), T_("October"), T_("November"), T_("December"));
	return $month[$num_mois];
}

?>
