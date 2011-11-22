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
?><?php
if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# MANAGE COMMENTS ON FEED
##########################################################
	case 'comment':
		$user_id = $core->auth->userID();
		$feed_id = $_POST['feed_id'];
		$status = $_POST['status'];

		$sql = "SELECT feed_status, feed_comment, feed_id
			FROM ".$core->prefix."feed
			WHERE feed_id = ".$feed_id;
		$rs = $core->con->select($sql);

		if ($rs->count() > 0){
			if ($rs->f('post_comment') == $status) {
				$error[] = T_('Nothing to do');
			}
			else {
				$cur = $core->con->openCursor($core->prefix."feed");
				$cur->feed_comment = $status;
				$cur->update("WHERE feed_id = $feed_id");
				$output = T_("Feed successfully updated");
			}
		}

		if (!empty($error)) {
			$output .= "<ul>";
			foreach($error as $value) {
				$output .= "<li>".$value."</li>";
			}
			$output .= "</ul>";
			print '<div class="flash_error">'.$output.'</div>';
		}
		else {
			print '<div class="flash_notice">'.$output.'</div>';
		}
		break;


##########################################################
# DEFAULT RETURN
##########################################################
	default:
		print '<div class="flash_error">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}

?>
