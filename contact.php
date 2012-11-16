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
# If contact page is disable
if(!$blog_settings->get('planet_contact_page')) {
	http::redirect('index.php');
}
$scripts = array();
$scripts[] = "javascript/functions.js";
include dirname(__FILE__).'/tpl.php';#
header('Content-type: text/html; charset=utf-8');

$flash='';
if(isset($_POST) && isset($_POST['submit'])){
	require_once(dirname(__FILE__).'/inc/lib/recaptchalib.php');
	$privatekey = "6LdEeQgAAAAAABrweqchK5omdyYS_fUeDqvDRq3Q";
	$captcha = recaptcha_check_answer (
		$privatekey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);
	if (!$captcha->is_valid) {
		$flash = array('type' => 'error', 'msg' => sprintf(T_("The reCAPTCHA wasn't entered correctly. Go back and try it again. (reCAPTCHA said: %s)"),$captcha->error));
	} else {
		# On recupere les infos
		$name = check_field('name',trim($_POST['name']),'not_empty');
		$email = check_field('email',trim($_POST['email']),'email');
		$subject = check_field('subject',trim($_POST['subject']),'not_empty');
		$content = check_field('content',trim($_POST['content']),'not_empty');
		$ip     = getIP();

		if ($name['success'] && $email['success'] && $subject['success'] && $content['success']){

			# Construction du mail
			$objet = "Contact: ".$subject['value'];
			$msg = T_("Name/Nickname : ").$name['value'];
			$msg .= "\n".T_("Email : ").$email['value'];
			$msg .= "\n".T_("Subject : ").$subject['value'];
			$msg .= "\n".T_("Content of the message: ").$content['value'];
			$msg .= "\nIP : $ip";

			# Envoi du mail
			$envoi = sendmail($email['value'], $blog_settings->get('author_mail'), $objet, $msg);

			# Message d'information
			if($envoi) {
				$flash = array('type' => 'notice', 'msg' => T_("Your email has been sent !"));
			} else {
				$flash = array('type' => 'error', 'msg' => T_("Your request could not be sent for an unknown reason.<br/>Please try again."));
			}
		}
		else {
			if(!$name['success']){
				$flash = array('type' => 'error', 'msg' => $name['error']);
			}
			if(!$email['success']){
				$flash = array('type' => 'error', 'msg' => $email['error']);
			}
			if(!$subject['success']){
				$flash = array('type' => 'error', 'msg' => $subject['error']);
			}
			if(!$content['success']){
				$flash = array('type' => 'error', 'msg' => $content['error']);
			}
		}
	}
}

if (!empty($flash)) {
	$msg = '<div class="flash'.$flash['type'].'">'.$flash['msg'].'</div>';
	$msg .= "<div class='informations'><h2>".T_("In case of problem")."</h2><p>";
	$msg .= sprintf(T_("If you do not recieve any confirmation from the administration team in the 5 days, do not hesitate to contact us by email at %s"), $blog_settings->get('author_mail'));
	$msg .= "</p></div>";
	$core->tpl->setVar('flashmsg', $msg);
	$core->tpl->render('contact.flash');
}

require_once(dirname(__FILE__).'/inc/lib/recaptchalib.php');
$publickey = "6LdEeQgAAAAAACLccbiO8TNaptSmepfMFEDL3hj2";
$captcha_html = recaptcha_get_html($publickey);

$form_values = array(
	"name" => "",
	"email" => "",
	"subject" => "",
	"content" => "",
);
if($name)		$form_values["name"] = $name['value'];
if($email)		$form_values["email"] = $email['value'];
if($subject)	$form_values["subject"] = $subject['value'];
if($content)	$form_values["content"] = $content['value'];

$core->tpl->setVar('params', $params);
$core->tpl->setVar('captcha_html', $captcha_html);
$core->tpl->setVar('form', $form_values);
$core->tpl->render('content.contact');
$core->renderTemplate();
?>
