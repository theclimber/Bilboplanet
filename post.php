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

# Verification du contenu du get
if (isset($_GET)) {
	# if user want to read a unique post
	if (isset($_GET['id']) && !empty($_GET['id'])){
		$post = new bpPost($core->con, $core->prefix, intval($_GET['id']));

		if($post->canRead()) {
			if (
				isset($_GET['go']) &&
				$_GET['go'] == "external" &&
				$blog_settings->get('internal_links')
			){

				$root_url = $blog_settings->get('planet_url');
				$analytics = $blog_settings->get('planet_analytics');

				if(!empty($analytics)) {
					# If google analytics is activated, launch request
					analyze (
						$analytics,
						$root_url.'/post/'.$post->getId(),
						'post:'.$this->getId,
						$post->getPermalink());
				}
				http::redirect(stripslashes($post->getPermalink));
			} else {
				$view = new PostView($core);
				$view->addJavascript('javascript/main.js');
				$view->addJavascript('javascript/jquery.boxy.js');
				# Print result on screen
				$view->render();
			}
		}
	}
}
?>
