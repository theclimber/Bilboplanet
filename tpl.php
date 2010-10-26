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
# RENDER WIDGETS
#####################
//TODO : it would be more reliable if we put in the settings table the widgets to load and where
// if we put a json dict in the database with for each element in the list:
// * the place where we want to place the widget (sidebar, footer)
// * the file to load
// * the order in which is have to load
// After that we need to check, before loading the tpl, if the widget function is returning a well formatted array
// * if yes => show it
// * if not => show an error in the widget place

$widget_path = dirname(__FILE__).'/widgets';
$widget_files = json_decode($blog_settings->get('planet_widget_files'));
foreach ($widget_files as $file){
	if (is_dir($widget_path) && is_file($widget_path.'/'.$file["name"])) {
		# Build an array of available widgets
		if ($file["position"] == "sidebar") {
			require_once($widget_path.'/'.$file["name"]);
			$wgt = getWidget();
			$core->tpl->setVar("sidebar-widget", $wgt);
			$core->tpl->render('sidebar.widget');
		}
		if ($file["position"] == "footer") {
			require_once($widget_path.'/'.$file["name"]);
			$wgt = getWidget();
			$core->tpl->setVar("footer-widget", $wgt);
			$core->tpl->render('footer.widget');
		}
	}
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
