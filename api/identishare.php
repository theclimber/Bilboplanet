<?php
//
// Copyright (C) 2011 Jacob Barkdull, Roberto Guido
//
//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU Affero General Public License as
//   published by the Free Software Foundation, either version 3 of the
//   License, or (at your option) any later version.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.
//


require_once(dirname(__FILE__).'/../inc/prepend.php');

$post_id = isset($_GET['post_id']) ? intval(trim($_GET['post_id'])) : -1;
$url = isset($_GET['url']) ? trim($_GET['url']) : '';
$planet_url = BP_PLANET_URL;

if (!is_int($post_id) || ($url != '' && !check_url($url))) {
	print T_("Permission denied");
	exit;
}

if(isset($_GET["source"])){
	header("Content-type: text/plain");
	readfile(".".$_SERVER["PHP_SELF"]);
	die();
}

$height="61";
if(isset($_GET["height"])){
	$height = $_GET["height"];
}

$server = $_SERVER["SERVER_NAME"];
if (!isset($_GET['nocount'])) {
	if ($url != '') {

		$referer = $url;
		$referringurl = str_replace(array("http://", "www."), "", $referer);
		$jsondata = file_get_contents("http://identi.ca/api/search.json?q=".$referringurl."&rpp=100");
		$results = substr_count($jsondata, str_replace("/", "\/", addslashes($referringurl)));

		if($results <= 0){
			$results = "0";
		}
	} elseif ($post_id > 0) {
	/*
	* if the number of share are not known yet, we check them by calling the api
	* if the number is known and is not older than 12 hours, then we take it
	*/
		$referer = $planet_url.'?post_id='.$post_id;
		$results = checkSharedLinkCount($post_id, "all");

	/*	# Check for planet URL
		$referer = $planet_url.'?post_id='.$post_id;
		$referringurl = str_replace(array("http://", "www."), "", $referer);
		$jsondata = file_get_contents("http://identi.ca/api/search.json?q=".$referringurl."&rpp=100");
		$result1 = substr_count($jsondata, str_replace("/", "\/", addslashes($referringurl)));

		#Check for permalink
		$rs = $core->con->select("SELECT post_permalink FROM ".$core->prefix."post WHERE post_id = ".$post_id);
		$referer = $rs->f('post_permalink');
		$referringurl = str_replace(array("http://", "www."), "", $rs->f('post_permalink'));
		$jsondata = file_get_contents("http://identi.ca/api/search.json?q=".$referringurl."&rpp=100");
		$result2 = substr_count($jsondata, str_replace("/", "\/", addslashes($referringurl)));

		# Get results
		$results = intval($result1) + intval($result2);
		if($results <= 0){
			$results = "0";
		}*/

		/*$sql = "SELECT
				post_id,
				engine,
				nb_share,
				modified
			FROM ".$core->prefix."post_share
			WHERE post_id = '$post_id' AND engine = 'identica'";
		$rs = $core->con->select($sql);
		if ($rs->count() > 0) {
			$results = $rs->f('nb_share');
		} else {
			$results = 0;
		}*/


	} else {
		$results = "-1";
	}

}

if(isset($_GET["title"])){
	$title = $_GET["title"]." ";
}else if(!isset($_GET["noscript"])){
	$title = '"+document.title+" - ';
}else{
	$title = '';
}

$html = <<<HTML
<html>
	<head>
		<title>Share on Identi.ca</title>
	</head>

	<body marginwidth="0" marginheight="0">
		<div id="identishare" style="height:$height; width:$height; display: inline-block; overflow; hidden; vertical-align: bottom; font-size: 35px; text-align: center;">
			<a href="http://identi.ca/index.php?action=newnotice&status_textarea=$title - $referer" target="_blank"
					style="display: inline-block; background-image: url('$planet_url/api/identishare_$height.png'); width: 32px; height: 32px; font-family: arial; text-decoration: none; line-height: 1.2em; color: #000000;"
					title="Share on Identi.ca"><b style="float: none !important; margin: 0px !important;">$results</b></a>
		</div>
	</body>
</html>
HTML;

if(isset($_GET["noscript"])){
	echo $html;
} elseif (isset($_GET['nocount'])) {
	$html2 = <<<HTML
<html>
	<head>
		<title>Share on Identi.ca</title>
	</head>
	<body marginwidth="0" marginheight="0">
		<div id="identishare" style="height:$height; width:$height; display: inline-block; overflow; hidden; vertical-align: bottom; font-size: 35px; text-align: center;">
			<a href="http://identi.ca/index.php?action=newnotice&status_textarea=$title - $referer" target="_blank" title="Share on Identi.ca"><img height=$height src="$planet_url/api/identica.png"/></a>
		</div>
	</body>
</html>
HTML;
	echo $html2;
}else{
	header("Content-type: text/javascript");
	echo 'document.getElementById("identishare").style.display="inline-block";'."\n";
	echo 'document.getElementById("identishare").style.width="'.$height.'px";'."\n";
	echo 'document.getElementById("identishare").style.overflow="hidden";'."\n";
	echo 'document.getElementById("identishare").innerHTML="<a href=\"http://identi.ca/index.php?action=newnotice&status_textarea='.$title.' - '.$referer.'\" target=\"_blank\" style=\"display: inline-block; background-image: url(\''.$planet_url.'/api/identishare.png\'); width: '.$height.'px; height: '.$height.'px; padding: 0px; font-size: 35px; text-decoration: none; line-height: 1.2em; color: #000000; text-align: center;\" title=\"Share on Identi.ca\"><b style=\"float: none !important; margin: 0px !important;\">'.$results.'</b></a>";';
}
?>
