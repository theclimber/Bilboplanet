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

# Include
require_once(dirname(__FILE__).'/inc/prepend.php');

# Function to convert specific char for RSS feed
function convert_chars($string) {

	$string_convert = $string;

	$string_convert = str_replace('&','&#x26;', $string_convert);
	$string_convert = str_replace('<','&#x3C;', $string_convert);
	$string_convert = str_replace('>','&#x3E;', $string_convert);

	return $string_convert;
}

# Function to split string only on space char
function short_str( $str, $len, $cut = true ) {
	if ( strlen( $str ) <= $len ) {
		return $str;
	}
	if ($cut) {
		return substr( $str, 0, $len );
	} else {
		return substr( $str, 0, strrpos( substr( $str, 0, $len ), ' ' ) ) . ' [...]';
	}
}

function uuid($key = null, $prefix = '') {
	$key = ($key == null)? uniqid(rand()) : $key;
	$chars = md5($key);
	$uuid  = substr($chars,0,8) . '-';
	$uuid .= substr($chars,8,4) . '-';
	$uuid .= substr($chars,12,4) . '-';
	$uuid .= substr($chars,16,4) . '-';
	$uuid .= substr($chars,20,12);

	return $prefix . $uuid;
}

# Check content of $_GET
if (isset($_GET) && isset($_GET['type'])) {
	if ($_GET['type']=="rss"){
		header('Content-Type: application/rss+xml; charset=UTF-8');
		$params = "feed.php?type=rss";
	}
	elseif($_GET['type']=="atom") {
		header('Content-Type: application/atom+xml; charset=UTF-8');
		$params = "feed.php?type=atom";
	}

	$tags = array();
	$users = array();
	$period = '';
	$tribe_id = null;
	$post_status = 1;
	if ($blog_settings->get('allow_uncensored_feed')) {
		$post_status = !empty($_GET['uncensored']) ? 2 : 1;
	}
	#if ($blog_settings->get('accept_public_tagged_feeds')) {
	if (true) {
		$tags = !empty($_GET['tags']) ? getArrayFromList($_GET['tags']) : array();
		$users = !empty($_GET['users']) ? getArrayFromList($_GET['users']) : array();
		$period = !empty($_GET['filter']) ? trim($_GET['filter']) : '';
	}
	elseif ($blog_settings->get('accept_user_tagged_feeds')) {
		$user_token = !empty($_GET['token']) ? trim($_GET['token']) : '';
		if ($core->hasRole('user') || $core->hasRole('user',$token)){
			$tags = !empty($_GET['tags']) ? getArrayFromList($_GET['tags']) : array();
			$users = !empty($_GET['users']) ? getArrayFromList($_GET['users']) : array();
			$period = !empty($_GET['filter']) ? trim($_GET['filter']) : '';
		}
	}
	/*else {
		$tribe_id = !empty($_GET['tribe_id']) ? trim($_GET['tribe_id']) : '';
	}*/

	# Order by most popular
	$popular = !empty($_GET['popular']) ? true : false;

	# On recupere le numero de la tribe
	if (isset($_GET['tribe_id']) && !empty($_GET['tribe_id'])){
		$params["tribe_id"] = urldecode(trim($_GET['tribe_id']));
		$params["title"] = $params["title"]." - ".sprintf(T_("%s tribe"), $params['tribe_id']);
		if ($_GET['tribe_id'] == 'popular') {
			$popular = true;
		} else {
			$tribe_id = $_GET['tribe_id'];
		}
	}


	# On active le cache
	debutCache();

	# Get informations about posts
	if (isset($_GET["popular"]) && !empty($_GET['popular'])){
		$params .= "&popular=true";
		$title_add = " - Popular feed";
	} else {
		$title_add = " - Feed";
	}

	$nb_items=$blog_settings->get('planet_nb_post');
	$num_start = 0;
	if ($tribe_id != null) {
		$sql = generate_tribe_SQL(
			$tribe_id,
			$num_start,
			$nb_items);
	}
	if ($sql == "") {
		# Terminaison de la commande SQL
		$sql = generate_SQL(
			$num_start,
			$blog_settings->get('planet_nb_art_flux'),
			$users,
			$tags,
			$search_value,
			$period,
			$popular,
			null,
			$post_status);
	}
	//print $sql;
	//exit;

	# Encode specific char
	if ($_GET['type'] == "rss") {
		$params = convert_chars($params);
	}
	elseif ($_GET['type'] == "atom") {
		$params = str_replace("&", "&amp;", $params);
	}

	# Execute SQL request
	$post_list = $core->con->select($sql);
	$title = convert_chars(html_entity_decode(stripslashes($blog_settings->get('planet_title')), ENT_QUOTES, 'UTF-8')).$title_add;
	$planet_desc = convert_chars(html_entity_decode(stripslashes($blog_settings->get('planet_desc')), ENT_QUOTES, 'UTF-8'));

	# Head of XML document
	echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";

	if ($_GET['type']=="rss"){
	# Header of RSS 2.0 content
?>
	<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
		<channel>
			<title><?php echo $title; ?></title>
			<atom:link href="<?php echo BP_PLANET_URL."/".$params;?>" rel="self" type="application/rss+xml" />
			<link><?php echo BP_PLANET_URL; ?></link>
			<description><?php echo $planet_desc;?></description>
			<lastBuildDate><?php echo date('r'); ?></lastBuildDate>
			<language><?php echo $blog_settings->get('planet_lang'); ?></language>
			<generator>Bilboplanet v<?php echo str_replace(array("\r\n", "\r", "\n"), null, $blog_settings->get('planet_version')); ?></generator>
			<webMaster><?php echo strtolower($blog_settings->get('author_mail'))." (".$blog_settings->get('author').")"; ?></webMaster>
			<image>
				<url><?php echo BP_PLANET_URL.'/themes/'.$blog_settings->get('planet_theme').'/images/logo.png';?></url>
				<title><?php echo $title; ?></title>
				<link><?php echo BP_PLANET_URL; ?></link>
				<description><?php echo $planet_desc;?></description>
			</image>

<?php
	}
	elseif($_GET['type']=="atom") {
	#Header of Atom Content
		$authority = parse_url(BP_PLANET_URL);
		$authority = $authority['host'];

?>
	<feed xmlns="http://www.w3.org/2005/Atom">

		<title><?php echo $title; ?></title>
		<subtitle type="text"><?php echo $planet_desc; ?></subtitle>
		<updated><?php echo date('c') ?></updated>
		<icon><?php echo BP_PLANET_URL.'/themes/'.$blog_settings->get('planet_theme').'/favicon.ico';?></icon>
		<logo><?php echo BP_PLANET_URL.'/themes/'.$blog_settings->get('planet_theme').'/images/logo.png';?></logo>
		<id>tag:<?php
		echo $authority.','.date('Y').':'.BP_PLANET_URL;
		?></id>
		<author>
			<name><?php echo $blog_settings->get('author'); ?></name>
			<email><?php echo strtolower($blog_settings->get('author_mail')); ?></email>
			<uri><?php echo BP_PLANET_URL; ?></uri>
		</author>
		<link rel="alternate" type="text/html" href="<?php echo BP_PLANET_URL; ?>" />
		<link rel="self" href="<?php echo BP_PLANET_URL."/".$params; ?>" />
		<generator uri="http://www.bilboplanet.com" version="<?php echo str_replace(array("\r\n", "\r", "\n"), null, $blog_settings->get('planet_version')); ?>">
			Bilboplanet
		</generator>

<?php
	}

	while ($post_list->fetch()) {
		# Convert value to UTF-8
		$titre = convert_iso_special_html_char(html_entity_decode(html_entity_decode($post_list->title, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
		$nom = convert_iso_special_html_char(html_entity_decode(html_entity_decode($post_list->user_fullname, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
		$item = convert_iso_special_html_char(html_entity_decode(html_entity_decode($post_list->content, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));

		$url = $post_list->permalink;
		if ($blog_settings->get('internal_links')) {
			$url = BP_PLANET_URL.
				"/index.php?post_id=".$post_list->post_id.
				"&go=external";
		}
		$id = uuid($url, 'urn:uuid:');

		# Other link
		$links =  '<i>'.sprintf('Original post of <a href="%s" title="Visit the source">%s</a>.',$url, $nom);
		$links .= sprintf(T_('Vote for this post on <a href="%s" title="Go on the planet">%s</a>.'),BP_PLANET_URL, $blog_settings->get('planet_title')).'</i>';

		# Remove html tag to post content
		$desc = strip_tags($item);
		# Split string only on space char
		$desc = short_str($desc, 300, false);

		# Gravatar
		if($blog_settings->get('planet_avatar')) {
			$avatar_url = getUserIcon($post_list->user_email);
//			$avatar_email = strtolower($post_list->user_email);
//			$avatar_url = "http://cdn.libravatar.org/avatar/".md5($avatar_email)."?d=".urlencode(BP_PLANET_URL."/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png")."&s=40";
			$avatar = '<img src="'.$avatar_url.'" alt="'.sprintf(T_('Gravatar of %s'),$post_list->user_fullname).'" class="gravatar" />';
		}

		if($blog_settings->get('planet_avatar')) {
			$item = $item."\n<p>".$avatar;
		}

		$item = $item."<br/>".$links."</p>";

		# Similar posts
		$post_tags = getPostTags($post_list->post_id);
		if ($blog_settings->get("show_similar_posts") && !empty($post_tags)) {
			$sql_sim = getSimilarPosts_SQL($post_list->post_id, $post_tags);

			$rsimilar = $core->con->select($sql_sim);
			if ($rsimilar->count() > 0) {
				$sim_html = '<div class="similar-block"><h3>'.T_('Similar posts').'</h3><ul>';
				while ($rsimilar->fetch()) {
					$post_permalink = $rsimilar->permalink;
					if ($blog_settings->get('internal_links')) {
						$post_permalink = BP_PLANET_URL.
							"/index.php?post_id=".$rsimilar->post_id;
					}
					$sim_html .= "<li>";
					$sim_html .= $rsimilar->user_fullname." : ";
					$sim_html .= '<a href="'.urldecode($post_permalink).'">'.$rsimilar->post_title;
					$sim_html .= "</a> (".mysqldatetime_to_date("d/m/Y",$rsimilar->post_pubdate).")";
					$sim_html .= "</li>";
				}
				$sim_html .= "</ul></div>";
				$item = $item."\n<p>".$sim_html."</p>";
			}
		}

		if ($_GET['type']=="rss"){
			# Display item content
			echo "\t\t\t<item>\n";
			echo "\t\t\t\t<title>".$nom." : ".$titre."</title>\n";
			echo "\t\t\t\t<link>".htmlentities($url)."</link>\n";
			echo "\t\t\t\t<pubDate>".date("r", strtotime($post_list->pubdate))."</pubDate>\n";
			echo "\t\t\t\t<dc:creator>".$nom."</dc:creator>\n";
			echo "\t\t\t\t<description><![CDATA[".$desc."]]></description>\n";
			echo "\t\t\t\t<guid isPermaLink=\"true\">".htmlentities($url)."</guid>\n";

			echo "\t\t\t\t<content:encoded><![CDATA[".$item."]]></content:encoded>\n";

			# End of Item
			echo "\t\t\t</item>\n";
		}
		elseif($_GET['type']=="atom") {
			# Affichage du contenu de l'item
			echo "\t\t<entry>\n";
			echo "\t\t\t<id>".$id."</id>\n";
			echo "\t\t\t<title>".$nom." : ".$titre."</title>\n";
			echo "\t\t\t<updated>".date("c", strtotime($post_list->pubdate))."</updated>\n";
			echo "\t\t\t<author>\n";
			echo "\t\t\t\t<name>".$nom."</name>\n";
			echo "\t\t\t</author>\n";
			echo "\t\t\t<link href=\"".htmlentities($url)."\" rel=\"alternate\" type=\"text/html\" title=\"".$titre."\" />\n";
			echo "\t\t\t<summary type=\"html\">".str_replace(array("\r\n", "\r", "\n"), " ", $desc)."</summary>\n";

			echo "\t\t\t<content type=\"html\"><![CDATA[".$item."]]></content>\n";
			echo "\t\t</entry>\n";
		}
	}

	if ($_GET['type']=="rss"){
		echo "\t\t</channel>\n";
		echo "\t</rss>";
	}
	elseif($_GET['type']=="atom") {
		echo "\t</feed>";
	}

		/* On termine le cache */
	finCache();
}
else {
	http::head(301);
	http::redirect(BP_PLANET_URL."/feed.php?type=rss");
}

$root_url = BP_PLANET_URL;
$analytics = $blog_settings->get('planet_analytics');
if(!empty($analytics)) {
	$analyzed_url = $root_url.'/feed/'.$_GET['type'];
	if (!empty($tags)) {
		$analyzed_url .= '/tags/'.implode(',',$tags);
	}
	if (!empty($users)) {
		$analyzed_url .= '/users/'.implode(',',$users);
	}
	if (!empty($period)) {
		$analyzed_url .= '/period/'.$period;
	}
	analyze($analytics, $analyzed_url, T_('Feed'),"",0);
}
?>
