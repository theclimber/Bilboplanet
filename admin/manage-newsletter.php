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
require_once(dirname(__FILE__).'/../inc/admin/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager') | !$core->hasPermission('administration')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}

### Mise en cache
#debutCache();

### Initialisation variables de traitement
$flash = array(); 	# Tableau pour l'affichage des messages d'erreurs, d'avertissement, de notice, ...
$confirmation = ''; # Message de confirmation

### Initialisation des variables formulaires
# Adresse e-mail expéditrice
$sender = (isset($_POST['newsletter_sender'])) ? check_field(T_('Sender'),$_POST['newsletter_sender'],'email') : check_field(T_('Sender'),$blog_settings->get('author_mail'),'email');
# Adresses e-mail destinataires
$recipients = (isset($_POST['newsletter_recipient'])) ? check_recipients(T_('Recipients'),$_POST['newsletter_recipient']) : check_recipients(T_('Recipients'),'');
# Sujet de la newsletter
$subject = (isset($_POST['newsletter_subject'])) ? check_field(T_('Subject'),cleanupString($_POST['newsletter_subject']),'not_empty') : '';
# Contenu de la newsletter
$message = (isset($_POST['newsletter_message'])) ? check_field(T_('Message'),cleanupString($_POST['newsletter_message']),'not_empty') : '';

### On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['submitNewsletter'])) {
	if($sender['success'] && $recipients['success'] && $subject['success'] && $message['success']) {
		$msg = htmlspecialchars(preg_replace('/\n/', '<br/>',$message['value']));
		$confirmation .= '<p>';
		$confirmation .= '<form name="NewsletterConfirm" method="POST">';
		$confirmation .= '&nbsp;&nbsp;<u>'.T_('Are you sure you want to send this newsletter?').'</u>';
		$confirmation .= '<br /><br />';
		$confirmation .= '<input type="hidden" name="sender" value="'.htmlspecialchars($sender['value']).'" />';
		$confirmation .= '<input type="hidden" name="recipients" value="'.htmlspecialchars($recipients['value']).'" />';
		$confirmation .= '<input type="hidden" name="subject" value="'.htmlspecialchars($subject['value']).'" />';
		$confirmation .= '<input type="hidden" name="message" value="'.$msg.'" />';
		$confirmation .= '&nbsp;&nbsp;<input type="submit" class="button br3px" name="confirmSubmit" value="'.T_('Yes').'" />';
		$confirmation .= '&nbsp;&nbsp;<input type="button" class="button br3px" name="reset" value="'.T_('No').'" />';
		$confirmation .= '</form>';
		$confirmation .= '</p>';
		$flash['warning'][] = $confirmation;
	}
	else {
		if(!$sender['success']) {
			$flash['error'][] = htmlspecialchars($sender['error']);
		}
		if(!$recipients['success']) {
			$flash['error'][] = htmlspecialchars($recipients['error']);
		}
		if(!$subject['success']) {
			$flash['error'][] = htmlspecialchars($subject['error']);
		}
		if(!$message['success']) {
			$flash['error'][] = htmlspecialchars($message['error']);
		}
	}
}

# Vérification du formulaire de confirmation
if(isset($_POST) && isset($_POST['confirmSubmit'])) {
	$envoi = sendmail($_POST['sender'], $_POST['recipients'], $_POST['subject'], $_POST['message'], 'newsletter');
	if($envoi) {
		$flash['notice'][] = T_("Your email has been sent");
	} else {
		$flash['error'][] = T_("Your request could not be sent for an unknown reason.<br/>Please try again.");
	}
}

### On recupere les informations nécessaires sur les membres actifs seulement
$sql = "SELECT user_fullname, user_email FROM ".$core->prefix."user WHERE user_status = '1' ORDER BY lower(user_fullname) ASC;";
$rs = $core->con->select($sql);
if ($rs) {
	$nb_rows = ceil($rs->count() / 4); # Calcul du nombre d'élément pour un afficage en 4 colonnes (arrondi à l'entier supérieur)
	$cpt = 0;
}

### Construction de la liste des destinataires (checkbox)
$checkbox_recipients = '<p>'."\n";
$checkbox_recipients .= '<input name="all_recipients" type="checkbox" id="checkAllCheckboxes" />'."\n";
$checkbox_recipients .= '&nbsp;'.T_('Select all members')."\n";
$checkbox_recipients .= '</p>'."\n";
$checkbox_recipients .= '<br />'."\n";
$checkbox_recipients .= '<table class="table-newsletter">'."\n";
$checkbox_recipients .= '<tr>'."\n";

while ($rs->fetch()) {
	if ($cpt == 0) {
		$checkbox_recipients .= '<td>'."\n";
	}
	$checkbox_recipients .= '<p>'."\n";
	if (checkCheckbox($recipients, $rs->user_email)) {
		#id="checkAll": appel jQuery pour cocher ou décocher toutes les checkbox du type checkAll par la checkbox id="checkAllCheckboxes"
		$checkbox_recipients .= '<input id="checkAll" name="newsletter_recipient[]" type="checkbox" value="' . $rs->user_email . '" checked />'."\n";
		}
	else {
		$checkbox_recipients .= '<input id="checkAll" name="newsletter_recipient[]" type="checkbox" value="' . $rs->user_email . '" />'."\n";
	}
	$checkbox_recipients .= '&nbsp;' . $rs->user_fullname."\n";
	$checkbox_recipients .= '&nbsp;(' . $rs->user_email . ')'."\n";
	$checkbox_recipients .= '</p>'."\n";
	if ($cpt == $nb_rows - 1) {
		$checkbox_recipients .= '</td>'."\n";
		$cpt = 0;
	}
	else {
		$cpt++;
	}
}
$checkbox_recipients .= '</tr>'."\n";
$checkbox_recipients .= '</table>'."\n";

# Inclusion fichier d'en-tête et menu de navigation
include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>
<!-- Début de la page -->
<script type="text/javascript" src="meta/js/manage-newsletter.js"></script>
<div id="BP_page" class="page">
	<div class="inpage">
    	<?php
		if (!empty($flash)) {
			$msg = '<ul>';
			foreach(array_keys($flash) as $key => $msg_type) {
				foreach(array_keys($flash[$msg_type]) as $key_msg) {
					$msg .= '<li>'.$flash[$msg_type][$key_msg].'</li>';
				}
			}
			$msg .= '</ul>';
			echo '<div id="post_flash" class="flash_'.$msg_type.'" style="display:none;">'.$msg.'</div>';
		}
		?>
		<form name="newsletter_form" id="newsletter_form" method="POST">
            <fieldset>
                <legend>
                    <?php echo T_('Recipients');?>
                </legend>
                <div class="message">
                    <p>
                        <?php echo T_('Select recipients of the newsletter').':';?>
                   </p>
                </div>
                <br />
				<?php echo $checkbox_recipients; ?>
            </fieldset>
            <fieldset>
                <legend>
                    <?php echo T_('Content'); ?>
                </legend>
                	<div class="message">
                        <p>
							<?php echo T_('Complete the informations of the newsletter').':';?>
                       </p>
                	</div>
                <br />
				<?php echo T_('Sender') . ":";?><br />
				<input type="text" name="newsletter_sender" size="60" class="input" value="<?php if($sender) echo $sender['value'];?>" /><br />
				<div class="comment">
					<p>
						<?php echo T_('Default').": ".$blog_settings->get('author_mail');?>
					</p>
				</div>
                <?php echo T_('Subject') . ":";?><br />
            	<input type="text" name="newsletter_subject" size="60" class="input" value="<?php if($subject) echo $subject['value'];?>" /><br />
				<div class="comment">
					<p>
						&nbsp;
					</p>
				</div>
                <?php echo T_('Message') . ":";?><br />
				<div class="wysiwyg"><script>edToolbar('newsletter_message'); </script></div>
				<script type="text/javascript">
					$(document).ready(function() {
						$('input[name="preview"]').click(function() {
							$('textarea[name="newsletter_message"]').preview({
								opacity: '0.5'
							})
						});
					});
				</script>
                <textarea name="newsletter_message" id="newsletter_message" class="cadre_option" rows="30" cols="150"><?php if($message) echo $message['value'];?></textarea><br />
    		</fieldset>
            <fieldset>
            	<legend>
                	<?php echo T_('Action'); ?>
                </legend>
                <br />
                <div class="button br3px">
                	<input type="button" class="preview" name="preview" value="<?php echo T_('Preview');?>" />
                </div>
                &nbsp;&nbsp;
                <div class="button br3px">
                	<input type="submit" class="valide" name="submitNewsletter" value="<?php echo T_('Send');?>" />
                </div>
                &nbsp;&nbsp;
                <div class="button br3px">
                   	<input type="reset" class="reset" name="reset" value="<?php echo T_('Reset');?>" />
                </div>
            </fieldset>
		</form>
<?php
### Inclusion du footer
include(dirname(__FILE__).'/footer.php');

## Fin du cache
#finCache();

else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;

# Fonction permettant de pré-cocher une checkbox si la valeur de celle-ci est dans un tableau
function checkCheckbox($array, $field) {
	if($array['value']) {
		$email = split(',', $array['value']);
		while (list ($key,$value) = @each ($email)) {
			if($value == $field) {
				return true;
			}
		}
	}
}

# Fonction de vérification des destinataires
function check_recipients($fieldname, $array) {
	$success = true;
	$value = "";
	$error = "";
	if(is_array($array) && count($array) > 0) {
		while (list ($key,$email) = @each ($array)) {
			$value .= $email.',';
		}
	}
	else {
		$success = false;
		$error = sprintf(T_('You shoud have selected %s'), T_($fieldname));
	}
	return array(
		"success" => $success,
		"value" => $value,
		"error" => $error
		);
}

# Fonction permettant de "nettoyer" du texte
function cleanupString($string) {
	$string = trim($string);
	if (get_magic_quotes_gpc()) {
		$string = stripslashes($string);
	}
	return $string;
}
?>
