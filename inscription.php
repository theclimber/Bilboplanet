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
session_start();
if(isset($_POST) && isset($_POST['submit'])){
	require_once(dirname(__FILE__).'/inc/lib/recaptchalib.php');
	$privatekey = "6LdEeQgAAAAAABrweqchK5omdyYS_fUeDqvDRq3Q";
	$captcha = recaptcha_check_answer (
		$privatekey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);

	# On recupere les infos
	$nom = check_field('nom',trim($_POST['nom']),'not_empty');
	$prenom = check_field('prenom',trim($_POST['prenom']),'',false);
	$mail = check_field('email',trim($_POST['email']),'email');
	$url = check_field('url',trim($_POST['url']),'url');
	$rss = check_field('flux',trim($_POST['rss']),'feed');
	$choix = check_field('choix',trim($_POST['choix']),'not_empty');
	if (!$captcha->is_valid) {
		$flash = array('type' => 'error', 'msg' => sprintf(T_("The reCAPTCHA wasn't entered correctly. Go back and try it again. (reCAPTCHA said: %s)"),$captcha->error));
	} else {
		$ip     = getIP();

		if ($nom['success'] && $prenom['success'] && $mail['success'] && $url['success'] && $rss['success'] && $choix['success']){
			# Construction du mail
			$objet = $planet_title." - ".$choix['value'];
			$msg = T_("Name : ").$nom['value'];
			$msg .= "\n".T_("Firstname : ").$prenom['value'];
			$msg .= "\n".T_("Email : ").$mail['value'];
			$msg .= "\n".T_("Website : ").$url['value'];
			$msg .= "\n".T_("Feed : ").$rss['value'];
			$msg .= "\n".T_("Choice : ").$choix['value'];
			$msg .= "\nIP : $ip";

			# Envoi du mail
			$envoi = mail($planet_author_mail, $objet, $msg,"From: ".$mail['value']."\r\nReply-To: ".$mail['value']."\r\n");

			# Message d'information
			if($envoi) {
				$flash = array('type' => 'notice', 'msg' => T_("Your email has been sent"));
			} else {
				$flash = array('type' => 'error', 'msg' => T_("Your request could not be sent for an unknown reason.<br/>Please try again."));
			}
		}
		else {
			if(!$nom['success']){
				$flash = array('type' => 'error', 'msg' => $nom['error']);
				$error['nom']=true;
			}
			if(!$prenom['success']){
				$flash = array('type' => 'error', 'msg' => $prenom['error']);
				$error['prenom']=true;
			}
			if(!$mail['success']){
				$flash = array('type' => 'error', 'msg' => $mail['error']);
				$error['email']=true;
			}
			if(!$url['success']){
				$flash = array('type' => 'error', 'msg' => $url['error']);
				$error['site']=true;
			}
			if(!$rss['success']){
				$flash = array('type' => 'error', 'msg' => $rss['error']);
				$error['flux']=true;
			}
			if(!$choix['success']){
				$flash = array('type' => 'error', 'msg' => $choix['error']);
			}
		}
	}
}
function error_bool($error, $field) {
	if($error[$field])
		print("<td class='error'>");
	else
		print("<td>");
}

include('head.php');
debutCache();
?>

<div id="centre">

<?php
include_once('sidebar.php');
?>

<div id="centre_centre">
<div id="template">
<div class="post_small">
<?php 
if (!empty($flash)) {
	echo '<div class="flash'.$flash['type'].'">'.$flash['msg'].'</div>';
	echo "<div class='informations'><h2 class='informations'>".T_("In case of problem")."</h2>";
	echo "<p>".sprintf(T_("If you don't recieve any new from the administration team in the 5 days do not hesitate to contact us via %s with this information :"),$planet_author_mail)."<br /><ul>";
	echo "<li><b>".T_("Subject")."</b> : $planet_title - ".$choix['value']."</li>";
	echo "<li><b>".T_("Name")."</b> : ".$nom['value']."</li>";
	echo "<li><b>".T_("Firstname")."</b> : ".$prenom['value']."</li>";
	echo "<li><b>".T_("Email")."</b> : ".$mail['value']."</li>";
	echo "<li><b>".T_("Website")."</b> : ".$url['value']."</li>";
	echo "<li><b>".T_("Feed")."</b> : ".$rss['value']."</li>";
	echo "<li><b>".T_("Choice")."</b> : ".$choix['value']."</li>";
	echo "</ul></p></div>";
}
?>

<?php
$file=dirname(__FILE__).'/inscription_contenu.php';
$content = file_get_contents($file);
echo stripslashes($content);
?>

<h2><?=T_('Test your feeds');?></h2>

<p><?=T_("Before to subscribe, do not hesitate to test your RSS/Atom feeds to be sure they'll be well interpretated by the planet aggregator engine which is using the <a href='http://simplepie.org/' title='Simple Pie' rel='noffolow'>Simple Pie</a> library (distributed under the LGPL licecne). Check also if your feeds are perfectly valid on <a href='http://feedvalidator.org/check.cgi' target='_blank'>Feedvalidator</a> and correct them if needed. Otherwise you could have some problems using them.");?>
<br/>
<?=T_("You can test the simplepie engine on the following page :");?><br/>
<a href="http://simplepie.org/demo/" title="test" rel="nofollow">http://simplepie.org/demo/</a>


<h2><?=T_('Subscribe / Unsubscribe');?></h2>
<p><?=T_('To add or remove your website from the planet, just fill this form in :');?></p>

<form method="post">
<table border="0" width="600">
	<tr>
	<?php error_bool($error, "nom"); ?><?=T_('Name or nicknname');?></td>
		<td><input type="text" name="nom" value="<?php if($nom) echo $nom['value']; ?>" /></td>
	</tr>
	<tr>
	<?php error_bool($error, "prenom"); ?><?=T_('Firstname (optional)');?></td>
		<td><input type="text" name="prenom" value="<?php if($prenom) echo $prenom['value']; ?>" /></td>
	</tr>
	<tr>
	<?php error_bool($error, "email"); ?><?=T_('Contact email');?></td>
		<td><input type="text" name="email" value="<?php if($mail) echo $mail['value']; ?>" /></td>
	</tr>
	<tr>
	<?php error_bool($error, "site"); ?><?=T_('Website URL');?></td>
		<td><input type="text" name="url" value="<?php if($url) echo $url['value']; ?>" /></td>
	</tr>
	<tr>
	<?php error_bool($error, "flux"); ?><?=T_('Feed URL (this can be a tag or category specific feed feed too)');?></td>
		<td><input type="text" name="rss" value="<?php if($rss) echo $rss['value']; ?>" /></td>
	</tr>
	<tr>
	<td colspan="2"><br/><input type="radio" name="choix" value="abonnement" checked /> <?=T_('Subscribe');?></td>
	</tr>
	<tr>
	<td colspan="2"><input type="radio" name="choix" value="desabonnement" /> <?=T_('Unsubscribe');?></td>
	</tr>
	<tr>
	<?php error_bool($error, "captcha"); ?><?=T_('Please fill in the captcha');?></td>
		<td >
<?php
require_once(dirname(__FILE__).'/inc/lib/recaptchalib.php');
$publickey = "6LdEeQgAAAAAACLccbiO8TNaptSmepfMFEDL3hj2";
echo recaptcha_get_html($publickey);
?>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" name="ok" value="" />
		<?=T_('I have read and accept the charter');?></td>
	</tr>
	<tr>
		<td  colspan="2" align="center"><br/>
		<input type="reset" value="<?=T_('Reset');?>" onclick="this.form.reset()">&nbsp;&nbsp;
		<input type="submit" value="<?=T_('Send');?>" name="submit"></td>
	</tr>
</table>
</form>
</div>

<div class="post_small">
<h2><?=T_('Contact us');?></h2>

<p><?=T_("If you need to contact the administration team for any reason (change of your feed URL, suggestion ...), you can do it by <a href='contact.php'>clicking here</a>.");?>
</p>
</div>

</div>
<script type="text/javascript" src="javascript/functions.js"></script>
<?php 
include(dirname(__FILE__).'/footer.php');
finCache();
?>
