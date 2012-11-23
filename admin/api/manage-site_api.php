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
# GET SITE INFO
##########################################################
	case 'info':
		$site_id = urldecode(trim($_POST['site_id']));
		$rs = $core->con->select("SELECT * FROM ".$core->prefix."site WHERE site_id = '$site_id'");
		$site = array(
			"site_id" => $rs->f('site_id'),
			"site_name" => $rs->f('site_name'),
			"site_url" => $rs->f('site_url'),
			"site_status" => $rs->f('site_status')
			);
		print json_encode($site);
		break;

##########################################################
# ADD SITE TO USER
##########################################################
	case 'add':
		$user_id = trim($_POST['user_id']);
		$site_url = check_field('site_url',trim($_POST['s_url']), 'url');
		$site_name = check_field('site_name',trim($_POST['s_name']));
		$error = array();

		if ($site_url['success'] && $site_name['success']) {
			$rs = $core->con->select("SELECT * FROM ".$core->prefix."site
				WHERE site_url = '".$site_url['value']."'");
			if ($rs->count() > 0){
				if ($rs->f('user_id') == $user_id) {
					$error[] = sprintf(T_('The user %s already own the website %s'),$user_id, $site_url['value']);
				}
				else {
					$error[] = sprintf(T_('The website %s owns to user %s'), $site_url['value'], $rs->f('user_id'));
				}
			}

			if (empty($error)) {
				# Get next ID
				$rs3 = $core->con->select(
					'SELECT MAX(site_id) '.
					'FROM '.$core->prefix.'site ' 
					);
				$next_site_id = (integer) $rs3->f(0) + 1;
				$cur = $core->con->openCursor($core->prefix.'site');
				$cur->site_id = $next_site_id;
				$cur->user_id = $user_id;
				$cur->site_url = $site_url['value'];
				$cur->site_name = $site_name['value'];
				$cur->created = array(' NOW() ');
				$cur->modified = array(' NOW() ');
				$cur->insert();

				$output = sprintf(T_("Website %s successfully added to user %s"), $site_url['value'], $user_id);
			}
		}
		else {
			if (!$site_url['success']) {
				$error[] = $site_url['error'];
			}
			if (!$site_name['success']) {
				$error[] = $site_name['error'];
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
# UPDATE SITE
##########################################################
	case 'update':
		$site_id = trim($_POST['site_id']);
		$site_url = check_field('site_url',trim($_POST['esite_url']), 'url');
		$site_name = check_field('site_name',trim($_POST['esite_name']));
		$error = array();

		if ($site_url['success'] && $site_name['success']) {
			$rs = $core->con->select("SELECT * FROM ".$core->prefix."site
				WHERE site_url = '".$site_url['value']."'
				AND site_id != ".$site_id);
			if ($rs->count() > 0){
				if ($rs->f('user_id') == $user_id) {
					$error[] = sprintf(T_('The user %s already own the website %s'),$user_id, $site_url['value']);
				}
				else {
					$error[] = sprintf(T_('The website %s is owned by user %s'), $site_url['value'], $rs->f('user_id'));
				}
			}

			if (empty($error)) {
				$cur = $core->con->openCursor($core->prefix.'site');
				$cur->site_url = $site_url['value'];
				$cur->site_name = $site_name['value'];
				$cur->modified = array(' NOW() ');
				$cur->update("WHERE site_id = ".$site_id);

				$output = sprintf(T_("Website %s successfully updated"), $site_url['value']);
			}
		}
		else {
			if (!$site_url['success']) {
				$error[] = $site_url['error'];
			}
			if (!$site_name['success']) {
				$error[] = $site_name['error'];
			}
		}

		if (!empty($error)) {
			$output .= "<ul>";
			foreach($error as $value) {
				$output .= "<li>".$value."</li>";
			}
			$output .= "</ul>";
			print '<div class="flash error">'.$output.'</div>';
		}
		else {
			print '<div class="flash notice">'.$output.'</div>';
		}
		break;

##########################################################
# TOGGLE SITE STATUS
##########################################################
	case 'toggle':
		$site_id = trim($_POST['site_id']);
		$site = $core->con->select("SELECT site_status FROM ".$core->prefix."site WHERE site_id = '$site_id'");
		
		$cur = $core->con->openCursor($core->prefix.'site');
		if($site->f('site_status') == 1) {
			$cur->site_status = 0;
		} else {
			$cur->site_status = 1;
		}
		$cur->update("WHERE site_id = '$site_id'");

		print '<div class="flash_notice">'.T_('Site status toggled').'</div>';
		break;

##########################################################
# REMOVE SITE
##########################################################
	case 'remove':
		$site_id = trim($_POST['site_id']);
		$rs = $core->con->select("SELECT site_name, site_url FROM ".$core->prefix."site WHERE site_id = '$site_id'");
		$confirmation = "<p>".sprintf(T_('Are you sure you want to remove site %s ?'),
			'<a href="'.$rs->f('site_url').'" target="_blank">'.$rs->f('site_name').'</a>')."?<br/>";
		$confirmation .= "<ul><li>".T_('This action can not be canceled')."</li>";
		$confirmation .= "<li>".T_('All the posts comming from this site will be removed')."</li>";
		$confirmation .= "<li>".T_('All the feeds of this site will be removed')."</li></ul><br/>";
		$confirmation .= "<form id='removeSiteConfirm_form' method='POST'><input type='hidden' name='site_id' value='".$site_id."'/>";
		$confirmation .= "<div class='button br3px'><input class='reset' type='button' value='".T_('Reset')."'/></div>&nbsp;&nbsp;";
		$confirmation .= "<div class='button br3px'><input class='valide' type='submit' name='confirm' value='".T_('Confirm')."'/></div></form></p>";
		print '<div class="flash_warning">'.$confirmation.'</div>';
		break;

##########################################################
# CONFIRM REMOVE SITE
##########################################################
	case 'removeConfirm':
		sleep(1);
		$site_id = trim($_POST['site_id']);
		$rs2 = $core->con->select("SELECT * FROM ".$core->prefix."site WHERE site_id = '$site_id'");
		$rs = $core->con->select("SELECT feed_id FROM ".$core->prefix."feed WHERE site_id = '$site_id'");
		if ($rs->count() >0) {
			while($rs->fetch()) {
//				$core->con->execute("DELETE FROM ".$core->prefix."post WHERE feed_id ='$rs->feed_id'");
				$core->con->execute("DELETE FROM ".$core->prefix."feed WHERE feed_id ='$rs->feed_id'");
			}
		}
		$core->con->execute("DELETE FROM ".$core->prefix."site WHERE site_id ='$site_id'");
		print '<div class="flash_notice">'.sprintf(T_("Delete of site %s succeeded"),$rs2->f('site_url')).'</div>';
		break;

##########################################################
# GET SITE OF USER
##########################################################
	case 'get-user-site':
		$user_id = trim($_POST['user_id']);
		$rs = $core->con->select("SELECT * FROM ".$core->prefix."site WHERE user_id = '$user_id'");
		$sites = array();
		while($rs->fetch()) {
			$sites[$rs->site_id] = array(
				'site_url' => $rs->site_url,
				'site_name' => $rs->site_name);
		}
		print json_encode($sites);
		break;

	default:
		print '<div class="flash_error">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}
?>
