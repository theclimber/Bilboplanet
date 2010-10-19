<?php

#######################
# RENDER SIDEBAR
#######################
if ($blog_settings->get('planet_msg_info')) {
	$core->tpl->render('sidebar.alert');
}
if($blog_settings->get('planet_vote')) {
	$core->tpl->render('sidebar.popular');
}

$sql_side = "SELECT 
	user_fullname as fullname,
	".$core->prefix."user.user_id as id,
	".$core->prefix."site.site_url as site_url,
	".$core->prefix."site.site_name as site_name
	FROM ".$core->prefix."user, ".$core->prefix."site
	WHERE ".$core->prefix."user.user_id = ".$core->prefix."site.user_id
	AND user_status = '1'
	ORDER BY lower(user_fullname)";
$rs_side = $core->con->select($sql_side);

while ($rs_side->fetch()) {
	$user_info = array(
		"id" => urlencode($rs_side->f('id')),
		"fullname" => $rs_side->f('fullname'),
		"site_url" => $rs_side->f('site_url')
		);
	$core->tpl->setVar("user", $user_info);
	$core->tpl->render("sidebar.users.list");
}

#####################
# RENDER FOOTER
#####################
$core->tpl->setVar('footer1', array(
	'text' => T_('Powered by Bilboplanet'),
	'url' => 'http://www.bilboplanet.com'));
$core->tpl->setVar('footer2', array(
	'text' => T_('Valid CSS - Xhtml'),
	'url' => 'http://validator.w3.org/check?verbose=1&uri='.$blog_settings->get('planet_url')));
$core->tpl->setVar('footer3', array(
	'text' => T_('Designed by BilboPlanet'),
	'url' => 'http://www.bilboplanet.com'));

######################
# RENDER JAVASCRIPT
######################
if (!isset($scripts)) {
	$scripts = array();
}

$scripts[] = "javascript/fancy.js";
foreach ($scripts as $js) {
	$core->tpl->setVar('js_file', $js);
	$core->tpl->render('js.import');
}
?>
