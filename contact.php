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
require_once(dirname(__FILE__).'/inc/i18n.php');
require_once(dirname(__FILE__).'/inc/fonctions.php');
$flash='';
global $error;
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
		$nom = check_field('nom',trim($_POST['nom']),'not_empty');
		$mail = check_field('email',trim($_POST['email']),'email');
		$titre = check_field('titre',trim($_POST['titre']),'not_empty');
		$content = check_field('content',trim($_POST['content']),'not_empty');
		$ip     = getIP();

		if ($nom['success'] && $mail['success'] && $titre['success'] && $content['success']){

			# Construction du mail
			$objet = $planet_title." - ".$titre['value'];
			$msg = T_("Name/Nickname : ").$nom['value'];
			$msg .= "\n".T_("Email : ").$mail['value'];
			$msg .= "\n".T_("Subject : ").$titre['value'];
			$msg .= "\n".T_("Content of the message: ").$content['value'];
			$msg .= "\nIP : $ip";

			# Envoi du mail
			$envoi= mail($planet_author_mail, $objet, $msg,"From: ".$mail['value']."\r\nReply-To: ".$mail['value']."\r\n");

			# Message d'information
			if($envoi) {
				$flash = array('type' => 'notice', 'msg' => T_("Your email has been sent !"));
			} else {
				$flash = array('type' => 'error', 'msg' => T_("Your request could not be sent for an unknown reason.<br/>Please try again."));
			}

		}
		else {
			if(!$nom['success']){
				$flash = array('type' => 'error', 'msg' => $nom['error']);
				$error['nom']=true;
			}
			if(!$mail['success']){
				$flash = array('type' => 'error', 'msg' => $mail['error']);
				$error['email']=true;
			}
			if(!$titre['success']){
				$flash = array('type' => 'error', 'msg' => $titre['error']);
				$error['titre']=true;
			}
			if(!$content['success']){
				$flash = array('type' => 'error', 'msg' => $content['error']);
				$error['content']=true;
			}
		}
	}
}
function error_field($error, $field, $content) {
	if($error[$field])
		print("<span class='error'>".$content."</span>");
	else
		print("<span>".$content."</span>");
}
include(dirname(__FILE__).'/head.php');
# On active le cache
debutCache();
?>
<script type="text/javascript" src="javascript/functions.js"></script>
<div id="centre">

<?php
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="centre_centre">
<?php 
if (!empty($flash)) {
	echo '<div class="flash'.$flash['type'].'">'.$flash['msg'].'</div>';
	echo "<div class='informations'><h2>".T_("In case of problem")."</h2><p>";
	printf(T_("If you do not recieve any confirmation from the administration team in the 5 days, do not hesitate to contact us by email at %s"), $planet_author_mail);
	echo "</p></div>";
}
?>

<div id="template">
	<div class="post_small">
	<h2><?php echo T_("Contact us");?></h2>

	<p><?php echo T_("You can contact the administration team with the form below:");?></p>
	<br/>
	<form method="post">
	<?php error_field($error,'nom',T_('Name / Nickname :')); ?><br>
	<input class="contact" size="30" maxlength="30" type="text" name="nom" value="<?php if($nom) echo $nom['value']; ?>" /><br>
	<br>
	<?php error_field($error,'email',T_('Email :')); ?><br>
	<input class="contact" size="30" maxlength="30" type="text" name="email" value="<?php if($mail) echo $mail['value']; ?>" /><br>
	<br>
	<?php error_field($error,'titre',T_('Subject :')); ?><br>
	<input class="contact" size="73" maxlength="96" type="text" name="titre" /><br>
	<br><?php error_field($error,'content',T_('Content :')); ?><br>
	<textarea id="styled" class="contact" type="text" name="content"></textarea>
	<br>
	<br>
<?php
require_once(dirname(__FILE__).'/inc/lib/recaptchalib.php');
$publickey = "6LdEeQgAAAAAACLccbiO8TNaptSmepfMFEDL3hj2";
echo recaptcha_get_html($publickey);
?>
	<br><br>
	<input type="reset" value="<?php echo T_('Reset');?>" onclick="this.form.reset()">&nbsp;&nbsp;
<input type="submit" value="<?php echo T_('Send');?>" name="submit">
	</form>
	<br/>
	</div>
</div>
<?php
include(dirname(__FILE__).'/footer.php');
finCache();
?>
