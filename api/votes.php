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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/prepend.php');

if ($core->auth->sessionExists() && isset($_POST) && isset($_POST['num_article']) && is_numeric(trim($_POST['num_article']))
	&& isset($_POST['token']) && isset($_POST['type']) ) {

	# On recuepre la valeur du post
	$num_article = trim($_POST['num_article']);
	$token = trim($_POST['token']);
	$type = trim($_POST['type']);
	$value = $type=="positif" ? 1 : -1;

	# On recupere l'adresse ip
	$ip = getIP();

	# On reconstruit le token
	$hash = md5($ip.$num_article);

	# On met a jour le score si l'ip n'a pas vote et si le token est bon
	if(!checkVote($core->con, $ip, $num_article)) {

		$user_id = $core->auth->userID();
		# Verification du numero de l'article
		$sql = "SELECT post_score from ".$core->prefix."post WHERE post_id = $num_article";
		$rs = $core->con->select($sql);
		$nb_art = $rs->count();
		$post_current_score = $rs->f('post_score');

		#Verification s'il a deja vote
		$sql = "SELECT COUNT(*) as nb from ".$core->prefix."votes WHERE post_id = $num_article AND vote_ip = '$ip' AND user_id='".$user_id."'";
		$rs = $core->con->select($sql);
		$nb_vote = $rs->f('nb');

		if($nb_art>0 && $nb_vote==0) {
			# Ajout de l'ip a la liste
			# FIXME : we have to handle the vote with a user logged in !!! Instead of admin.rand()
			$cur = $core->con->openCursor($core->prefix.'votes');
			$cur->post_id = $num_article;
			$cur->user_id = $user_id; //'admin'.rand();
			$cur->vote_ip = $ip;
			$cur->vote_value = $value;
			$cur->created = array( 'NOW()' );
			$cur->insert();

			# Mise a jour du score
			$cur = $core->con->openCursor($core->prefix.'post');
			$cur->post_score = $post_current_score + $value;
			$cur->update("WHERE post_id = '$num_article'");
			echo T_("Success");
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
	echo 'needlogin';
}

?>
