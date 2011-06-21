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

$tribe_id = $blog_settings->get('planet_main_tribe');
$tags = '';
$users = '';
$search = '';

# Customizing the tribe
if (isset($_GET)) {
	# if user want to read a unique tribe
	if (isset($_GET['id']) && !empty($_GET['id'])){
		$tribe_id = $_GET['id'];
		$core->tribes->setCurrentTribe($tribe_id);
	}
	if (isset($_GET['tags'])) {
		$tags = $_GET['tags'];
		$core->tribes->setCurrentTags($tags);
	}
	if (isset($_GET['users'])) {
		$users = $_GET['users'];
		$core->tribes->setCurrentUsers($users);
	}
	if (isset($_GET['search'])) {
		$search = $_GET['search'];
		$core->tribes->setCurrentSearch($search);
	}
}


$view = new TribeView($core);
# Customizing the view
if (isset($_GET)) {
	if (isset($_GET['page']) && is_numeric($_GET['page'])) {
		$view->setPage($_GET['page']);
	}
	if (isset($_GET['nbitems']) && is_numeric($_GET['nbitems'])) {
		$view->setNbItems($_GET['nbitems']);
	}
	if (isset($_GET['popular']) {
		$view->setPopular();
	}
	if (isset($_GET['filter']) &&
		in_array($_GET['filter'], array('day', 'week', 'month', 'all'))) {
		$view->setPeriod($_GET['filter']);
	}
}

# Print result on screen
$view->render();

?>
