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
# Add a new feed
##########################################################
	case 'add_feed':
		$user_id = '';
		if ($core->auth->sessionExists()){
			$user_id = $core->auth->userID();
		}

		$feed_url = check_field('feed',urldecode(trim($_POST['feed_url'])),'url');
		$site_url = check_field('website',urldecode(trim($_POST['site_url'])),'url');

		if ($feed_url['success'] || $site_url['success']) {
			$error[] = T_('This feed or site is not a valid URL.');
		}

		# Check if feed is not yet in pending feeds
		$sql = "SELECT user_id, site_url, feed_url
			FROM ".$core->prefix."pending_feed
            WHERE feed_url = '".$feed_url['value']."';";
		$rs = $core->con->select($sql);

		if ($rs->count() > 0) {
			$error[] = T_('This feed is already waiting for validation.');
		} else {
			# check if feed is not yet in existing feeds
			$sql1 = "SELECT feed_url, user_id
				FROM ".$core->prefix."feed
				WHERE feed_url = ".$feed_url.";";
			$rs1 = $core->con->select($sql);
			if ($rs1->count() > 0) {
				$error[] = sprintf(T_('This feed is already used in this planet by user %s'), $rs1->f('user_id'));
			} else {
				$cur = $core->con->openCursor($core->prefix.'pending_feed');
				$cur->user_id = $user_id;
				$cur->site_url = $site_url['value'];
				$cur->feed_url = $feed_url['value'];
				$cur->created = array(' NOW() ');
				$cur->insert();
				$output .= T_("Feed waiting for validation");

				$rs_user = $core->con->select("SELECT * FROM ".$core->prefix."user WHERE user_id = '".$user_id."'");

				$objet = "[".$blog_settings->get('planet_name')."] ".
					sprintf(T_("Feed validation request for "),$user_id);
				$msg = T_("User id : ").$user_id;
				$msg .= "\n".T_("Fullname : ").$rs_user->f('user_fullname');
				$msg .= "\n".T_("Site url : ").$site_url['value'];
				$msg .= "\n".T_("Feed url : ").$feed_url['value'];
				$msg .= "\nIP : $ip";

				# Send email to planet author
				$envoi = sendmail($rs_user->f('user_email'), $blog_settings->get('author_mail'), $objet, $msg);

				# Information message
				if($envoi) {
					$output .= "<br/>".T_("An email was sent to the site administrator to ask for validation.");
				} else {
					$output .= "<br/>".T_("The email could not be sent to the site administrator for validation.");
				}
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
# Remove a feed
##########################################################
	case 'rm_feed':
		$user_id = '';
		if ($core->auth->sessionExists()){
			$user_id = $core->auth->userID();
		}
		$feed_id = trim($_POST['feed_id']);

		// check if feed exists
		$sql = "SELECT feed_id, feed_url FROM ".$core->prefix."feed
			WHERE user_id='".$user_id."'
			AND feed_id=".$feed_id."";
		$rs = $core->con->select($sql);
		if ($rs->count() > 0) {
			// we can remove feed
		    $core->con->execute(
                "DELETE FROM ".$core->prefix."feed WHERE feed_id = '$feed_id'
                    AND user_id = '".$user_id."'");
			$output .= T_("Feed removed : ").$rs->f('feed_url');
		} else {
			// the feed seems unexisting
			$error[] = sprintf(T_('This feed does not exist'));
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
# Remove a pending feed
##########################################################
	case 'rm_pending_feed':
		$user_id = '';
		if ($core->auth->sessionExists()){
			$user_id = $core->auth->userID();
		}
		$feed_url = urldecode(trim($_POST['feed_url']));

		// check if feed exists
		$sql = "SELECT user_id, feed_url FROM ".$core->prefix."pending_feed
			WHERE user_id='".$user_id."'
			AND feed_url='".$feed_url."'";
		print $sql;
		$rs = $core->con->select($sql);
		if ($rs->count() > 0) {
			// we can remove feed
		    $core->con->execute(
                "DELETE FROM ".$core->prefix."pending_feed WHERE feed_url = '$feed_url'
                    AND user_id = '".$user_id."'");
			$output .= T_("Feed removed : ").$feed_url;
		} else {
			// the feed seems unexisting
			$error[] = sprintf(T_('This feed does not exist'));
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
