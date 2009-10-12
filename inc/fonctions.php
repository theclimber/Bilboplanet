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

require_once(dirname(__FILE__).'/i18n.php');
require_once(dirname(__FILE__).'/database.php');

# Fonction qui retourne le nom de domaine en fonction de l'url
function domaine($referer) {

	# On recupere l'adresse sans le protocole
	$referer_light = substr(strstr($referer, "://"), 3);

	# Si il n' y a pas d'url, on retourne rien
	if (empty($referer_light)) return "";

	# On recharche si il y a une / dans l'url et si oui on retourne l'adresse jusqu'a ce caractere
	if (($qm = strpos($referer_light, "/")) !== false) $referer_light = substr($referer_light, 0, $qm);

	# On retourne le resultat
	return $referer_light;
}


#----------------------------------#
#   Fonction de gestion du cache   #
#----------------------------------#

# Procedure de debut de cache des pages
function debutCache() {

	global $secondes_cache, $log;

	# Construction du nom du fichier servant de cache
	$url_cache = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$fichier_cache = dirname(__FILE__).'/cache/'.md5($url_cache).'.cache';

	# Si le fichier de cache existe
	if (file_exists($fichier_cache)) {
		if ($log == "debug") echo T_("Reading cache");
		@readfile($fichier_cache);
		exit();
	}

	# Sinon on regenere le cache
	ob_start();
}

# Procedure de fin de cache
function finCache() {

	# Construction du nom du fichier servant de cache
	$url_cache = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$fichier_cache = dirname(__FILE__).'/cache/'.md5($url_cache).'.cache';

	# On ecrit le contenu de la page
	$pointeur = @fopen($fichier_cache, 'w');
	@fwrite($pointeur, ob_get_contents());
	@fclose($pointeur);
	ob_end_flush();
}


#------------------------------#
#   Fonction de statistiques   #
#------------------------------#

# Fonction qui retourne le nombre de membres
function getNbMembres() {

	# Requete sql
	$sql = 'SELECT COUNT(*) FROM membre WHERE statut_membre = 1';
	$rqt = mysql_query($sql) or die("Error with request $sql");
	$nb = mysql_fetch_row($rqt);

	# On retourne le resultat
	return $nb[0];
}

# Fonction qui retourne le nombre de flux
function getNbFlux() {

	# Requete sql
	$sql = 'SELECT COUNT(*) FROM flux, membre WHERE membre.num_membre = flux.num_membre AND statut_membre = 1';
	$rqt = mysql_query($sql) or die("Error with request $sql");
	$nb = mysql_fetch_row($rqt);

	# On retourne le resultat
	return $nb[0];
}

# Fonction qui retourne le nombre d'articles
function getNbArticles() {

	# Requete sql
	$sql = 'SELECT COUNT(*) FROM article WHERE article_statut = 1';
	$rqt = mysql_query($sql) or die("Error with request $sql");
	$nb = mysql_fetch_row($rqt);

	# On retourne le resultat
	return $nb[0];
}

# Fonction qui retourne le nombre de votes
function getNbVotes() {

	# Requete sql
	$sql = 'SELECT COUNT(*) FROM votes';
	$rqt = mysql_query($sql) or die("Error with request $sql");
	$nb = mysql_fetch_row($rqt);

	# On retourne le resultat
	return $nb[0];
}

# Fonction qui retourne la liste des nb membres les plus actifs
function getTopMembreArticles($nb) {

	# Requete sql
	$sql = "SELECT nom_membre, site_membre, COUNT(num_article) AS nb_article 
		FROM article, membre 
		WHERE article.num_membre = membre.num_membre
		AND statut_membre = 1
		GROUP BY nom_membre
		ORDER BY nb_article DESC
		LIMIT 0,$nb";
	$rqt = mysql_query($sql) or die("Error with request $sql");

	# On retourne la liste
	return $rqt;
}

# Fonction qui retourne la liste des nb membres qui ont le plu de points
function getTopMembreVotes($nb) {

	# Requete sql
	$sql = "SELECT nom_membre, site_membre, SUM(article_score) AS score
		FROM article, membre 
		WHERE article.num_membre = membre.num_membre 
		AND statut_membre = 1
		GROUP BY nom_membre
		ORDER BY score DESC
		LIMIT 0,$nb";
	$rqt = mysql_query($sql) or die("Error with request $sql");

	# On retourne la liste
	return $rqt;
}


#-----------------------#
#   Fonction de votes   #
#-----------------------#

# Fonction qui recupere l'adresse IP meme a travers un proxy
# Provenance : http://devloop.lyua.org/blog/index.php?2007/01/07/376-detection-de-proxy-en-php
function getIP() {

	# Si le visiteur passe par un proxy
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']!="") {

		if(strchr($_SERVER['HTTP_X_FORWARDED_FOR'],',')) {

			$tab = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
			$proxy = trim($tab[count($tab)-1]);
			$realip = trim($tab[0]);

		} else {

			$realip = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
			$proxy = $_SERVER['REMOTE_ADDR'];
		}

		if(ip2long($realip)===FALSE) $realip = $_SERVER['REMOTE_ADDR'];

		# Sinon si il se connecte en direct
	} else {

		$realip = $_SERVER['REMOTE_ADDR'];
		$proxy = "";
	}

	if($realip == $proxy) $proxy = "";

	# on retoune l'ip
	return $realip;
}

# Fonction qui verifie si une ip a votee sur un article
function checkVote($ip, $num_article) {

	# Recuperation des adresses IP qui ont votes
	$sql = "SELECT vote_ip FROM votes WHERE num_article = '$num_article' AND vote_ip = '$ip'";
	$rqt_article = mysql_query($sql) or die("Error with request $sql");
	$nb_vote = mysql_num_rows($rqt_article);

	if ($nb_vote==0){
		return 0;
	}
	else {
		return 1;
	}
}

# Fonciton qui retourn 0 ou un nombre positif
function positif($val) {
	if($val < 0) {
		$result = 0;
	} else {
		$result = $val;
	}
	return $result;
}

# Fonction qui converti les code de carateres html special iso en code utf8 html
function convert_iso_special_html_char($string) {

	$search = array('&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&');
	$replace = array('&#39;', '&#39;', '&quot;', '&quot;', '&amp;');
	return str_replace($search, $replace, $string);
}


#--------------------------#
#   Fonction d'affichage   #
#--------------------------#

# Fonction qui affiche les articles en fonction d'une requete (possibilite d'utiliser la fonction strip_tag, valeur 0 ou 1)
function afficheListeArticles($sql, $strip_tags, $recherche="") {

	# Recuperation des options de configuration
	global $planet_theme, $planet_password, $planet_url, $planet_avatar, $planet_votes_system, $activate_votes;

	# Execution de la rqt sql
	$liste_articles = mysql_query(trim($sql)) or die("Error with request $sql");

	# On recupere le nombre de resultat
	$nb = mysql_num_rows($liste_articles);

	# Si il n'y a pas de resultat
	if(!$nb) {
		echo '<div class="post">'.T_('No posts found').'</div>';

		# Sinon si il y a des resultats
	} else {

		# Boucle d'affichage des articles
		$cpt = 1;
		while ($liste = mysql_fetch_row($liste_articles)) {

			# Formatage de la date et heure
			$date = date("d/m/Y",$liste[1]);
			$heure = date("H:i",$liste[1]);
			$relevance = "";
			$article_url = $liste[3];
			$article_titre = html_entity_decode($liste[2], ENT_QUOTES, 'UTF-8');
			$article_contenu = html_entity_decode($liste[4], ENT_QUOTES, 'UTF-8');
			$membre_id = $liste[9];
			$membre_nom = $liste[0];
			$membre_email = $liste[8];
			$membre_site = $liste[5];
			if (!empty($recherche)){
				$relevance = '<span class="relevance">'.sprintf(T_('Score for %s'),$recherche).' : '.$liste[10].'</span>';
				#$article_titre = ereg_replace($recherche, '<span class="highlight">\\0</span>', $article_titre);
				#$article_contenu = ereg_replace($recherche, '<span class="highlight">\\0</span>', $article_contenu);
				/*preg_match_all("/([^<>]+)/", $article_contenu, $matches);
				$article_contenu = "";
				foreach ($matches as $val) {
					print_r($val);
					if (preg_match("/<$val>/", $article_contenu)) { $article_contenu .= '<'.$val.'>'; }
					else {
						$article_contenu .= str_replace( $recherche, '<span class="highlight">'.$searchString.'</span>', $val);
					}
				}*/
			}

			# Balise HTML
			echo '<!-- debut post-content --><div class="article"><div class="separ_article_top"></div>';

			# Gravatar
			if($planet_avatar) {
				$gravatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($membre_email)."&default=".urlencode($planet_url."/themes/$planet_theme/images/gravatar.png")."&size=40";

				$gravatar = '<div class="avatar_article"><a href="'.$planet_url.'/?num_membre='.$membre_id.'" title="'.sprintf(T_("Show the posts of %s"),$membre_nom).'"><img src="'.$gravatar_url.'" class="gravatar" /></a></div>';
			}

			# Titre de l'article + ancre
			echo '<div class="nom_article">';
			if($planet_avatar) echo $gravatar;
			echo '<a href="'.$article_url.'" title="'.T_('Visit source').'">'.$article_titre.'</a><a name="article'.$cpt.'">&nbsp;</a>'.$relevance.'</div>'."\n";
			if ($activate_votes)
				echo afficheVotes($liste[7], $liste[6]);

			# Chapo de l'article
			echo '<div class="complement_article">'.sprintf(T_('By %s, on %s at %s.'),'<a href="'.$planet_url.'/?num_membre='.$membre_id.'">'.$membre_nom.'</a>',$date,$heure).'</div>';

			# Contenu de l'article
			echo "<div class=\"contenu_article\"> ";
			if($strip_tags) {
				echo strip_tags($article_contenu)."&nbsp;[...]";
				echo '<br /><a href="'.$article_url.'" title="'.$article_titre.'">'.T_('Read more').'</a>';
			}
			else echo $article_contenu;
			echo '<div class="separ_article_bottom"></div>';
			echo "</div><!-- fin post-content -->";

			# Lien de retour
			echo '<a href="#tour" class="retour_sommaire">'.T_('Back to summary').'</a>';
			echo "</div><!-- fin post -->\n";

			# Incrementation du compteur d'article
			$cpt++;
		}
	}
}

# Fonction qui affiche le sommaire rapide d'une liste d'article
function afficheSommaireArticles($sql) {

	# Recuperation des options de configuration
	global $nb_article;

	# Execution de la rqt sql
	$liste_articles = mysql_query(trim($sql)) or die("Error with request $sql");

	# On recupere le nombre de resultat
	$nb = mysql_num_rows($liste_articles);

	# Division
	echo "<div id=\"top_10\">";

	# Si il n'y a pas de resultat
	if(!$nb) {
		echo '<div class="post">'.T_('No posts found').'</div></div>';
		# Sinon si il y a des resultats
	} else {

		if($_SERVER['REQUEST_URI'] == '/') {
			# Si on est sur la page d'accueil
			echo '<h3>'.sprintf(T_('Fast access to the %d last posts'),$nb_article).'</h3><br/>';
		} else {
			echo '<h3>'.T_('Fast access to the last posts of the page').'</h3><br/>';
		}

		$max_title_length = 100;
		$cpt = 1;
		while ($cpt <= $nb_article && ($liste = mysql_fetch_row($liste_articles))) {

			# Formatage de la date
			$date = date("d/m/Y",$liste[1]);
			# Affichage du lien
			$titre = html_entity_decode($liste[2], ENT_QUOTES, 'UTF-8');
			if (strlen($titre) > $max_title_length)
				$show = substr($titre,0,$max_title_length)."...";
			else
				$show = $titre;
			echo '<a href="#article'.$cpt.'" title="'.$titre.'">'.$date.' : '.$show.'</a> ';
			# Incrementation du compteur
			$cpt++;
		}
		echo '</div><!-- fin post -->';
	}
}

# Procedure qui affiche les titres des items d'un flux RSS
function afficheTitreFlux($url, $nb, $htmlAvant, $htmlApres) {
	# Inclusion de la librairie simplepie (on le fait seulement ici
	# Afin d'optimiser le code
	require_once(dirname(__FILE__).'/lib/simplepie/simplepie.inc');
	# On cree un objet SimplePie
	$feed = new SimplePie();
	$feed->set_feed_url($url);
	$feed->set_cache_location(dirname(__FILE__).'/cache');
	$feed->set_cache_duration($item_refresh);
	$feed->init();
	$item_nb = $feed->get_item_quantity();
	if ($feed->get_item_quantity() == 0) {
		# Si le flux ne contient pas de donnee
		echo "Error: no item for $url";
	} else {

		# Sinon on affiche les titres de chaque item
		$items = $feed->get_items(0, $nb);
		foreach ($items as $item) {
			echo $htmlAvant.'<a href="'.$item->get_permalink().'">'.$item->get_title().'</a>'.$htmlApres;
		}

		# Destruction de l'objet
		unset($feed);
	}
}

# Fonction qui affiche la version du planet
function afficheVersion() {

	# Nom du fichier
	$fichier = dirname(__FILE__).'/../VERSION';

	# Ouverture du fichier en lecture
	$file = fopen($fichier, "r");

	# On recupere la version
	$version = trim(fgets($file,20));

	# Fermeture du fichier
	fclose($file);

	# On retourne le resultat
	return $version;
}

function afficheVotes($nb_votes, $num_article) {
	global $planet_theme, $planet_url;
	# On met un s a vote si il le faut
	$vote = "vote";
	if($nb_votes > 1) $vote = "votes";

	# Score du vote en fonction du system
	if($planet_votes_system == "yes-no") {
		$score = $nb_votes;
	} else {
		$score = positif($nb_votes);
	}

	# Bouton de vote
	$text =  '<div class="votes">';
	if (checkVote(getIP(), $num_article)) {

		# Si le visiteur a deja vote
		$text .= '<span id="vote'.$num_article.'" class="avote">'.$score.' '.$vote.'.<br/>
				<span id="imgoui" title="'.T_('Vote yes').'"></span>
				<span id="imgnon" title="'.T_('Vote no').'"></span>';
		$text .= '</span>';

	} else {

		# Si il n'a jamais vote, on construit le token
		$ip = getIP();
		$token = md5($ip.$num_article);
		# On affiche le bouton de vote
		$text .= '<span id="vote'.$num_article.'" class="vote">'.$score.' '.$vote.'<br/>
				<a href="#blackhole" title="'.T_('This post seems pertinent to you').'" id="aoui'.$num_article.'" 
				onclick="javascript:vote('."'$num_article','$token', 'positif'".');" >
				<span id="imgoui" title="'.T_('Vote yes').'"></span></a>';

		# En fonciton du systeme de vote
		if($planet_votes_system == "yes-no") {
			$text .= '<a href="#blackhole" title="'.T_('This post seems not pertinent to you').'" id="anon'.$num_article.'"
				onclick="javascript:vote('."'$num_article','$token', 'negatif'".');" >
				<span id="imgnon" title="'.T_('Vote no').'"></span></a>';
		} else {
			$text .= '<a href="#blackhole" title="'.T_('This post should not be here').'" id="anon'.$num_article.'"
				onclick="if(confirm(\''.T_('Are you sure this post should not be on this planet and should be removed?').'\')) '."{ vote('$num_article','$token', 'negatif');}".' " >
				<span id="imgnon" title="'.T_('Vote no').'"></span></a>';
		}
		$text .= "</span>";
	}
	$text .= "</div><!-- fin vote -->\n";
	return $text;
}

#-----------------------#
# Fonctions check forms #
#-----------------------#
# This function return an array with the keys : success, value, error
# Valid field types :
# email
# url
function check_field($fieldname, $value, $type="none", $required=true){
	$success=true;
	$error="";
	switch ($type) {
		case "email":
			if (!check_email_address($value)){
				$error = sprintf(T_('The field "%s" has to be a valid email address'),$fieldname);
				$success = false;
			}
			break;
		case "url":
			if (!check_url($value)){
				$error = sprintf(T_('The field "%s" %s has to be a valid URL'),$fieldname,$value);
				$success = false;
			}
			break;
		case "feed":
			if (!check_url($value)){
				$error = sprintf(T_('The field "%s" %s has to be a valid URL'),$fieldname,$value);
				$success = false;
			}
			if ($success && !check_feed($value)){
				$error = sprintf(T_('The field "%s" %s is not a valid simplepie feed'),$fieldname,$value);
				$success = false;
			}
			break;
		default:
			break;
	}
	if ($required && empty($value)){
		$error = sprintf(T_('The field "%s" is empty'),$fieldname);
		$success = false;
	}
	return array(
		"success" => $success,
		"value" => $value,
		"error" => $error
		);
}

function check_email_address($email) {
	// First, we check that there's one @ symbol, and that the lengths are right
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		return false;
	}
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
			return false;
		}
	}
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}

function check_url($url){
	// SCHEME
	$urlregex = "^(https?|ftp)\:\/\/";

	// USER AND PASS (optional)
	$urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";

	// HOSTNAME OR IP
	//$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*";  // http://x = allowed (ex. http://localhost, http://routerlogin)
	$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)+";  // http://x.x = minimum
	//$urlregex .= "([a-z0-9+\$_-]+\.)*[a-z0-9+\$_-]{2,3}";  // http://x.xx(x) = minimum
	//use only one of the above

	// PORT (optional)
	$urlregex .= "(\:[0-9]{2,5})?";
	// PATH  (optional)
	$urlregex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
	// GET Query (optional)
	$urlregex .= "(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?";
	// ANCHOR (optional)
	$urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?\$";

	// check
	if (eregi($urlregex, $url)) {
		$url = @parse_url($url);
		if ( ! $url) {
			return false;
		}
		$url = array_map('trim', $url);
		$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
		$path = (isset($url['path'])) ? $url['path'] : '';

		if ($path == ''){
			$path = '/';
		}

		$path .= ( isset ( $url['query'] ) ) ? "?$url[query]" : '';
		if ( isset ( $url['host'] ) AND $url['host'] != gethostbyname ( $url['host'] ) ){
			if ( PHP_VERSION >= 6 ){
				$site = "$url[scheme]://$url[host]:$url[port]$path";
				$headers = get_headers($site);
			}
			else{
				$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);

				if ( ! $fp ){
					return false;
				}
				fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
				$headers = fread ( $fp, 128 );
				fclose ( $fp );
			}
			$headers = ( is_array ( $headers ) ) ? implode ( "\n", $headers ) : $headers;
			return ( bool ) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers );
		}
		return false;
	} else {
		return false;
	}
}

function check_feed($url){
	require_once(dirname(__FILE__).'/lib/simplepie/simplepie.inc');
	$file = new SimplePie_File($url);
	$test = new SimplePie_Locator($file);

	if ($test->is_feed($file))
		return true;
	else
		return false;
}


#---------------------#
#   Fonction divers   #
#---------------------#

# Fonction de securite
function securiteCheck() {

	global $planet_url;

	# On recupere le nom du serveur d'ou vient le visiteur
	$server = domaine(trim($_SERVER["HTTP_REFERER"]));

	# On verifie la provenance
	if ( $server != domaine(trim($planet_url)) ) {

		# On informe d'un probleme et on arrete tout
		echo T_("Access denied");
		exit(1);
	}
}


# Fonction de cryptage d'adresse email
function hex_encode($str) {
	$encoded = bin2hex($str);
	$encoded = chunk_split($encoded, 2, '%');
	$encoded = '%'.substr($encoded, 0, strlen($encoded) - 1);
	return $encoded;
}

# Fonction qui retourne la taille d'un fichier
function tailleFichier($fichier) {

	# On recupere la taille du fichier
	$taille=filesize($fichier);

	# Gestion de l'unite
	if ($taille >= 1073741824) {
		$taille = round($taille / 1073741824 * 100) / 100 . " Go";
	} elseif ($taille >= 1048576) {
		$taille = round($taille / 1048576 * 100) / 100 . " Mo";
	} elseif ($taille >= 1024) {
		$taille = round($taille / 1024 * 100) / 100 . " Ko";
	} else {
		$taille = $taille . " o";
	} 

	if($taille==0) $taille="-";
	return $taille;
}


# Returns if cron is running or not
function get_cron_running() {
	$fichier = dirname(__FILE__).'/cron_running.txt';
	if (file_exists($fichier)) {
		$file = fopen($fichier, "r");
		$date = trim(fgets($file, 255));
		fclose($file);

		# si le timestamp est plus recent que 10 sec
		if ($date + 10 - time() >= 0)
			return true;
	}
	else
		return false;
}


?>
