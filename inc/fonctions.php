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
#----------------------------------#
#   Functions for support of JSON  #
#----------------------------------#
if ( !function_exists('json_decode') ){
	function json_decode2($content, $assoc=false){
		require_once (dirname(__FILE__).'/lib/json/JSON.php');
		if ( $assoc ){
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		} else {
			$json = new Services_JSON;
		}
		return $json->decode($content);
	}
}

if ( !function_exists('json_encode') ){
	function json_encode2($content){
		require_once (dirname(__FILE__).'/lib/json/JSON.php');
		$json = new Services_JSON;

		return $json->encode($content);
	}
}

#----------------------------------#
#   Fonction Analytics             #
#----------------------------------#
function analyze($analytics,$purl,$name, $go, $html = 1) {
	global $blog_settings;

	if ($analytics == "google-analytics") {
		$aid = $blog_settings->get('planet_ganalytics');
		ga($aid,$purl,$name);
	}
	elseif($analytics == "piwik"){
		$aid = $blog_settings->get('piwik_id');
		$script = piwik_analytics($aid,$purl,$name, $go, $html);
	}
}
function piwik_analytics($aid,$purl,$name, $go, $html = 1) {
	require_once(dirname(__FILE__).'/lib/PiwikTracker.php');
	global $blog_settings;
	if (empty($go)){
		$go = $_SERVER['PHP_SELF'];
	}
	PiwikTracker::$URL = $blog_settings->get('piwik_url');

	$screen_resolution = "1280x800";
	if (!isset($_SESSION[iterate]) && $html) {
		if(!isset($_COOKIE["piwik_user_resolution"])){
			//means cookie is not found set it using Javascript
			$_SESSION[iterate]=1;
?>
<script language="javascript"><!--
	writeCookie();
	function writeCookie() {
		var the_cookie = "piwik_user_resolution="+ screen.width +"x"+ screen.height;
		document.cookie=the_cookie
		location ='<?php echo $go;?>';
	}
//--></script>
<?php
		} else{
			$screen_resolution = $_COOKIE["piwik_user_resolution"];
		}
	} else{
		$screen_resolution = $_COOKIE["users_resolution"];
	}

	$screen_resolution = preg_split('x',$screen_resolution);

	$t = new PiwikTracker( $idSite = $aid, $blog_settings->get('piwik_url'));
	// Optional tracking
	$t->setUserAgent($_SERVER['HTTP_USER_AGENT']);
	$t->setBrowserLanguage(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
	$t->setLocalTime( date("H:i:s") );
	$t->setResolution( $screen_resolution[0], $screen_resolution[1] );
	// Mandatory
	$t->setUrl( $url = $purl );
	$t->doTrackPageView($name);
}

function ga($gaid,$url,$name) {
	global $blog_settings;
	$var_utmac=$gaid; //enter the new urchin code
	$var_utmhn=BP_PLANET_URL; //enter your domain
	$var_utmn=rand(1000000000,9999999999);//random request number
	$var_cookie=rand(10000000,99999999);//random cookie number
	$var_random=rand(1000000000,2147483647); //number under 2147483647
	$var_today=time(); //today
	$var_referer=$_SERVER['HTTP_REFERER']; //referer url

	$var_uservar=urlencode($name); //enter your own user defined variable
	$var_utmp=$url; //this example adds a fake page request to the (fake) rss directory (the viewer IP to check for absolute unique RSS readers)

	$urchinUrl='http://www.google-analytics.com/__utm.gif?utmwv=1&utmn='.$var_utmn.'&utmsr=-&utmsc=-&utmul=-&utmje=0&utmfl=-&utmdt=-&utmhn='.$var_utmhn.'&utmr='.$var_referer.'&utmp='.$var_utmp.'&utmac='.$var_utmac.'&utmcc=__utma%3D'.$var_cookie.'.'.$var_random.'.'.$var_today.'.'.$var_today.'.'.$var_today.'.2%3B%2B__utmb%3D'.$var_cookie.'%3B%2B__utmc%3D'.$var_cookie.'%3B%2B__utmz%3D'.$var_cookie.'.'.$var_today.'.2.2.utmccn%3D(direct)%7Cutmcsr%3D(direct)%7Cutmcmd%3D(none)%3B%2B__utmv%3D'.$var_cookie.'.'.$var_uservar.'%3B';

	$handle = fopen ($urchinUrl, "r");
	$test = fgets($handle);
	fclose($handle);
}

#---------------------------------------------------#
#   Fonction de publication sur les sites sociaux   #
#---------------------------------------------------#

/**
 * @param hostname : this must be a valid hostname url like "http://identi.ca"
 * @param username : your statusnet username
 * @param password : your statusnet password
 * @param message : the message you want to post
 */
function postToStatusNet($hostname,$username,$password,$message){
	global $blog_settings;
	if (!empty($hostname)) {
		$host = $hostname."/api/statuses/update.xml?status=".urlencode(stripslashes(urldecode($message)))."&source=bilboplanet%20api";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $host);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($ch);
		// Look at the returned header
		$resultArray = curl_getinfo($ch);

		curl_close($ch);

		if($resultArray['http_code'] == "200") {
			$statusnet_status='OK'; }
		else $statusnet_status = "KO : error code :".$resultArray['http_code'].").";
	} else {
		$statusnet_status = T_("Error : the statusnet account is not properly configured. The given hostname is empty.");
	}
	return $statusnet_status;
}

/**
 * version of sprintf for cases where named arguments are desired (python syntax)
 *
 * with sprintf: sprintf('second: %2$s ; first: %1$s', '1st', '2nd');
 *
 * with sprintfn: sprintfn('second: %(second)s ; first: %(first)s', array(
 *  'first' => '1st',
 *  'second'=> '2nd'
 * ));
 *
 * @param string $format sprintf format string, with any number of named arguments
 * @param array $args array of [ 'arg_name' => 'arg value', ... ] replacements to be made
 * @return string|false result of sprintf call, or bool false on error
 */
function sprintfn ($format, array $args = array()) {
	// map of argument names to their corresponding sprintf numeric argument value
	$arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);

	// find the next named argument. each search starts at the end of the previous replacement.
	for ($pos = 0; preg_match('/(?<=%)\(([a-zA-Z_]\w*)\)/', $format, $match, PREG_OFFSET_CAPTURE, $pos);) {
		$arg_pos = $match[0][1];
		$arg_len = strlen($match[0][0]);
		$arg_key = $match[1][0];

		// programmer did not supply a value for the named argument found in the format string
		if (! array_key_exists($arg_key, $arg_nums)) {
			user_error("sprintfn(): Missing argument '${arg_key}'", E_USER_WARNING);
			return false;
		}

		// replace the named argument with the corresponding numeric one
		$format = substr_replace($format, $replace = $arg_nums[$arg_key] . '$', $arg_pos, $arg_len);
		$pos = $arg_pos + strlen($replace); // skip to end of replacement for next iteration
	}

	return vsprintf($format, array_values($args));
}


#----------------------------------#
#   Fonction de gestion du cache   #
#----------------------------------#

# Procedure de debut de cache des pages
function debutCache() {
	global $log;

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
function getNbUsers($con) {
	global $core;
	$sql = 'SELECT COUNT(1) as nb FROM '.$core->prefix.'user WHERE user_status = 1';
	$rs = $con->select($sql);
	return $rs->f('nb');
}

# Fonction qui retourne le nombre de flux
function getNbFeeds($con) {
	global $core;
	$sql = 'SELECT COUNT(1) as nb FROM '.$core->prefix.'user, '.$core->prefix.'feed WHERE '.$core->prefix.'user.user_id = '.$core->prefix.'feed.user_id AND '.$core->prefix.'user.user_status = 1';
	$rs = $core->con->select($sql);
	return $rs->f('nb');
}

# Fonction qui retourne le nombre d'articles
function getNbPosts($con, $user_id = null) {
	global $core;
	if (isset($user_id)) {
		$sql = 'SELECT COUNT(1) as nb FROM '.$core->prefix.'post WHERE post_status = 1 AND user_id = \''.$user_id.'\'';
	} else {
		$sql = 'SELECT COUNT(1) as nb FROM '.$core->prefix.'post WHERE post_status = 1';
	}
	$rs = $core->con->select($sql);
	return $rs->f('nb');
}

# Fonction qui retourne le nombre de votes
function getNbVotes($con, $user_id = null) {
	global $core;
	if (isset($user_id)) {
		$sql = "SELECT
				".$core->prefix."user.user_id as user_id,
				SUM(post_score) AS nb
			FROM ".$core->prefix."post, ".$core->prefix."user, ".$core->prefix."site
			WHERE
				".$core->prefix."site.user_id = ".$core->prefix."user.user_id
				AND ".$core->prefix."user.user_id = ".$core->prefix."post.user_id
				AND ".$core->prefix."user.user_id = '".$user_id."'
				AND user_status = 1
			GROUP BY ".$core->prefix."user.user_id";
	} else {
		$sql = 'SELECT COUNT(1) as nb FROM '.$core->prefix.'votes';
	}
	$rs = $core->con->select($sql);
	return $rs->f('nb');
}

# Fonction qui retourne le nombre de flux en attente
function getNbPendingFeed() {
	global $core;
	$sql = 'SELECT COUNT(1) as nb FROM '.$core->prefix.'pending_feed WHERE 1';
	$rs = $core->con->select($sql);
	return $rs->f('nb');
}

function getUserSite($user_id) {
	global $core;
	$sql = "SELECT site_url
		FROM ".$core->prefix."site
		WHERE user_id = '".$user_id."'";
	$rs = $core->con->select($sql);

	$sites = array();
	while ($rs->fetch()) {
		$sites[] = $rs->site_url;
	}
	return $sites;
}
function getFeedSite($feed_id) {
	global $core;
	$sql = "SELECT site_url
		FROM ".$core->prefix."site, ".$core->prefix."feed
		WHERE ".$core->prefix."feed.site_id = ".$core->prefix."site.site_id
			AND ".$core->prefix."feed.feed_id = ".$feed_id;
	$rs = $core->con->select($sql);
	return $rs->f('site_url');
}
function getPostTags($post_id) {
	global $core;
	$sql = "SELECT tag_id
		FROM ".$core->prefix."post_tag
		WHERE post_id = ".$post_id.";";
	$res = $core->con->select($sql);

	$tags = array();
	while($res->fetch()){
		$tags[] = $res->tag_id;
	}
	return $tags;
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
function checkVote($con, $ip, $num_article) {
	global $core;
	$user_id = '';
	if ($core->auth->sessionExists()) {
		$user_id = $core->auth->userID();
		$sql = "SELECT COUNT(vote_ip) as nb FROM ".$core->prefix."votes WHERE post_id = '$num_article' AND user_id = '".$user_id."'";

		# Recuperation des adresses IP qui ont votes
		$rs = $con->select($sql);
		if ($rs->f('nb')==0)
			return false;
		return true;
	}
	return false;
}

# Fonction qui converti les code de carateres html special iso en code utf8 html
function convert_iso_special_html_char($string) {
	$search = array('&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&');
	$replace = array('&#39;', '&#39;', '&quot;', '&quot;', '&amp;');
	return str_replace($search, $replace, $string);
}

function getArrayFromList($list) {
	if ($list == null) {
		return array();
	}
	$patterns = array( '/, /', '/ ,/');
	$replacement = array(',', ',');
	$list = urldecode($list);
	$list = preg_replace($patterns, $replacement, $list);
	$array = preg_split('/,/',$list, -1, PREG_SPLIT_NO_EMPTY);
	return $array;
}

function getListFromArray($arr) {
	$list = implode(',',$arr);
	return $list;
}

#--------------------------#
#   Fonction d'affichage   #
#--------------------------#
function generate_SQL(
		$num_start = 0,
		$nb_items = null,
		$users = array(),
		$tags = array(),
		$search = null,
		$period = null,
		$popular = false,
		$post_id = null,
		$post_status = 1,
		$count = false,
		$notags = array(),
		$nousers = array())
	{
	global $blog_settings, $core;
	if (!isset($nb_items)) {
		$nb_items = $blog_settings->get('planet_nb_post');
	}

	$tables = $core->prefix."post, ".$core->prefix."user";
	if (!empty($tags) || !empty($notags)) {
		$tables .= ", ".$core->prefix."post_tag";
	}

	if (!$count) {
		$select = $core->prefix."user.user_id		as user_id,
				user_fullname	as user_fullname,
				user_email		as user_email,
				user_token		as user_token,
				post_pubdate	as pubdate,
				post_title		as title,
				post_permalink	as permalink,
				post_content	as content,
				post_image		as image,
				post_nbview		as nbview,
				last_viewed		as last_viewed,
				SUBSTRING(post_content,1,1200) as short_content,
				".$core->prefix."post.post_id		as post_id,
				post_score		as score,
				post_status		as status,
				post_comment	as comment";
	} else {
		$select = "COUNT(".$core->prefix."post.post_id) as count,
				MAX(".$core->prefix."post.post_pubdate) as last";
	}
	$where_clause = $core->prefix."user.user_id = ".$core->prefix."post.user_id
		AND user_status = '1'
		AND post_score > '".$blog_settings->get('planet_votes_limit')."'";

	if ($post_status <= 1) {
		$where_clause .= " AND post_status = '".$post_status."' ";
	}

	if (isset($post_id) && !empty($post_id)) {
		$where_clause .= " AND ".$core->prefix."post.post_id = '".$post_id."'";
		$sql = "SELECT DISTINCT
				".$select."
			FROM ".$tables."
			WHERE ".$where_clause;
		return $sql;
	}

	// find all posts with users
	if (!empty($users)) {
		$sql_users = "(";
		foreach ($users as $key=>$user) {
			$sql_users .= "LOWER(".$core->prefix."post.user_id) = '".strtolower($user)."'";
			$or = ($key == count($users)-1) ? "" : " OR ";
			$sql_users .= $or;
		}
		$sql_users .= ")";
		$where_clause .= ' AND '.$sql_users.' ';
	}

	// find all posts without this users
	if (!empty($nousers)) {
		$sql_nousers = "(";
		foreach ($nousers as $key=>$user) {
			$sql_nousers .= "LOWER(".$core->prefix."post.user_id) != '".strtolower($user)."'";
			$and = ($key == count($nousers)-1) ? "" : " AND ";
			$sql_nousers .= $and;
		}
		$sql_nousers .= ")";
		$where_clause .= ' AND '.$sql_nousers.' ';
	}

	// find all posts with theses tags
	if (!empty($tags)) {
		$sql_tags = "(";
		foreach ($tags as $key=>$tag) {
			$sql_tags .= "LOWER(".$core->prefix."post_tag.tag_id) = '".strtolower($tag)."'";
			$or = ($key == count($tags)-1) ? "" : " OR ";
			$sql_tags .= $or;
		}
		$sql_tags .= ")";
		$where_clause .= " AND ".$core->prefix."post.post_id = ".$core->prefix."post_tag.post_id";
		$where_clause .= ' AND '.$sql_tags.' ';
	}

	// find all posts without theses tags
	if (!empty($notags)) {

/*		$sql_notags = "";
		foreach ($notags as $key=>$tag) {
			$sql_notags .= "('".$tag."' NOT IN (SELECT
				LOWER(".$core->prefix."post_tag.tag_id) as tag_id FROM ".$core->prefix."post_tag
				WHERE ".$core->prefix."post_tag.post_id = ".$core->prefix."post.post_id";
			$sql_notags .= "))";
			$sql_notags .= ($key == count($notags)-1) ? "" : " AND ";
		}*/

		$sql_notags = "(";
		foreach ($notags as $key=>$tag) {
//			$sql_notags .= "LOWER(".$core->prefix."post_tag.tag_id) != '".strtolower($tag)."'";
			$and = ($key == count($notags)-1) ? "'".$tag."'" : "'".$tag."', ";
			$sql_notags .= $and;
		}
		$sql_notags .= ") NOT IN (SELECT ";
		for ($i = 0; $i < count($notags); $i++) {
			$sql_notags .= "LOWER(".$core->prefix."post_tag.tag_id)";
			if ($i < count($notags)-1) {
				$sql_notags .= ",";
			}
		}
		$sql_notags .= " FROM ".$core->prefix."post_tag
			WHERE ".$core->prefix."post_tag.post_id = ".$core->prefix."post.post_id)";
		$where_clause .= " AND ".$core->prefix."post.post_id = ".$core->prefix."post_tag.post_id";

		$where_clause .= ' AND '.$sql_notags.' ';
	}

	if (isset($search) && !empty($search)){
		# Complete the SQL query
		$search = strtolower($search);
		$where_clause .= " AND (
			lower(".$core->prefix."post.post_title) LIKE '%$search%'
			OR lower(".$core->prefix."post.post_permalink) LIKE '%$search%'
			OR lower(".$core->prefix."post.post_content) LIKE '%$search%'
			OR lower(".$core->prefix."user.user_fullname) LIKE '%$search%')";
	}

	if (isset($period) && !empty($period)) {
		# Complete the SQL query
		$now = mktime(0, 0, 0, date("m",time()), date("d",time()), date("Y",time()));
		$day = date('Y-m-d', $now).' 00:00:00';
		$week = date('Y-m-d', $now - 3600*24*7).' 00:00:00';
		$month = date('Y-m-d', $now - 3600*24*31).' 00:00:00';
		$filter_class = array(
			"day" => "",
			"week" => "",
			"month" => "");
		switch($period) {
		case "day"		:
			$where_clause .= " AND post_pubdate > '".$day."'";
			break;
		case "week"		:
			$where_clause .= " AND post_pubdate > '".$week."'";
			break;
		case "month"	:
			$where_clause .= " AND post_pubdate > '".$month."'";
			break;
		default			:
			$where_clause .= " AND post_pubdate > '".$week."'";
			break;
		}
	}

	if ($popular){
		// the popular posts are based on :
		// * the number of times they are viewed
		// * the score of votes
		// * the times they are shared in social networks
		$max = $core->con->select("SELECT
			MAX(post_nbview) as max_view,
			MAX(post_score) as max_score,
			MAX(nb_share) as max_share
			FROM ".$core->prefix."post, ".$core->prefix."post_share");
		$max_view = !$max->f('max_view') ? 1 : $max->f('max_view');
		$max_score = !$max->f('max_score') ? 1 : $max->f('max_score');
		$max_share = !$max->f('max_share') ? 1 : $max->f('max_share');
		# Complete the SQL query
		$select .= ",
			post_score/".$max_score." + post_nbview/".$max_view." + nb_share/".$max_share." as total_score";
		$tables .= ", ".$core->prefix."post_share";
		$where_clause .= " AND post_score > 0
			AND ".$core->prefix."post_share.post_id = ".$core->prefix."post.post_id
			AND ".$core->prefix."post_share.engine = 'identica'";
		if (!isset($period) || empty($period)) {
			$week = time() - 3600*24*7;
			$where_clause .= "AND post_pubdate > ".$week;
		}
		$order_sql = " ORDER BY total_score DESC";
	}
	else {
		$order_sql = " ORDER BY post_pubdate DESC";
	}
	if (!$count && $nb_items > 0) {
		$limit_sql = "LIMIT $nb_items OFFSET $num_start";
	} else {
		$limit_sql = "";
	}

	$debut_sql = "SELECT DISTINCT
			".$select."
		FROM ".$tables."
		WHERE ".$where_clause;
	$fin_sql = $order_sql." ".$limit_sql;
	$sql = $debut_sql." ".$fin_sql;

//	print $sql;
//	exit;
	return $sql;
}

function generate_tribe_SQL($tribe_id, $num_start = 0, $nb_items = 10, $popular=false, $search = '') {
	global $core;
	$sql_tribes = "SELECT
			tribe_id,
			user_id,
			tribe_name,
			tribe_search,
			tribe_tags,
			tribe_notags,
			tribe_users,
			tribe_nousers
		FROM ".$core->prefix."tribe
		WHERE tribe_id = '".$tribe_id."'
		AND visibility = 1";
#	print $sql_tribes;
#	exit;
	$rs = $core->con->select($sql_tribes);

	if ($rs->f('user_id') != "root") {
		if (!$core->hasPermission('administration')) {
			if (!$core->auth->sessionExists() || $core->auth->userID() != $rs->f('user_id')) {
				return "";
			}
		}
	}

	$tribe_name = $rs->f('tribe_name');
	$tribe_search = $rs->f('tribe_search');//getArrayFromList($rs->tribe_search);
	if ($search != '') {
		$tribe_search = $search;
	}
	$tribe_tags = getArrayFromList($rs->f('tribe_tags'));
	$tribe_notags = getArrayFromList($rs->f('tribe_notags'));
	$tribe_users = getArrayFromList($rs->f('tribe_users'));
	$tribe_nousers = getArrayFromList($rs->f('tribe_nousers'));
	$align = $align=='right'? 'left' : 'right';

	$count = true;
	if ($nb_items > 0) {
		$count = false; // we just need to count the number of posts
	}
	// Generating the SQL request

	return generate_SQL(
		$num_start,
		$nb_items,
		$tribe_users,
		$tribe_tags,
		$tribe_search,
		null, // period
		$popular, // popular
		null, // post_id
		1, // post_status
		$count, // count
		$tribe_notags,
		$tribe_nousers
	);
}

function getSimilarPosts_SQL($post_id,$post_tags) {
	global $core;
	for($i=0; $i<sizeof($post_tags); $i++) {
		$post_tags[$i] = "'".$core->con->escape($post_tags[$i])."'";
	}
	$tags_string = implode(",",$post_tags);
	$sql_sim = "SELECT
			".$core->prefix."post.post_id,
			".$core->prefix."post.user_id,
			".$core->prefix."user.user_fullname,
			".$core->prefix."post.post_pubdate,
			".$core->prefix."post.post_permalink,
			".$core->prefix."post.post_title,
			COUNT(".$core->prefix."post.post_id) as tag_count
		FROM
			".$core->prefix."post,
			".$core->prefix."post_tag,
			".$core->prefix."user
		WHERE
		".$core->prefix."post.post_id = ".$core->prefix."post_tag.post_id
			AND ".$core->prefix."user.user_id = ".$core->prefix."post.user_id
			AND NOT ".$core->prefix."post.post_id = ".$post_id."
			AND tag_id IN (".$tags_string.")
		GROUP BY ".$core->prefix."post.post_id, ".$core->prefix."user.user_fullname
		HAVING COUNT(".$core->prefix."post.post_id) > 2
		ORDER BY tag_count DESC
		LIMIT 5";
	return $sql_sim;
}

function showTribe($tpl, $rs_posts, $page = 0) {
	global $core, $blog_settings;
	if ($rs_posts->count() > 0) {
		while ($rs_posts->fetch()) {
			######################
			# RENDER TRIBE LIST
			######################
			$post_permalink = $rs_posts->permalink;
			if ($blog_settings->get('internal_links')) {
				$post_permalink = BP_PLANET_URL.
					"/index.php?post_id=".$rs_posts->post_id.
					"&go=external";
			}

			$entry = array(
				"id" => $rs_posts->post_id,
				"date" => mysqldatetime_to_date("d/m/Y",$rs_posts->pubdate),
				"day" => mysqldatetime_to_date("d",$rs_posts->pubdate),
				"month" => mysqldatetime_to_date("m",$rs_posts->pubdate),
				"year" => mysqldatetime_to_date("Y",$rs_posts->pubdate),
				"hour" => mysqldatetime_to_date("H:i",$rs_posts->pubdate),
				"permalink" => urldecode($post_permalink),
				"title" => html_entity_decode($rs_posts->title, ENT_QUOTES, 'UTF-8'),
				"content" => html_entity_decode($rs_posts->content, ENT_QUOTES, 'UTF-8'),
				"image" => $rs_posts->image,
				"author_id" => $rs_posts->user_id,
				"author_fullname" => $rs_posts->user_fullname,
				"author_email" => $rs_posts->user_email,
				"nbview" => $rs_posts->nbview,
				"last_viewed" => mysqldatetime_to_date('d/m/Y H:i',$rs_posts->last_viewed),
				"user_votes" => getNbVotes(null,$rs_posts->user_id),
				"user_posts" => getNbPosts(null,$rs_posts->user_id)
				);

			$tpl->setVar('entry', $entry);

			$tpl->render('portal.entry');
		}
    }
    return $tpl;
}

function showSinglePost($rs, $tpl, $search_value, $multiview=true, $strip_tags=false) {
	global $blog_settings, $core, $user_settings;
	$avatar = $blog_settings->get('planet_avatar');

	$post_permalink = $rs->f('permalink');
	if ($blog_settings->get('internal_links')) {
		$post_permalink = BP_PLANET_URL.
			"/index.php?post_id=".$rs->f('post_id').
			"&go=external";
	}

	$title = html_entity_decode($rs->f('title'), ENT_QUOTES, 'UTF-8');
	$content = html_entity_decode($rs->f('content'), ENT_QUOTES, 'UTF-8');
	$short_content = html_entity_decode($rs->f('short_content'), ENT_QUOTES, 'UTF-8');
	$short = true;
	if (strlen($content)<1200) {
		$short = false;
		$short_content = $content;
	}
	$post = array(
		"id" => $rs->f('post_id'),
		"date" => mysqldatetime_to_date("d/m/Y",$rs->f('pubdate')),
		"day" => mysqldatetime_to_date("d",$rs->f('pubdate')),
		"month" => mysqldatetime_to_date("m",$rs->f('pubdate')),
		"year" => mysqldatetime_to_date("Y",$rs->f('pubdate')),
		"hour" => mysqldatetime_to_date("H:i",$rs->f('pubdate')),
		"permalink" => urldecode($post_permalink),
		"title" => $title,
		"content" => $content,
		"short_content" => $short_content,
		"image" => $rs->f('image'),
		"author_id" => $rs->f('user_id'),
		"author_fullname" => $rs->f('user_fullname'),
		"author_email" => $rs->f('user_email'),
		"nbview" => $rs->f('nbview'),
		"last_viewed" => mysqldatetime_to_date('d/m/Y H:i',$rs->f('last_viewed')),
		"user_votes" => getNbVotes(null,$rs->f('user_id')),
		"user_posts" => getNbPosts(null,$rs->f('user_id'))
		);

	$post['description'] = sprintf(T_('By %s, on %s at %s.'),
		'<a href="#" onclick="javascript:add_user(\''.$rs->f('user_id').'\')">'.$rs->f('user_fullname').'</a>',
		$post["date"],$post["hour"]);
	$post['description'].= ' <a href="'.BP_PLANET_URL.'/index.php?post_id='.$rs->f('post_id').'" title="'.$post['title'].'">'.T_("View post detail").'</a>';
	if (!empty($search_value)){
		# Format the occurences of the search request in the posts list
		$post['content'] = split_balise($search_value, '<span class="search-content">'.$search_value.'</span>', $post['content'], 'str_ireplace', 1);
		# Format the occurences of the search request in the posts title
		$post['title'] = split_balise($search_value, '<span class="search_title">'.$search_value.'</span>', $post['title'], 'str_ireplace', 1);
	}

	if ($short) {
		$last_space = strripos($post['short_content'], ' ');
		$post['short_content'] = substr($post['short_content'],0,$last_space);
		$post['short_content'] = '<span class="short-content">'.strip_tags($post['short_content'])."&nbsp;[...] </span>".
				'<br /><span class="read-more"><a href="'.BP_PLANET_URL.'/?post_id='.$post['id'].'" title="'.$title.'">'.T_('Read more').'</a></span>';
	}
	if($strip_tags) {
		$post['content'] = $post['short_content'];
	}
	$post_tags = getPostTags($rs->f('post_id'));

	$tpl->setVar('post', $post);
	# Gravatar
	if($avatar) {
		$libravatar = getUserIcon($post['author_email']);
		$tpl->setVar('avatar_url', $libravatar);

		$tpl->render('post.block.gravatar');
	}
	if ($multiview) {
		if ($post['image'] != '') {
			$tpl->render('post.image');
		}
		$tpl->render('post.multi');
	}
	if ($blog_settings->get('planet_vote')) {
		$votes = array("html" => afficheVotes($rs->f('score'), $rs->f('post_id')));
		$tpl->setVar('votes', $votes);
		$tpl->render('post.block.votes');
	}
	if (!empty($post_tags)){
		foreach ($post_tags as $tag) {
			$tpl->setVar('post_tag', $tag);
			$tpl->render('post.tags');
		}
	}
	if ($blog_settings->get('allow_post_modification')) {
		if($blog_settings->get('allow_tagging_everything')) {
			$tpl->render('post.action.tags');
		} else {
			if($core->auth->userID() == $rs->f('user_id')) {
				$tpl->render('post.action.tags');
			}
		}
	}
	if ($user_settings != null) {
		if ($user_settings->get("social.twitter")) {
			$tpl->render('social.twitter');
		}
		if ($user_settings->get("social.shaarli")) {
			$simple_title = $post['title'];
			$tpl->setVar('stripped_title', addslashes($simple_title));
			$tpl->render('social.shaarli');
		}
		if ($user_settings->get("social.google")) {
			$tpl->render('social.google');
		}
		if ($user_settings->get("social.statusnet")) {
			$tpl->setVar('stripped_title', urlencode($rs->f('title')));
			$tpl->render('social.statusnet');
		}
		if ($user_settings->get("social.reddit")) {
			$tpl->setVar('stripped_title', addslashes($simple_title));
			$tpl->setVar('encoded_url', urlencode(BP_PLANET_URL.'/?post_id='.$post['id']));
			$tpl->render('social.reddit');
		}
	}
	if ($blog_settings->get('show_similar_posts') && !empty($post_tags)) {
		$sql_sim = getSimilarPosts_SQL($rs->f('post_id'), $post_tags);
		$rsimilar = $core->con->select($sql_sim);
		if ($rsimilar->count() > 0) {
			while ($rsimilar->fetch()) {
				$post_permalink = $rsimilar->permalink;
				if ($blog_settings->get('internal_links')) {
					$post_permalink = BP_PLANET_URL.
						"/index.php?post_id=".$rsimilar->post_id;
				}
				$similar = array(
					"author" => $rsimilar->user_fullname,
					"title" => $rsimilar->post_title,
					"permalink" => urldecode($post_permalink),
					"pubdate" => mysqldatetime_to_date("d/m/Y",$rsimilar->post_pubdate)
				);
				$tpl->setVar('similar', $similar);
				$tpl->render("post.similar.item");
			}
			$tpl->render("post.similar.block");
		}
	}
	if ($blog_settings->get('allow_post_comments')) {
		if($core->auth->userID() == $rs->f('user_id') || $core->hasRole('manager')) {
			if ($rs->f('comment')) {
				$tpl->render('post.action.uncomment');
			} else {
				$tpl->render('post.action.comment');
			}
		}
	}
	if ($blog_settings->get('allow_post_comments') && $rs->f('comment') == 1) {
		$sql = "SELECT * FROM ".$core->prefix."comment
			WHERE post_id=".$rs->f('post_id');
//		print $sql;
//		exit;
		$rs_comment = $core->con->select($sql);
		while ($rs_comment->fetch()) {
			$fullname = $rs_comment->user_fullname;
			if (!empty($rs_comment->user_site)) {
				$fullname = '<a href="'.$rs_comment->user_site.'">'.$fullname.'</a>';
			}
			$content = $core->wikiTransform($rs_comment->content);
			$comment = array(
				"id" => $rs_comment->comment_id,
				"post_id" => $rs_comment->post_id,
				"user_fullname_link" => $fullname,
				"user_fullname" => $rs_comment->user_fullname,
				"user_site" => $rs_comment->user_site,
				"content" => $content,
				"pubdate" => mysqldatetime_to_date("d/m/Y",$rs_comment->created)
				);
			$tpl->setVar("comment", $comment);
			$tpl->render('post.comment.element');
		}
		$tpl->render('post.comment.block');
	}
	if ($rs->count()>1) {
		$tpl->render('post.backsummary');
	}
	return $tpl;
}

function showPosts($rs, $tpl, $search_value="", $multiview=true, $strip_tags=false) {
	global $blog_settings, $core, $user_settings;
	$avatar = $blog_settings->get('planet_avatar');

	while($rs->fetch()){
		$tpl = showSinglePost($rs,$tpl,$search_value,$multiview,$strip_tags);
		$tpl->render('post.block');
	}

	if ($rs->count() > 1) {
		$tpl->render('post.morebutton');
	}
	return $tpl;
}

# Fonction qui affiche le sommaire rapide d'une liste d'article
function showPostsSummary($rs, $tpl) {
	while($rs->fetch()){
		$max_title_length = 100;
		$title = html_entity_decode($rs->title, ENT_QUOTES, 'UTF-8');
		if (strlen($title) > $max_title_length)
			$show = substr($title,0,$max_title_length)."...";
		else
			$show = $title;

		$line = array(
			"date" => mysqldatetime_to_date("d/m/Y",$rs->pubdate),
			"title" => $title,
			"user" => $rs->user_fullname,
			"short_title" => $show,
			"id" => $rs->post_id,
			"url" => "#post".$rs->post_id);
		$tpl->setVar('summary', $line);
		$tpl->render('summary.line');
	}
	return $tpl;
}

function afficheVotes($nb_votes, $num_article) {
	global $blog_settings, $core;

	# On met un s a vote si il le faut
	$vote = "vote";
	if($nb_votes > 1) $vote = "votes";

	# Score du vote en fonction du system
	$score = $nb_votes;
	if($blog_settings->get('planet_votes_system') != "yes-no" && $score < 0)
		$score = 0;

	# Bouton de vote
	$text =  '';
	if (checkVote($core->con, getIP(), $num_article)) {

		# Si le visiteur a deja vote
		$text .= '<span id="vote'.$num_article.'" class="avote">'.$score.' '.$vote.'.
				<span id="imgoui" title="'.T_('Vote yes').'"></span>
				<span id="imgnon" title="'.T_('Vote no').'"></span>';
		$text .= '</span>';

	} else {

		# Si il n'a jamais vote, on construit le token
		$ip = getIP();
		$token = md5($ip.$num_article);
		# On affiche le bouton de vote
		$text .= '<span id="vote'.$num_article.'" class="vote">'.$score.' '.$vote.'
				<a href="#blackhole" title="'.T_('This post seems pertinent to you').'" id="aoui'.$num_article.'"
				onclick="javascript:vote('."'$num_article','$token', 'positif'".');" >
				<span id="imgoui" title="'.T_('Vote yes').'"></span></a>';

		# En fonciton du systeme de vote
		if($blog_settings->get('planet_votes_system') == "yes-no") {
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
	return $text;
}

#-----------------------#
# Fonctions check forms #
#-----------------------#
# This function return an array with the keys : success, value, error
# Valid field types :
# email
# url
# feed
# login
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
		case "login":
			if (!check_login($value)) {
				$msg = "The username has to be formed of minimum 2 characters with letters and/or numbers. ";
				$msg .= "Specials characters allowed: \"@\" \".\" \"_\" \"-\"";
				$error = sprintf(T_($msg).'.',$fieldname,$value);
				$success = false;
			}
			break;
		case "password":
			if ($value['password'] != $value['password2']) {
				$success = false;
				$error = sprintf(T_('The fields %s does not match'),$fieldname,$value);
			}
			elseif(strlen($value['password']) < 4 && strlen($value['password']) != 0) {
				$success = false;
				$error = sprintf(T_('The fields %s needs to have 4 characters minimum'),$fieldname,$value);
			}
			$value = $value['password'];
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
	if (substr($url, -1) == '/'){ # remove de last '/' in URL if exists
		$url = substr_replace($url, '', -1, 1);
	}

	// SCHEME
	$urlregex = "^(https?|ftp)\:\/\/";

	// USER AND PASS (optional)
	$urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";

	// HOSTNAME OR IP
	$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*";  // http://x = allowed (ex. http://localhost, http://routerlogin)
	//$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)+";  // http://x.x = minimum
	//$urlregex .= "([a-z0-9+\$_-]+\.)*[a-z0-9+\$_-]{2,3}";  // http://x.xx(x) = minimum
	//use only one of the above

	// PORT (optional)
	$urlregex .= "(\:[0-9]{2,5})?";
	// PATH  (optional)
	$urlregex .= "(\/([a-z0-9+\$_-~\%\-\.]\.?)+)*\/?";
	// GET Query (optional)
	$urlregex .= "(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?";
	// ANCHOR (optional)
	#$urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?\$";

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
		if (isset($url['host'])) {
			return true;
		}

		// Curl the url to see if it really exists
/*		if ( isset ( $url['host'] ) AND $url['host'] != gethostbyname ( $url['host'] ) ){
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
		}*/
		return false;
	} else {
		return false;
	}
}

function check_feed($url){
	require_once(dirname(__FILE__).'/lib/simplepie_1.3.compiled.php');
	$file = new SimplePie_File($url);
	$test = new SimplePie_Locator($file);
	$test->set_registry(new SimplePie_Registry());

	if ($test->is_feed($file))
		return true;
	else
		return false;
}

function check_login ($login) {
		$regex = '/^[A-Za-z0-9@._-]{2,}$/';
		if(preg_match($regex, $login)) {
			return true;
		}
		else {
			return false;
		}
}

#---------------------#
#   Fonction divers   #
#---------------------#

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

function get_database_size(){
	global $core;
	if ($core->con->driver() == "mysql") {
		$request = mysql_query("SHOW TABLE STATUS") or die("Error with request $sql : ".mysql_error());
		$dbsize = 0;
		while( $row = mysql_fetch_array($request) ) {
			$dbsize += $row[ "Data_length" ] + $row[ "Index_length" ];
		}
	} elseif ($core->con->driver() == "pgsql") {
		$rs = $core->con->select("SELECT pg_database_size('".$core->con->database()."')");
		$dbsize = $rs->f(0);
	} else {
		$dbsize = "N/A";
	}
	return $dbsize;
}

function formatfilesize( $data ) {
	// bytes
	if( $data < 1024 ) {
		return $data . T_(" bytes");
	}
	// kilobytes
	elseif( $data < 1024000 ) {
		return round( ( $data / 1024 ), 1 ) . T_(" Kb");
	}
	// megabytes
	elseif ($data < 1073741824) {
		return round( ( $data / 1024000 ), 1 ) . T_(" MB");
	}
	// gigabytes
	else {
		return round( ( $data / 1073741824 ), 1) . T_(" GB");
	}
}

function my_gzdecode($string) {
	$string = substr($string, 10);
	return gzinflate($string);
}
#---------------------#
#   Fonction mail     #
#---------------------#

function sendmail ($sender, $recipients, $subject, $message, $type='normal', $reply_to='') {

	global $blog_settings;

	$planet_title = html_entity_decode(stripslashes($blog_settings->get('planet_title')), ENT_QUOTES, 'UTF-8');

	$subject = html_entity_decode(stripslashes($subject), ENT_QUOTES, 'UTF-8');
	$subject = "[".$planet_title."] ".$subject;
	$subject= mb_encode_mimeheader($subject,"UTF-8", "Q", "\n");

	$message = html_entity_decode(stripslashes($message), ENT_QUOTES, 'UTF-8');

	$sender = strtolower($sender);
	$recipients = strtolower($recipients);

	if (empty($reply_to)){
		$reply_to = $sender;
	}
	$reply_to = strtolower($reply_to);

	if ($type == 'newsletter') {
		$headers  = "From: ".mb_encode_mimeheader($planet_title,"UTF-8", "Q", "\n")." <".$sender.">\r\n";
		$headers .= "Return-Path: ".$sender."\r\n";
		$headers .= "Reply-To: ".$reply_to."\r\n";
		$headers .= "Bcc: ".$recipients."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		$headers .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
		$message_content  = "<html>\r\n";
		$message_content .= "<body>\r\n";
		$message_content .= $message."\r\n";
		$message_content .= "</body>\r\n";
		$message_content .= "</html>\r\n";
	$recipients = $sender;
	}
	elseif ($type == 'normal') {
		$headers  = "From: ".$sender."\r\n";
		$headers .= "Reply-To: ".$reply_to."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
		$headers .= "Content-Transfer-Encoding: base64\r\n\r\n";
		$message_content = base64_encode($message);
	}
	return mail($recipients, $subject, $message_content, $headers);
}

/**
* Convert MySQL's DATE (YYYY-MM-DD) or DATETIME (YYYY-MM-DD hh:mm:ss) to timestamp
*
* Returns the timestamp equivalent of a given DATE/DATETIME
*
* @todo add regex to validate given datetime
* @author Clemens Kofler <clemens.kofler@chello.at>
* @access    public
* @return    integer
*/
function mysqldatetime_to_timestamp($datetime = "")
{
  // function is only applicable for valid MySQL DATETIME (19 characters) and DATE (10 characters)
  $l = strlen($datetime);
    if(!($l == 10 || $l == 19))
      return 0;

    //
    $date = $datetime;
    $hours = 0;
    $minutes = 0;
    $seconds = 0;

    // DATETIME only
    if($l == 19)
    {
      list($date, $time) = explode(" ", $datetime);
      list($hours, $minutes, $seconds) = explode(":", $time);
    }

    list($year, $month, $day) = explode("-", $date);

    return mktime($hours, $minutes, $seconds, $month, $day, $year);
}

/**
* Convert MySQL's DATE (YYYY-MM-DD) or DATETIME (YYYY-MM-DD hh:mm:ss) to date using given format string
*
* Returns the date (format according to given string) of a given DATE/DATETIME
*
* @author Clemens Kofler <clemens.kofler@chello.at>
* @access    public
* @return    integer
*/
function mysqldatetime_to_date($format = "d.m.Y, H:i:s", $datetime = "")
{
    return date($format, mysqldatetime_to_timestamp($datetime));
}

/**
* Convert timestamp to MySQL's DATE or DATETIME (YYYY-MM-DD hh:mm:ss)
*
* Returns the DATE or DATETIME equivalent of a given timestamp
*
* @author Clemens Kofler <clemens.kofler@chello.at>
* @access    public
* @return    integer
*/
function timestamp_to_mysqldatetime($timestamp = "", $datetime = true)
{
  if(empty($timestamp) || !is_numeric($timestamp)) $timestamp = time();

    return ($datetime) ? date("Y-m-d H:i:s", $timestamp) : date("Y-m-d", $timestamp);
}

function decode_strip($str, $max_str_length){
	$dstr = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
	if (strlen($dstr) > $max_str_length)
		return substr($dstr,0, $max_str_length)."...";
	else
		return $dstr;
}

#-------------------------------------------------------------------#
# Functions to recover the plain text into the HTML original code.	#
# To search into the HTML tags, the variable $flag need to be at -1	#
#-------------------------------------------------------------------#
function split_balise($de, $par, $txt, $fct, $flag = 1){
	global $arg;
	$arg = compact('de', 'par', 'fct', 'flag');
	return preg_replace_callback('#((?:(?!<[/a-z]).)*)([^>]*>|$)#si', "mon_rplc_callback", $txt);
}

function mon_rplc_callback($capture){
	global $arg;
	if ($arg['flag'] == 1) {
		return $arg['fct']($arg['de'], $arg['par'], $capture[1]).$capture[2];
	}
	else {
		return $capture[1].$arg['fct']($arg['de'], $arg['par'], $capture[2]);
	}
}

function cleanString($toClean) {
	$normalizeChars = array(
		'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
		'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
		'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
		'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
		'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
		'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
		'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', '®'=>'r', '€'=>'e', 'ĸ'=>'k',
		'@'=>'a', '§'=>'s', '¢'=>'c', 'ħ'=>'h', 'ŋ'=>'n', 'Ŧ'=>'T', 'œ'=>'oe'
	);
	$toClean     =     str_replace('&', '-and-', $toClean);
	$toClean     =     strtr($toClean, $normalizeChars);
	$toClean     =     trim(preg_replace('/[^\w\d_ -]/si', '', $toClean));//remove all illegal chars
	$toClean     =     str_replace(' ', '_', $toClean);
	$toClean     =     str_replace('--', '-', $toClean);

	return $toClean;
}

#---------------------------------------------------#
# Function to encode html inside <code></code> tags	#
#---------------------------------------------------#
function code_htmlentities ($html, $tag1, $tag2, $return = 0) {
	$split1 = preg_split('(<'.$tag1.'[^>]*>)', $html, -1);
	$result = array();

	# Pour chaque element on test si on trouve une fin de balise
	foreach ($split1 as $el) {
		$split2 = preg_split('(<\/'.$tag1.'[^>]*>)', $el, -1);
		if (count($split2) == 2) {
			# si la longueur du tableau est de 2, c'est qu'il y avait une balise
			# l'element avec une valise est le premier des deux
			$content_text = htmlentities(stripslashes($split2[0]), ENT_QUOTES, 'UTF-8');
			if ($return) {
				$content_text = str_replace("\n", '<br/>', $content_text);
			}
			$result[] = '<'.$tag2.'>'.$content_text.'</'.$tag2.'>';
			$result[] = $split2[1];
		}
		else {
			# s'il n'y a pas de balise, alors on ajoute simplement l'element au tableau
			$result[] = $el;
		}
	}

	# Maintenant que le texte est transforme en tableau, on l'affiche element par element
	$output = "";
	foreach($result as $el) {
		$output .= $el;
	}
	return $output;
}


#-----------------------------------------------#
# Function to generate random Password		#
#-----------------------------------------------#
function createRandomPassword() {
	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
	srand((double)microtime()*1000000);
	$i = 0;
	$pass = '' ;

	while ($i <= 7) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}

	return $pass;
}
function getAnalyticsCode() {
	global $blog_settings;
	$output='';
	$analytics = $blog_settings->get('planet_analytics');

	if ($analytics == "google-analytics") {
		$ga = $blog_settings->get('planet_ganalytics');
		$output = "
<script type=\"text/javascript\">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$ga']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
		";
	} elseif ($analytics == 'piwik') {
		$id = $blog_settings->get('piwik_id');
		$url = $blog_settings->get('piwik_url');
		$output = "
<!-- Piwik -->
<script type=\"text/javascript\">
	var pkBaseURL = ((\"https:\" == document.location.protocol) ? \"https://piwik.kiwais.com/\" : \" $url\");
document.write(unescape(\"%3Cscript src='\" + pkBaseURL + \"piwik.js' type='text/javascript'%3E%3C/script%3E\"));
</script><script type=\"text/javascript\">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + \"piwik.php\",  $id);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch(err) {
console.debug(err);
}
</script>
<noscript><p><img src=\"$url/piwik.php?idsite=$id\" style=\"border:0\" alt='' /></p></noscript>
<!-- End Piwik Tracking Tag -->";
	}
	return $output;
}
function strip_script($string, $rm = 1) {
	if (!$rm) {
		$s = array("/<(\/script[^\>]*)>/", "/<(script[^\>]*)>/");
		$r = array("<$1></myScript>", "<myScript><$1>");
		//do
		$string = preg_replace($s, $r, $string);
		//while (preg_match($s, $string)!=0);
		return code_htmlentities($string, 'myScript', 'pre', 1);
	} else {

		$split1 = preg_split('(<script[^>]*>)', $string, -1);
		$result = array();

		# Pour chaque element on test si on trouve une fin de balise
		foreach ($split1 as $el) {
			$split2 = preg_split('(<\/script[^>]*>)', $el, -1);
			if (count($split2) == 2) {
				# si la longueur du tableau est de 2, c'est qu'il y avait une balise
				# l'element avec une valise est le premier des deux
				$content_text = '';
				//$result[] = '<'.$tag2.'>'.$content_text.'</'.$tag2.'>';
				$result[] = $split2[1];
			}
			else {
				# s'il n'y a pas de balise, alors on ajoute simplement l'element au tableau
				$result[] = $el;
			}
		}

		# Maintenant que le texte est transforme en tableau, on l'affiche element par element
		$output = "";
		foreach($result as $el) {
			$output .= $el;
		}
		return $output;
	}
}

function getNbTweet($post_id) {
	global $blog_settings, $core;
	$planet_url = BP_PLANET_URL;

	/*
	# Check for planet URL
	$referer = $planet_url.'?post_id='.$post_id;
	$referringurl = str_replace(array("http://", "www."), "", $referer);
	$jsondata = file_get_contents("http://urls.api.twitter.com/1/urls/count.json?url=".$referringurl."&rpp=100");
	$result1 = substr_count($jsondata, str_replace("/", "\/", addslashes($referringurl)));
	 */
	$result1 = 0;

	#Check for permalink
	$rs = $core->con->select("SELECT post_permalink FROM ".$core->prefix."post WHERE post_id = ".$post_id);
	$referer = $rs->f('post_permalink');
	$referringurl = str_replace(array("http://", "www."), "", $rs->f('post_permalink'));
	$jsondata = file_get_contents("http://urls.api.twitter.com/1/urls/count.json?url=".$referringurl."&rpp=100");
	$data = json_decode($jsondata);
	$result2 = $data->{'count'};

	# Get results
	$results = intval($result1) + intval($result2);
	if($results <= 0){
		$results = "0";
	}

	return $results;
}

function getNbDent($post_id) {
	global $blog_settings, $core;
	$planet_url = BP_PLANET_URL;

	# Check for planet URL
	$referer = $planet_url.'?post_id='.$post_id;
	$referringurl = str_replace(array("http://", "www."), "", $referer);
	$jsondata = file_get_contents("http://identi.ca/api/search.json?q=".$referringurl."&rpp=100");
//	$result1 = substr_count($jsondata, str_replace("/", "\/", addslashes($referringurl)));
	$data = json_decode($jsondata);
	$result1 = count($data->{'results'});

	#Check for permalink
	$rs = $core->con->select("SELECT post_permalink FROM ".$core->prefix."post WHERE post_id = ".$post_id);
	$referer = $rs->f('post_permalink');
	$referringurl = str_replace(array("http://", "www."), "", $rs->f('post_permalink'));
	$jsondata = file_get_contents("http://identi.ca/api/search.json?q=".$referringurl."&rpp=100");
//	$result2 = substr_count($jsondata, str_replace("/", "\/", addslashes($referringurl)));
	$data = json_decode($jsondata);
	$result2 = count($data->{'results'});

	# Get results
	$results = intval($result1) + intval($result2);
	if($results <= 0){
		$results = "0";
	}

	return $results;
}

function checkSharedLinkCount($post_id, $return_engine="") {
	global $core, $blog_settings;

	$return = 0;
	$share_count = json_decode($blog_settings->get('planet_share_count'));

	foreach ($share_count as $engine) {
		$run_update = false;
		$nb_share = 0;
		$sql = "SELECT
				post_id,
				engine,
				nb_share,
				modified
			FROM ".$core->prefix."post_share
			WHERE post_id = '$post_id' AND engine = '".$engine."'";
		$rs = $core->con->select($sql);
		if ($rs->count() == 0) {
			$run_update = true;
		} else {
			$last_update = mysqldatetime_to_timestamp($rs->f('modified'));
			$to_update = time() - 3600*12;
			if ($last_update - $to_update <= 0) {
				// the last update was more than 12h ago
				$run_update = true;
			}
		}

		if ($run_update) {
			switch($engine) {
			case "twitter":
				$nb_share = getNbTweet($post_id);
				break;
			case "identica":
				$nb_share = getNbDent($post_id);
				break;
			default:
				$nb_share = 0;
				break;
			}

			$cur = $core->con->openCursor($core->prefix.'post_share');
			$cur->nb_share = $nb_share;
			$cur->modified = array(' NOW() ');
			if ($rs->count() == 0) {
				$cur->post_id = $post_id;
				$cur->engine = $engine;
				$cur->created = array(' NOW() ');
				$cur->insert();
			} elseif ($nb_share > $rs->f('nb_share')) {
				$cur->update("WHERE post_id='$post_id' AND engine='$engine'");
			}
			if ($return_engine == $engine || $return_engine == "all") {
				$return += $nb_share;
			}
		} else {
			if ($return_engine == $engine || $return_engine == "all") {
				$return += $rs->f('nb_share');
			}
		}
	}
	return $return;

}
function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function startWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function getAllSupportedLanguages() {
	$i18n_folder = dirname(__FILE__).'/../i18n';
	$directories = glob($i18n_folder.'/*',GLOB_ONLYDIR);
	$directories = scandir($i18n_folder);
	$lang = array(
		array("code" => "en", "name" => T_("English")));
	foreach ($directories as $dir) {
		if ($dir != ".." && $dir != ".") {
			if (is_dir($i18n_folder.'/'.$dir)) {
				$lang[] = array("code" => $dir, "name" => getLanguageName($dir));
			}
		}
	}
	return $lang;
}

function getLanguageName($lang) {
	switch ($lang) {
	case 'en':
		return T_("English");
	case 'fr':
		return T_("French");
	case 'de':
		return T_("German");
	case 'nl':
		return T_("Dutch");
	case 'ar':
		return T_("Arab");
	case 'it':
		return T_("Italian");
	case 'es':
		return T_("Spanish");
	default:
			return "";
	}
}


function stripAccents($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
	'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

function generateUserToken($name,$email,$password) {
	global $blog_settings;
	$len = strlen($email.$password);
	$rand = rand(1,20);
	if ($blog_settings != null)
		$salt = $blog_settings->get('planet_salt');
	$token = sha1(time().substr($salt,0,$len).substr($name,0,$rand).$password);
	return $token;
}

function joinBilboplanetCommunity($url,$title,$desc,$author,$mail) {
	global $blog_settings;
	$joined=false;
	if ($blog_settings != null) {
		$joined = $blog_settings->get('planet_joined_community');
	}
	if (!$joined) {
		$objet = T_("A new Bilboplanet joined the community");
		$to = "dev@bilboplanet.com";
		$msg .= "\n".T_("Url : ").$url;
		$msg .= "\n".T_("Title : ").$title;
		$msg .= "\n".T_("Description : ").$desc;
		$msg .= "\n\n".T_("Author : ").$author;
		$msg .= "\n".T_("Email : ").$mail;

		$envoi1 = sendmail($mail, $to, $objet, $msg);
		if ($envoi1) {
			if ($blog_settings != null) {
				$blog_settings->put('planet_joined_community', '1', "boolean");
			}
			return T_("You're planet joined the Bilboplanet community !");
		}
		return T_("Problem sending email.");
	}
	return T_("Your planet already joined the Bilboplanet community.");
}


 // When the directory is not empty:
function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir")
					rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

function getTribeIcon($tribe_id,$tribe_name,$tribe_icon) {
    if ($tribe_icon != '') {
        $tribe_icon = BP_PLANET_URL.'/'.$tribe_icon;
    } else {
        $tribe_icon = "http://cdn.libravatar.org/avatar/".md5($tribe_id.$tribe_name)."?default=retro";
    }
    return $tribe_icon;
}
function getUserIcon($user_email) {
	global $blog_settings;
	$avatar_email = strtolower($user_email);
//		$backup_img = urlencode(BP_PLANET_URL."/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png");
	$identicon = "/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png";
	if (!is_file(dirname(__FILE__)."/..".$identicon)) {
		$libravatar = "http://cdn.libravatar.org/avatar/".md5($avatar_email)."?default=identicon";
	} else {
		$identicon = urlencode(BP_PLANET_URL.$identicon);
		$libravatar = "http://cdn.libravatar.org/avatar/".md5($avatar_email)."?d=".$identicon;
	}
	return $libravatar;
}
?>
