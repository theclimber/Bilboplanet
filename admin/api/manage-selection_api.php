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
?><?
if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# GET POSTS LIST
##########################################################
	case 'filter':
		$nb_items = !empty($_POST['nb_items']) ? $_POST['nb_items'] : null;
		$user_filter = !empty($_POST['filtre_membre']) ? $_POST['filtre_membre'] : 0;
		$user_id = $_POST['user_id'];
		echo publishedArticles($user_id, $nb_items, $user_filter);
		break;

##########################################################
# GET SELECTED POSTS
##########################################################
	case 'selected':
		$user_id = $_POST['user_id'];
		echo selectedArticles($user_id);
		break;

##########################################################
# SELECT POST
##########################################################
	case 'select':
		$post_id = $_POST['post_id'];
		$user_id = $_POST['user_id'];
		$cotation = array(
			'style' => $_POST['style'],
			'contenu' => $_POST['contenu'],
			'recherche' => $_POST['recherche']);

		$next = strtotime('next Sunday', time());
		$nextSunday = mktime(8,0,0,date('m',$next),date('d',$next),date('Y',$next));

		$sql = "SELECT COUNT(id) FROM article_pool
			WHERE post_id = ".$post_id."
			AND user_id = '".$user_id."'";
		$request = mysql_query($sql);
		$count = mysql_fetch_row($request);
		if ($count[0] > 0){
			echo "Error : you can not vote twice";
		}
		else {
			$sql = "INSERT INTO article_pool (user_id, article_id, period, cotation, status)
				VALUES ('".$user_id."', '".$post_id."','".$nextSunday."', '".json_encode($cotation)."', 'selected')";
			$request = mysql_query($sql);
			if (!$request){
				echo "MySQL error : ".mysql_error();
				exit;
			}
			echo "Done";
		}
		break;

	default:
		print '<div class="flash error">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}


function publishedArticles($current_user_id, $count = 0, $filtre_membre = 0){
	if ($count == 0){
		$head = "Derniers articles publiés";
		$limit = '';
	} else {
		$head = $count." derniers articles publiés";
		$limit = ' LIMIT '.$count.' ';
	}
	$filtre = ($filtre_membre==0) ? '' : ' AND article.num_membre = '.$filtre_membre.' ';

	$lastSunday = mktime(8,0,0,date('m',strtotime('last Sunday')),date('d',strtotime('last Sunday')),date('Y',strtotime('last Sunday')));
	
	$list_articles = '<table id="list-articles">';
	$list_articles .= '<tr class="title">';
	$list_articles .= '<td>Date</td>';
	$list_articles .= '<td>Auteur</td>';
	$list_articles .= '<td>Titre</td>';
	$list_articles .='<td>Action</td></tr>';

	connectBD();
	/* On recupere les infomations des articles */
	$sql = "SELECT nom_membre, article_pub, article_titre, article_url, article_statut, num_article
			FROM article, membre
			WHERE article.num_membre = membre.num_membre
			AND statut_membre = '1'
			AND article_statut = '1'
			AND article_pub > '".$lastSunday." '".$filtre."
			ORDER BY article_pub DESC ".$limit;
	$request = mysql_query($sql) or die("Error with request $sql");
	$nb_articles=0;
	while($article = mysql_fetch_row($request)){
		$nb_articles++;
		$article_id = $article[5];
		$article_author = $article[0];
		$article_date = date("d/m/Y",$article[1]);
		$article_titre = decode_strip($article[2], 100);
		$article_titre = '<a href="'.$article[3].'" target="_blank">'.$article_titre.'</a>';
		
		$sql = "SELECT COUNT(*) FROM article_pool
			WHERE article_id = ".$article[5]."
			AND user_id = ".$current_user_id;
		$res = mysql_query($sql);
		$count_selected = mysql_fetch_row($res);
		if ($count_selected[0] > 0){
			$status = 'selected';
			$action = '<img src="images/like-light.png" title="Sélectionner cet article" />';
		}
		else {
			$status = 'unselected';
			$action = '<a href="javascript:select('.$article[5].')"><img src="images/like.png" title="Sélectionner cet article" /></a>';
		}

		$list_articles .= '<tr id="line'.$article_id.'" class="'.$status.'">';
		$list_articles .= '<td>'.$article_date.'</td>';
		$list_articles .= '<td><b>'.$article_author.'</b></td>';
		$list_articles .= '<td>'.$article_titre.'</td>';
		$list_articles .= '<td><span id="action'.$article_id.'">'.$action.'</span></td></tr>';
	}
	$list_articles .= "</table>";
	$content = '<h3>'.$head.' depuis le '.date('d-m-Y à G:i:s',$lastSunday).' :</h3>';
	$content .= $list_articles;

	closeBD();
	if ($nb_articles==0){
		return $content.'<p>Aucun article trouvé</p>';
	}
	return $content;
}

function selectedArticles($current_user_id, $current_time = null){
	if (!isset($current_time)) $current_time = time();
	$last = strtotime('last Sunday', $current_time);
	$next = strtotime('next Sunday', $current_time);
	$lastSunday = mktime(8,0,0,date('m',$last),date('d',$last),date('Y',$last));
	$nextSunday = mktime(8,0,0,date('m',$next),date('d',$next),date('Y',$next));

	$list_articles = '<table id="list-articles">';
	$list_articles .= '<tr class="title"><td>Date</td><td>Auteur</td><td>Titre</td><td>Cotation</td><td>Action</td><td>Intervenants</td></tr>';

	connectBD();
	/* On recupere les infomations des articles */
	$sql = "SELECT DISTINCT (num_article), nom_membre, article_titre, article_url, article_pub
		FROM article, membre, article_pool
		WHERE article.num_membre = membre.num_membre
		AND article_pool.article_id = article.num_article
		AND article_pool.period = ".$nextSunday."
		ORDER BY article_pub DESC";
	$request = mysql_query($sql) or die("Error with request $sql");
	$nb_articles=0;
	while($article = mysql_fetch_row($request)){
		$nb_articles++;
		$article_id = $article[0];
		$article_author = $article[1];
		$article_date = date("d/m/Y",$article[4]);
		$article_titre = decode_strip($article[2], 100);
		$article_titre = '<a href="'.$article[3].'" target="_blank">'.$article_titre.'</a>';
		
		$sql = "SELECT user_id, login, cotation
			FROM article_pool, user
			WHERE article_pool.user_id = user.id
			AND article_id = ".$article_id;
		$res = mysql_query($sql);
		$users = '<ul class="userlist">';
		$status = 'unselected';
		$cotation = array('nb_votes' => 0, 'style' => 0, 'recherche' => 0, 'contenu' => 0);
		while ($vote = mysql_fetch_row($res)){
			$n++;
			if ($vote[0] == $current_user_id)
				$status = 'selected';
			$users .= '<li>'.$vote[1]."</li>";
			$user_vote = json_decode($vote[2]);
			$cotation['nb_votes'] += 1;
			$cotation['style'] += $user_vote->{'style'};
			$cotation['contenu'] += $user_vote->{'contenu'};
			$cotation['recherche'] += $user_vote->{'recherche'};
		}
		$users .= '</ul>';
		$cotation = compute_cotation($cotation);

		if ($status == 'selected')
			$action = '<img src="images/like-light.png" title="Sélectionner cet article" />';
		else
			$action = '<a href="javascript:select('.$article_id.')"><img src="images/like.png" title="Sélectionner cet article" /></a>';

		$list_articles .= '<tr id="line'.$article_id.'" class="'.$status.'">';
		$list_articles .= '<td>'.$article_date.'</td>';
		$list_articles .= '<td><b>'.$article_author.'</b></td>';
		$list_articles .= '<td>'.$article_titre.'</td>';
		$list_articles .= '<td>'.$cotation.'</td>';
		$list_articles .= '<td><span id="action'.$article_id.'">'.$action.'</span></td>';
		$list_articles .= '<td>'.$users.'</td></tr>';
	}
	$list_articles .= "</table>";
	$content = '<h3>Articles déjà selectionnés</h3>';
	$content .= $list_articles;

	closeBD();
	if ($nb_articles==0){
		return $content.'<p>Aucun article trouvé</p>';
	}
	return $content;
}

function compute_cotation($cot){
	$nb = $cot['nb_votes'];
	
	if ($nb > 0){
		$cotation = array(
			'style' => $cot['style']/$nb,
			'contenu' => $cot['contenu']/$nb,
			'recherche' => $cot['recherche']/$nb
		);
		return json_encode($cotation);
	}
	return 0;
}

?>
