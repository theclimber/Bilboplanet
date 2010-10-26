<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
* Blog : blog.bilboplanet.com
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
$scripts = array();
$scripts[] = "javascript/functions.js";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');
	
$flash='';
session_start();
print_r($_POST);
if(isset($_POST) && isset($_POST['submit'])){
	require_once(dirname(__FILE__).'/inc/lib/recaptchalib.php');
	$privatekey = "6LdEeQgAAAAAABrweqchK5omdyYS_fUeDqvDRq3Q";
	$captcha = recaptcha_check_answer (
		$privatekey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);

	# On recupere les infos
	$user_id = check_field('user_id',trim($_POST['user_id']),'not_empty');
	$fullname = check_field('fullname',trim($_POST['fullname']),'',false);
	$email = check_field('email',trim($_POST['email']),'email');
	$url = check_field('url',trim($_POST['url']),'url');
	$feed = check_field('flux',trim($_POST['feed']),'feed');
	$choice = check_field('choice',trim($_POST['choice']),'not_empty');
	$charter = check_field('charter',trim($_POST['ok']),'not_empty');
	if (!$captcha->is_valid) {
		$flash = array('type' => 'error', 'msg' => sprintf(T_("The reCAPTCHA wasn't entered correctly. Go back and try it again. (reCAPTCHA said: %s)"),$captcha->error));
	} else {
		$ip = getIP();

		if ($user_id['success'] && $fullname['success'] && $email['success'] && $url['success'] && $feed['success'] && $choice['success'] && $charter["success"]){
			# Construction du email
			$objet = $choice['value'];
			$msg = T_("Name : ").$user_id['value'];
			$msg .= "\n".T_("Firstname : ").$fullname['value'];
			$msg .= "\n".T_("Email : ").$email['value'];
			$msg .= "\n".T_("Website : ").$url['value'];
			$msg .= "\n".T_("Feed : ").$feed['value'];
			$msg .= "\n".T_("Choice : ").$choice['value'];
			$msg .= "\nIP : $ip";

			# Envoi du email
			$envoi = sendmail($email['value'], $blog_settings->get('author_mail'), $objet, $msg);

			# Message d'information
			if($envoi) {
				$flash = array('type' => 'notice', 'msg' => T_("Your email has been sent"));
			} else {
				$flash = array('type' => 'error', 'msg' => T_("Your request could not be sent for an unknown reason.<br/>Please try again."));
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
			if(!$url['success']){
				$flash = array('type' => 'error', 'msg' => $url['error']);
			}
			if(!$feed['success']){
				$flash = array('type' => 'error', 'msg' => $feed['error']);
			}
			if(!$choice['success']){
				$flash = array('type' => 'error', 'msg' => $choice['error']);
			}
			if(!$charter['success']){
				$flash = array('type' => 'error', 'msg' => $charter['error']);
			}
		}
	}
}

if(!$blog_settings->get('planet_subscription')) {
	$content = "<img src=\"themes/".$blog_settings->get('planet_theme')."/images/closed.png\" />";
	$core->tpl->setVar('html', $content);
	$core->tpl->render('content.html');
	echo $core->tpl->render();
	exit;
}
else {
	if (!empty($flash)) {
		$msg = '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>';
		if ($flash['type'] != "error") {
			$msg .= "<div class='informations'><h2 class='informations'>".T_("In case of problem")."</h2>";
			$msg .= "<p>".sprintf(T_("If you don't recieve any new from the administration team in the 5 days do not hesitate to contact us via %s with this information :"),$blog_settings->get('planet_mail'))."<br /><ul>";
			$msg .= "<li><b>".T_("Subject")."</b> : ".$blog_settings->get('planet_title') ." - ".$choice['value']."</li>";
			$msg .= "<li><b>".T_("Username")."</b> : ".$user_id['value']."</li>";
			$msg .= "<li><b>".T_("Fullname")."</b> : ".$fullname['value']."</li>";
			$msg .= "<li><b>".T_("Email")."</b> : ".$email['value']."</li>";
			$msg .= "<li><b>".T_("Website")."</b> : ".$url['value']."</li>";
			$msg .= "<li><b>".T_("Feed")."</b> : ".$feed['value']."</li>";
			$msg .= "<li><b>".T_("Choice")."</b> : ".$choice['value']."</li>";
			$msg .= "</ul></p></div>";
		}
		$core->tpl->setVar('flashmsg', $msg);
		$core->tpl->render('subscription.flash');
	}

	$content = html_entity_decode(stripslashes($blog_settings->get('planet_subscription_content')), ENT_QUOTES, 'UTF-8');

	require_once(dirname(__FILE__).'/inc/lib/recaptchalib.php');
	$publickey = "6LdEeQgAAAAAACLccbiO8TNaptSmepfMFEDL3hj2";
	$captcha_html = recaptcha_get_html($publickey);

	$form_values = array(
		"user_id" => "",
		"fullname" => "",
		"email" => "",
		"url" => "",
		"feed" => "",
	);
	if($user_id)	$form_values["user_id"] = $user_id['value'];
	if($fullname)	$form_values["fullname"] = $fullname['value'];
	if($email)	$form_values["email"] = $email['value'];
	if($url)	$form_values["url"] = $url['value'];
	if($feed)	$form_values["feed"] = $feed['value'];

	$core->tpl->setVar('form', $form_values);
	$core->tpl->setVar('subscription_content', $content);
	$core->tpl->setVar('captcha_html', $captcha_html);
	$core->tpl->render('content.subscription');
	echo $core->tpl->render();
}
?>
