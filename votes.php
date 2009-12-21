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

# Inclusion des fonctions
require_once(dirname(__FILE__).'/inc/i18n.php');
require_once(dirname(__FILE__).'/inc/fonctions.php');

# Connexion a la base de donnees
connectBD();

# Verification du contenu envoye
if ( isset($_POST) && isset($_POST['num_article']) && is_numeric(trim($_POST['num_article'])) 
	&& isset($_POST['token']) && isset($_POST['type']) ) {

		# On recuepre la valeur du post
		$num_article = trim($_POST['num_article']);
		$token = trim($_POST['token']);
		$type = trim($_POST['type']);

		# On recupere l'adresse ip
		$ip = getIP();

		# On reconstruit le token
		$hash = md5($ip.$num_article);

		# On met a jour le score si l'ip n'a pas vote et si le token est bon
		if(!checkVote($ip, $num_article) && ($token == $hash)) {

			# Verification du numero de l'article
			$sql = "SELECT num_article from article WHERE num_article = $num_article";
			$result = mysql_query($sql) or die("Error with request $sql");
			$nb_art = mysql_num_rows($result);
			
			#Verification s'il a deja vote
			$sql = "SELECT * from votes WHERE num_article = $num_article AND vote_ip = '$ip'";
			$result = mysql_query($sql) or die("Error with request $sql");
			$nb_vote = mysql_num_rows($result);

			if($nb_art>0 && $nb_vote==0) {

				# Ajout de l'ip a la liste
				$sql = "INSERT INTO votes VALUES ('$num_article', '$ip')";
				$result = mysql_query($sql) or die("Error with request $sql");

				# Mise a jour du score 
				if ($type == "positif") {
					# Si c'est un vote positif
					$sql = "UPDATE article SET article_score = article_score + 1 WHERE num_article = '$num_article'";
				} else {
					# Si c'est un vote negatif
					$sql = "UPDATE article SET article_score = article_score - 1 WHERE num_article = '$num_article'";
				}
				$result = mysql_query($sql) or die("Error with request $sql");

				if($result) {

					# Tout st ok
					echo T_("success");

				} else {

					# On indique qu'il y a un probleme
					echo T_("Error: the vote could not be saved");
				}

			} else {

				# On indique qu'il y a un probleme
				echo T_("Error: article number incorrect !");
			}

		} else {

			# On indique que le vote a deja eu lieu
			echo T_("Error: you already vote on this post !");
		}

	} else {

		# On previent que la tentative a echouee
		echo T_('Error: refused access');
	}

# Fermeture de la base de donnees
closeBD();
?>
