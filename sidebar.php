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
<!-- SIDEBAR -->
 <div id="colonne">
<?php
$message_information=BP_MSG_INFO;
if(!empty($message_information)) {
	echo '<div id="nouvelles">';
	echo '<div id="nouvelles_head"><h2 id="informations_title">'.T_('Quick news').'</h2></div>';
	echo "<div id=\"nouvelles_center\">$message_information</div>";
	echo '<div id="nouvelles_footer"></div>';
	echo '</div>';
}
?>

	<h2 id="abonnements"><?php echo T_('Subscribe');?></h2>
		<ul>
			<li><img src="themes/<?php echo $planet_theme; ?>/images/ico-feed.gif" alt="feed" />&nbsp;<a href="feed.php?type=rss" title="<?php echo T_('Subscribe to RSS feed');?>" rel="nofollow"><?=T_('Feed with all the posts');?></a></li>
<?php
if ($activate_votes)
	echo ' <li><img src="themes/'.$planet_theme.'/images/ico-feed.gif" alt="feed" />&nbsp;<a href="feed.php?type=rss&popular=true" title="'.T_('Subscribe to all the popular posts RSS feed').'" rel="nofollow">'.T_('Popular posts feed').'</a></li>';
?>
		</ul>
		
	<h2 id="membres"><?php echo T_('Members');?></h2>
		<ul>
			<?php

			connectBD();
			/* On recupere les infomations sur les membres et leurs flux */
			$sql_side = "SELECT nom_membre, site_membre, num_membre FROM membre WHERE statut_membre = '1' ORDER BY nom_membre ASC";
			$rqt_side = mysql_query(trim($sql_side)) or die("Error with request $sql_side");

			while ($liste = mysql_fetch_row($rqt_side)) {
				/* On affiche le nom du membre */
				echo '<li><a href="'.$planet_url.'/?num_membre='.$liste[2].'" title="'.T_('See members posts').'">
					<img src="themes/'.$planet_theme.'/images/ico-external.gif" alt="feed" /></a>&nbsp;
				<a href="'.$liste[1].'" title="'.sprintf(T_('Visit the website of %s'),$liste[0]).'">'.$liste[0].'</a></li>';
			}
			closeBD();
			?>
		</ul>
		
	<h2 id="participer"><?php echo T_('Contribute');?></h2>
		<ul>
			<li><img src="themes/<?php echo $planet_theme; ?>/images/ico-meta.gif" alt="meta" />&nbsp;<a href="inscription.php" title="<?php echo T_('Subscribe your blog to the planet');?>" rel="nofollow"><?=T_('Add your blog');?></a></li>
			<li><img src="themes/<?php echo $planet_theme; ?>/images/ico-meta.gif" alt="meta" />&nbsp;<a href="<?php echo $planet_url; ?>/admin" title="<?php echo T_('Admin interface');?>" rel="nofollow"><?=T_('Administration');?></a></li>
		</ul>

</div>
<!-- SIDEBAR -->
