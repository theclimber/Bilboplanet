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
if(isset($_GET['action'])) {
	switch (trim($_GET['action'])){

##########################################################
# VALIDATE EMAIL
##########################################################
	case 'validate':
		$token = trim($_GET['user']);
		if ($token != "") {
			$rs_user = $core->con->select("SELECT user_id,user_fullname,user_email,created
				FROM ".$core->prefix."user
				WHERE user_token='".$token."'");
			if ($rs_user->count() == 1) {
				if ($rs_user->f('created') > time()+3*24*3600) {
					$error[] = T_("This link is expired");
				}
			} else {
				$error[] = T_("Invalid link");
			}
		} else {
			$error[] = T_("Invalid link token");
		}

		if (empty($error)) {
			$cur = $core->con->openCursor($core->prefix.'user');
			$cur->user_status = 1;
			$cur->modified = array(' NOW() ');
			$cur->update("WHERE user_id='".$rs_user->f('user_id')."'");
			$output = T_("Thank you for validating your account !");


			# send acceptation email :
			$subject = sprintf(T_("Welcome on the %s"), $blog_settings->get('planet_title'));
			$to = $rs_user->f('user_email');

			$msg .= sprintf(T_("Dear %s,"), $rs_user->f('user_fullname'));
			$msg .= "\n\n".T_("Congratulations ! Your account was created and validated. You are now able to connnect on the planet and to change your settings. Feel free to do so and discover all the nice features that you can enable for a better surf!");
			$msg .= "\n- ".T_("Change your user settings");
			$msg .= "\n- ".T_("Enable the social networks links that you want to use (Google+, Twitter, Shaarli)");
			$msg .= "\n- ".T_("Share your blog and your feeds");
			$msg .= "\n- ".T_("Add tags on your post blogs to get them in the right tribes");
			$msg .= "\n- ".T_("Create your own tribes and personalised filtered feeds");
			$msg .= "\n".T_("To login into your account, go on the following page : ");
			$msg .= $blog_settings->get('planet_url')."/auth.php";
			$msg .= "\n\n".T_("We hope you'll enjoy being on this planet and being part of our community.");
			$msg .= "\n\n".T_("Thank you");
			$msg .= "\n".$blog_settings->get('author');

			sendmail($blog_settings->get('author_mail'), $to, $subject, $msg);
		}

		
		if (!empty($error)) {
			$output .= "<ul>";
			foreach($error as $value) {
				$output .= "<li>".$value."</li>";
			}
			$output .= "</ul>";
			$output = '<div class="flash_error">'.$output.'</div>';
		}
		else {
			$output = '<div class="flash_notice">'.$output.'</div>';
		}
        header('Content-type: text/html; charset=utf-8');
        print '<html><meta http-equiv="refresh" content="10; URL='.BP_PLANET_URL.'"><body>';
        print $output;
        print "</body></html>";
		break;
		
##########################################################
# DEFAULT RETURN
##########################################################
	default:
		print '<div class="flash_error">'.T_('User bad call').'</div>';
		break;
	}
} elseif(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# UPDATE ACCOUNT
##########################################################
	case 'update':
		$user_id = $core->auth->userID();
		$user = $core->con->select("SELECT * FROM ".$core->prefix."user WHERE user_id = '$user_id'");

		$new_fullname = check_field(T_('Fullname'), trim($_POST['efullname']));
		$new_email = check_field(T_('Email'), trim($_POST['eemail']), 'email');
		$new_password = check_field('password', array("password" => trim($_POST['password']), "password2" => trim($_POST['password2'])), 'password', false);
		$new_lang = !empty($_POST['elang']) ? trim($_POST['elang']) : 'en';

		$error = array();

		if ($new_email['success']
			&& $new_fullname['success']
			&& $new_password['success'])
		{
			$new_fullname['value'] = htmlentities($new_fullname['value'],ENT_QUOTES,mb_detect_encoding($new_fullname['value']));

			$sql = "SELECT user_id, user_fullname, user_email, user_lang FROM ".$core->prefix."user
				WHERE user_id != '".$user_id."'
				AND (user_fullname = '".$new_fullname['value']."'
				OR user_email = '".$new_email['value']."')";
			$rs1 = $core->con->select($sql);
			if ($rs1->count() > 0){
				if ($rs1->f('user_fullname') == $new_fullname['value']) {
					$error[] = sprintf(T_('The user %s already exists'),$new_fullname['value']);
				}
				if ($rs1->f('user_email') == $new_email['value']) {
					$error[] = sprintf(T_('The email address %s is already in use by %s'),$new_email['value'], $rs1->f('user_id'));
				}
			}

			if (empty($error)) {
				$cur = $core->con->openCursor($core->prefix.'user');
				$cur->user_fullname = $new_fullname['value'];
				$cur->user_email = $new_email['value'];
				if (!empty($new_password['value'])) {
					$cur->user_pwd = crypt::hmac('BP_MASTER_KEY',$new_password['value']);
				}
				$cur->user_lang = $new_lang;
				$cur->modified = array(' NOW() ');
				$cur->update("WHERE user_id = '$user_id'");

				$output = sprintf(T_("User %s successfully updated"),$new_id['value']);
			}
		} else {
			if (!$new_fullname['success']) {
				$error[] = $new_fullname['error'];
			}
			if (!$new_email['success']) {
				$error[] = $new_email['error'];
			}
			if (!$new_password['success']) {
				$error[] = $new_password['error'];
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
