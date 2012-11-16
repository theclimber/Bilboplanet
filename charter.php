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
require_once(dirname(__FILE__).'/inc/prepend.php');
$scripts = array();
$scripts[] = "javascript/functions.js";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

$params = array(
	'title'=> $blog_settings->get('planet_title')." - ".T_("Charter"),
	);

$content = $blog_settings->get('planet_subscription_content');
$content = stripslashes($content);
$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
$content = code_htmlentities($content, 'code', 'code', 1);

$core->tpl->setVar('params', $params);
$core->tpl->setVar('subscription_content', $content);
$core->tpl->render('content.charter');
$core->renderTemplate();

?>
