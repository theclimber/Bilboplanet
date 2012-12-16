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

if ($core->auth->sessionExists()){
	http::redirect(BP_PLANET_URL.'/index.php');
	exit;
}

$scripts = array();
$scripts[] = "javascript/functions.js";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

$form_values = array(
	"user_id" => "",
	"fullname" => "",
	"email" => "",
	"password" => ""
);
$flash='';
session_start();
require_once(dirname(__FILE__).'/inc/lib/recaptchalib.php');
$privatekey = "6LdEeQgAAAAAABrweqchK5omdyYS_fUeDqvDRq3Q";
$publickey = "6LdEeQgAAAAAACLccbiO8TNaptSmepfMFEDL3hj2";

if(isset($_POST) && isset($_POST['submit'])){
	$captcha = recaptcha_check_answer (
		$privatekey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);

	# On recupere les infos
	$user_id = check_field('user_id',trim($_POST['user_id']),'not_empty');
	$fullname = check_field('fullname',trim($_POST['fullname']),'',false);
	$email = check_field('email',trim($_POST['email']),'email');
	$password = check_field('password',trim($_POST['pass']), 'not_empty');

	if($user_id)	$form_values["user_id"] = $user_id['value'];
	if($fullname)	$form_values["fullname"] = $fullname['value'];
	if($email)		$form_values["email"] = $email['value'];
	if($password)	$form_values["password"] = $password['value'];

	if (!$captcha->is_valid) {
		$flash = array('type' => 'error', 'msg' => sprintf(T_("The reCAPTCHA wasn't entered correctly. Go back and try it again. (reCAPTCHA said: %s)"),$captcha->error));
	} else {
		$ip = getIP();
		if ($user_id['success'] && $fullname['success'] && $email['success']){

			$token = generateUserToken($fullname,$email,$password);
			$validation_url = BP_PLANET_URL."/user/api/index.php?ajax=account&action=validate&user=".$token;

			# Build email
			$objet = sprintf(T_("Signup of user %s,"),$user_id['value']);
			$bmsg = T_("User id :")." ".$user_id['value'];
			$bmsg .= "\n".T_("Fullname :")." ".$fullname['value'];
			$bmsg .= "\n".T_("Email :")." ".$email['value'];
			$bmsg .= "\nIP : $ip";
			$msg .= $bmsg;
			$msg .= "\n\n".T_("Please validate your account by going on the following link :");
			$msg .= "\n ".$validation_url;
			$msg .= "\n\n".T_("NOTE: the link will expire in 3 days.");
			$msg .= "\n\n".T_("Thank you");
			$msg .= "\n".$blog_settings->get('author');
			# TODO : the mail should contain a special token to signup

			# Send email to new user to confirm email
			$envoi1 = sendmail($blog_settings->get('author_mail'), $email['value'], $objet, $msg);

			$bmsg = T_("A new user registered an account on the planet :")." \n\n".$bmsg;
			# Send email to planet author
			$envoi2 = sendmail($email['value'], $blog_settings->get('author_mail'), $objet, $bmsg);

			# Information message
			if($envoi1 && $envoi2) {

				$addPendingUser = addUserSignup($user_id['value'], $fullname['value'], $email['value'], $password['value'], $blog_settings->get('planet_lang'), $token);

				# Check error
				if (empty($addPendingUser)) {
					$flash = array('type' => 'notice', 'msg' => T_("Thank you for your registration."));
				} else {
					$flash = array('type' => 'error', 'msg' => T_("There was an error,
						please report to the website administrator if the same error occurs several times :\n"));
					foreach($addPendingUser as $value) {
						$flash['msg'] .= "<br/>".$value;
					}
				}

			} else {
				$flash = array(
					'type' => 'error',
					'msg' => sprintf(T_("Your request could not be sent for an unknown reason.<br/>
					Please try again or send an email manually to %s."), $blog_settings->get('planet_mail'))
					);
			}

		}
		else {
			if(!$user_id['success']){
				$flash = array('type' => 'error', 'msg' => $user_id['error']);
			}
			if(!$fullname['success']){
				$flash = array('type' => 'error', 'msg' => $fullname['error']);
			}
			if(!$email['success']){
				$flash = array('type' => 'error', 'msg' => $email['error']);
			}
			if(!$password['success']){
				$flash = array('type' => 'error', 'msg' => $password['error']);
			}
		}
	}
}

if(!$blog_settings->get('planet_subscription')) {
	$content = "<img src=\"themes/".$blog_settings->get('planet_theme')."/images/closed.png\" />";
	$core->tpl->setVar('html', $content);
	$core->tpl->render('content.html');
	$core->renderTemplate();
	exit;
}
else {
	if (!empty($flash)) {
		$msg = '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>';
		if ($flash['type'] != "error") {
			$msg .= "<div class='informations'><h2 class='informations'>".T_("In case of problem")."</h2>";
			$msg .= "<p>".sprintf(T_("If you don't recieve any news from the administration team within the 5 days do not hesitate to contact us via %s with this information :"),$blog_settings->get('planet_mail'))."<br /><ul>";
			$msg .= "<li><b>".T_("Subject")."</b> : ".$blog_settings->get('planet_title') ." - ".$choice['value']."</li>";
			$msg .= "<li><b>".T_("Username")."</b> : ".$user_id['value']."</li>";
			$msg .= "<li><b>".T_("Fullname")."</b> : ".$fullname['value']."</li>";
			$msg .= "<li><b>".T_("Email")."</b> : ".$email['value']."</li>";
			$msg .= "</ul></p></div>";
			$msg .= T_("You should have recieved a confirmation email. Please validate and then you'll be able to connect : ")."<a href='".BP_PLANET_URL."/auth.php'>here</a>";
		}
		$core->tpl->setVar('flashmsg', $msg);
		$core->tpl->render('signup.flash');

	}

	$content = $blog_settings->get('planet_subscription_content');
	$content = stripslashes($content);
	$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
	$content = code_htmlentities($content, 'code', 'code', 1);

	$captcha_html = recaptcha_get_html($publickey);

	$core->tpl->setVar('params', $params);
	$core->tpl->setVar('form', $form_values);
	$core->tpl->setVar('subscription_content', $content);
	$core->tpl->setVar('captcha_html', $captcha_html);
	$core->tpl->render('content.signup');
	$core->renderTemplate();

}

#---------------------------------------------------#
# Function to add pending user			    #
#---------------------------------------------------#
function addUserSignup($user_id, $user_fullname, $user_email, $password, $lang, $token) {

	global $core;

	# Clean Up user_id
	$user_id = preg_replace("( )", "_", $user_id);
	$user_id = cleanString($user_id);

	# Check if user's information already exist in not pending users
	$rs1 = $core->con->select("SELECT user_id, user_fullname, user_email
		FROM ".$core->prefix."user
		WHERE lower(user_id) = '".strtolower($user_id)."'
		OR lower(user_fullname) = '".strtolower($user_fullname)."'
		OR lower(user_email) = '".strtolower($user_email)."'");
	if ($rs1->count() > 0){
		if ($rs1->f('user_id') == $user_id) {
			$error[] = sprintf(T_('The user %s already exists'),$user_id);
		}
		if ($rs1->f('user_fullname') == $user_fullname) {
			$error[] = sprintf(T_('The user %s already exists'),$user_fullname);
		}
		if ($rs1->f('user_email') == $user_email) {
			$error[] = sprintf(T_('The email address %s is already in use'),$user_email);
		}
	} else {
		# Check if website is already in use
		$rs2 = $core->con->select("SELECT ".$core->prefix."user.user_id
			FROM ".$core->prefix."user, ".$core->prefix."site
			WHERE ".$core->prefix."site.user_id = ".$core->prefix."user.user_id
			AND site_url = '".$url."'");
		if ($rs2->count() > 0){
			$error[] = sprintf(T_('The website %s is already assigned to the user %s'),$url, $user_id);
		}
	}

	# All OK
	if (empty($error)) {
		$cur = $core->con->openCursor($core->prefix.'user');
		$cur->user_id = $user_id;
		$cur->user_fullname = $user_fullname;
		$cur->user_email = $user_email;
		$cur->user_pwd = crypt::hmac('BP_MASTER_KEY', $password);
		$cur->user_token = $token;
		$cur->user_status = 0;
		$cur->user_lang = $lang;
		$cur->created = array(' NOW() ');
		$cur->modified = array(' NOW() ');
		$cur->insert();
	}

	return $error;
}
?>
