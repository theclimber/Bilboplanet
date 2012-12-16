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
# UPDATE USER CONFIG
##########################################################
	case 'update':
		$user_id = $core->auth->userID();

		$newsletter = $_POST['newsletter'];
		$twitter = $_POST['twitter']=='true' ? 1 : 0;
		$google = $_POST['google']=='true' ? 1 : 0;
		$shaarli = $_POST['shaarli']=='true' ? 1 : 0;
		$shaarli_type = $_POST['shaarli-type'];
		if (!in_array($shaarli_type, array('local', 'remote'))) {
			$shaarli = 0;
		}
		$statusnet = $_POST['statusnet']=='true' ? 1 : 0;
		$statusnet_account = check_field(T_('Statusnet Account'),$_POST['statusnet-account'], 'url');
		$reddit = $_POST['reddit']=='true' ? 1 : 0;

		if (!in_array($newsletter,array('nomail','dayly','weekly','monthly'))) {
			$error[] = T_('Error detected');
		}
		if ($statusnet == 1) {
			if (!$statusnet_account['success']) {
				$error[] = T_("Please check statusnet URL : Invalid URL");
			}
		}
		$shaarli_instance = '';
		if ($shaarli == 1) {
			if ($shaarli_type == 'remote') {
				$instance = check_field(T_('Shaarli instance'),$_POST['shaarli-instance'], 'url');
				if (!$instance['success']) {
					$error[] = T_("Please check shaarli URL : Invalid URL");
				} else {
					$shaarli_instance = $instance['value'];
				}
			} else {
				$shaarli_instance = BP_PLANET_URL.'/shaarli/?user='.$user_id;
			}
		}

		if (empty($error)) {
			$user_settings->put(
				'social.newsletter', $newsletter, 'string');
			$user_settings->put(
				'social.twitter', $twitter, 'boolean');
			$user_settings->put(
				'social.google', $google, 'boolean');
			$user_settings->put(
				'social.shaarli', $shaarli, 'boolean');
			if ($shaarli == 1) {
				$user_settings->put(
					'social.shaarli.type', $shaarli_type, 'string');
				$user_settings->put(
					'social.shaarli.instance', $shaarli_instance, 'string');
			}
			$user_settings->put(
				'social.statusnet', $statusnet, 'boolean');
			if ($statusnet == 1) {
				$user_settings->put(
					'social.statusnet.account', $statusnet_account['value'], 'string');
			}
			$user_settings->put(
				'social.reddit', $reddit, 'boolean');

			$output = T_('Social settings successfully configured.');
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
