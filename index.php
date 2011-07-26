<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
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
require_once(dirname(__FILE__).'/inc/prepend.php');

$page = isset($_GET['page']) ? $_GET['page'] : "tribe";
$action = isset($_GET['action']) ? $_GET['action'] : "view";

if (!in_array($page, array(
		'tribe',
		'post',
		'update'
	))) {
	$view = new GenericView($core, $_GET['page']);
	$view->addJavascript('javascript/functions.js');
	$view->render();
}
else {
	$classname = ucfirst($page).'Controller';
	$controller = null;
	if (class_exists($classname)) {
		$controller = new $classname($core);
	}
	if (method_exists($controller, $action)) {
		$controller->$action();
	} else {
		$view = new GenericView($core, '404');
		$view->render();
	}
}
?>
