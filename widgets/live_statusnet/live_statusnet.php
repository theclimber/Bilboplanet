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
function getWidget() {
	$scripts = array();
	$scripts[] = 'widgets/live_statusnet/jquery.livetwitter.js';
	$scripts[] = 'widgets/live_statusnet/jquery.toastmessage.js';
	$scripts[] = 'widgets/live_statusnet/live_statusnet.js';
	$css = array();
	$css[] = 'widgets/live_statusnet/jquery.toastmessage.css';
	$css[] = 'widgets/live_statusnet/live_statusnet.css';

	$html = '<div id="statusnetUserTimeline" class="tweets"></div>';
	$html .= "<script type=\"text/javascript\">
		$('#statusnetUserTimeline').liveTwitter('planetlibre', {limit: 5, rate: 30000, refresh: true, mode: 'home_timeline', service:'status.planet-libre.org', showAuthor: true}, function(divName, count) {
			if (show_notice_at_start) {
				for (var i = 0; i < count; i++) {
					var newTw = this.container.children[i].textContent;
					showStickySuccessToast(newTw);
				}
			} else {
				show_notice_at_start = 1;
			}
		});
	</script>";

	return array("id" => "live_statusnet",
		"title" => "En live depuis statusnet",
		"html" => $html,
		"scripts" => $scripts,
		"styles" => $css
		);
}
?>
