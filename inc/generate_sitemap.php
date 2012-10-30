<?php
/**
 * La structure XML est très simple à écrire. Les balises suivantes sont obligatoires :
    * urlset : ouverture et femeture du fichier
    * url : pour chaque page
    * loc : pour chaque url
 */
function generate_sitemap () {
	global $core,$blog_settings;

	$now = mktime(0, 0, 0, date("m",time()), date("d",time()), date("Y",time()));
	$week = date('Y-m-d', $now - 3600*24*7).' 00:00:00';

	$sql = "SELECT
			post_id
		FROM ".$core->prefix."post, ".$core->prefix."user
		WHERE ".$core->prefix."user.user_id = ".$core->prefix."post.user_id
		AND `post_status` = 1
		AND user_status = 1
		AND post_score > '".$blog_settings->get('planet_votes_limit')."'
		ORDER BY post_pubdate DESC";
	$rs = $core->con->select($sql);

	$xml .= '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'."\n";
	$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	while($rs->fetch()) {
		$xml .= "\n\t".'<url>';
		$xml .= "\n\t\t".'<loc>'.BP_PLANET_URL.'/?post_id='.$rs->post_id.'</loc>';
		$xml .= "\n\t\t".'<priority>0.5000</priority>';
		$xml .= "\n\t".'</url>';
	}
	$xml .= "</urlset>";

	$file = dirname(__FILE__).'/../sitemap.xml';

	if (file_exists($file)) {
		# supprimer le fichier existant
		unlink($file);
	}

	$fp = @fopen($file,'wb');
	if ($fp === false) {
		throw new Exception(sprintf(__('Cannot write %s file.'),$file));
	}
	fwrite($fp,$xml);
	fclose($fp);
}
require_once(dirname(__FILE__).'/prepend.php');
generate_sitemap();
?>
