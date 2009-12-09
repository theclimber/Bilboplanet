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
/* Inclusion du fichier de configuration */
require_once(dirname(__FILE__).'/../inc/fonctions.php');
debutCache();

$flash='';
$confirmation='';
global $error;
# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['Confirm_delete'])) {
	$num = trim($_POST['num']);
	connectBD();
	$num = trim($_POST['num']);
	$sql = "DELETE FROM membre WHERE num_membre='$num'";
	$result = mysql_query($sql) or die("Error with request $sql");
	$sql = "DELETE FROM flux WHERE num_membre='$num'";
	$result = mysql_query($sql) or die("Error with request $sql");
	$sql = "DELETE FROM votes USING article, votes WHERE article.num_article=votes.num_article AND article.num_membre = '$num'";
	$result = mysql_query($sql) or die("Error with request $sql");
	$sql = "DELETE FROM article WHERE num_membre='$num'";
	$result = mysql_query($sql) or die("Error with request $sql");
	$flash = array('type' => 'notice', 'msg' => sprintf(T_("Delete of user %s succeeded"),$nom['value']));
	# Femeture de la base
	closeBD();
	if(!$result) {
		$flash = array('type' => 'error', 'msg' => sprintf(T_('Error while trying to remove the user %s'),$nom['value']));
	}
}
if(isset($_POST) && (
    (isset($_POST['submitModif']) && !empty($_POST['submitModif'])) ||
    (isset($_POST['submitAjout']) && !empty($_POST['submitAjout'])) ||
    (isset($_POST['submitDelete']) && !empty($_POST['submitDelete']))
))
{
	# Fonction de securite
	securiteCheck();

	# On recupere les infos
	$nom = check_field('nom',trim($_POST['nom']));
	$email = check_field('email',trim($_POST['email']),'email');
	if ($action == "ajout" || $action == "mod")
		if (isset($_POST['statut']) && trim($_POST['statut'])==1)
			$site = check_field('site',$site.trim($_POST['site']),'url');
		else
			$site = check_field('site',$site.trim($_POST['site']),'not_empty');
	else
		$site = check_field('site',$site.trim($_POST['site']),'not_empty');
	$action = trim($_POST['action']);

	# On convertie tous les caracteres speciaux en code html
	if ($nom['success'] && $email['success'] && $site['success']){
		$nom['value'] = htmlentities($nom['value'],ENT_QUOTES,mb_detect_encoding($nom['value']));
		connectBD();
		# On insert une nouvelle entree
		if(isset($_POST) && isset($_POST['submitAjout']) && !empty($_POST['submitAjout'])){
			$sql = "SELECT nom_membre FROM membre WHERE nom_membre='".$nom['value']."'";
			$result1 = mysql_query($sql) or die("Error with request $sql");
			$sql = "SELECT nom_membre FROM membre WHERE email_membre='".$email['value']."'";
			$result2 = mysql_query($sql) or die("Error with request $sql");
			$sql = "SELECT nom_membre FROM membre WHERE site_membre='".$site['value']."'";
			$result3 = mysql_query($sql) or die("Error with request $sql");
			if (mysql_result($result1,0)){
				$flash = array('type' => 'error', 'msg' => sprintf(T_('The user %s already exists'),$nom['value']));
				$error['nom']=true;
			}
			if (mysql_result($result2,0)){
				$flash = array('type' => 'error', 'msg' => sprintf(T_('The email address %s is already in use'),$email['value']));
				$error['email']=true;
			}
			if (mysql_result($result3,0)){
				$flash = array('type' => 'error', 'msg' => sprintf(T_('The website %s is already assigned to an user'),$site['value']));
				$error['site']=true;
			}
			if (!mysql_result($result1,0) && !mysql_result($result2,0) && !mysql_result($result3,0)) {
				$sql = "INSERT INTO membre VALUES ('', '".$nom['value']."', '".$email['value']."', '".$site['value']."', '1')";
				$result = mysql_query($sql) or die("Error with request $sql");
				$flash = array('type' => 'notice', 'msg' => sprintf(T_("User %s successfully added"),$nom['value']));
				if (!$result)
					$flash = array('type' => 'error', 'msg' => sprintf(T_('Error while trying to modify user %s'),$nom['value']));
			}
		}
		elseif(isset($_POST['submitModif']) && !empty($_POST['submitModif']) && isset($_POST['statut']) && isset($_POST['num'])) {
			$num = trim($_POST['num']);
			$statut = trim($_POST['statut']);
			$sql = "UPDATE membre 
				SET nom_membre = '".$nom['value']."', site_membre = '".$site['value']."', email_membre = '".$email['value']."', statut_membre = '$statut'
				WHERE num_membre = '$num'";
			$flash = array('type' => 'notice', 'msg' => sprintf(T_('Modification of user %s succeeded'),$nom['value']));
			$result = mysql_query($sql) or die("Error with request $sql");
			if (!$result)
				$flash = array('type' => 'error', 'msg' => sprintf(T_('Error while trying to modify user %s'),$nom['value']));
		}
            # Femeture de la base
            closeBD();
	}
	if(isset($_POST) && isset($_POST['submitDelete']) && !empty($_POST['submitDelete'])) {
            $confirmation = "<p>".sprintf(T_('Are you sure you want to remove user %s ?'),$nom['value'])."?<br/>";
            $confirmation .= "<ul><li>".T_('This action can not be canceled')."</li>";
            $confirmation .= "<li>".T_('All the posts of the user will be removed')."</li>";
            $confirmation .= "<li>".T_('All the votes on these posts will be removed')."</li>";
            $confirmation .= "<li>".T_('All the feeds of this user will be removed')."</li></ul><br/>";
            $confirmation .= "<form method='post'><input type='hidden' name='num' value='".trim($_POST['num'])."'/>";
            $confirmation .= "<input type='submit' class='button br3px' name='reset' value='".T_('Reset')."'/>&nbsp;&nbsp;";
            $confirmation .= "<input type='submit' class='button br3px' name='Confirm_delete' value='".T_('Confirm')."'/></form></p>";
            $flash = array('type' => 'error', 'msg' => $confirmation);
        }
	if($action=="ajout"){
		if(!$nom['success']){
			$flash = array('type' => 'error', 'msg' => $nom['error']);
			$error['nom']=true;
		}
		if(!$email['success']){
			$flash = array('type' => 'error', 'msg' => $email['error']);
			$error['email']=true;
		}
		if(!$site['success']){
			$flash = array('type' => 'error', 'msg' => $site['error']);
			$error['site']=true;
		}
	}else{
		if(!$nom['success']){
			$flash = array('type' => 'error', 'msg' => $nom['error']);
		}
		if(!$email['success']){
			$flash = array('type' => 'error', 'msg' => $email['error']);
		}
		if(!$site['success']){
			$flash = array('type' => 'error', 'msg' => $site['error']);
		}
	}
}
function error_bool($error, $field) {
	if($error[$field])
		print("<td class='error'>");
	else
		print("<td>");
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="BP_page" class="page">
	<div class="inpage">
	
<?php if (!empty($flash))echo '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>'; ?>

	
<fieldset><legend><?=T_('Add an user');?></legend>
		<div class="message">
			<p><?=T_('To add a new member, fill out the form below.');?></p>
		</div><br />

<form method="post">
<label for="name"><strong><?php error_bool($error, "nom"); ?><?=T_('Name');?>:</strong></label>
<input type="text" class="input" style="padding-left: 10px;" id="username" name="nom" value="<?php if($flash['type']=='error' && $_POST['nom']) echo $_POST['nom'];?>" size="50" maxlength="50" />
<br /><br /> <!-- A CHANGER !!! -->
<label for="email"><strong><?php error_bool($error, "email"); ?><?=T_('Email');?>:</strong></label>
<input type="text" class="input" style="padding-left: 10px;" id="email" name="email" value="<?php if($flash['type']=='error' && $_POST['email']) echo $_POST['email'];?>" size="50" maxlength="80" />
<br /><br /> <!-- A CHANGER !!! -->
<label for="site"><strong><?php error_bool($error, "site"); ?><?=T_('Website (without the ending /)');?>:</strong></label>
<input type="text" class="input" style="padding-left: 10px;" value="http://www.exemple.com" onfocus="if (this.value==='http://www.exemple.com') {this.value='';}" id="site" name="site" value="<?php if($flash['type']=='error' && $_POST['site']) echo $_POST['site'];?>" size="50" maxlength="80" />
<br /><br /><br /> <!-- A CHANGER !!! -->
<div class="button"><input type="reset" class="reset" name="reset" onClick="this.form.reset()" value="<?=T_('Reset');?>"></div>
<div class="button"><input type="submit" name="submitAjout" class="add_user" value="<?=T_('Send');?>"></div>
</form>
</fieldset>


<fieldset><legend><?=T_('List of the users');?></legend>
		<div class="message">
			<p><?=T_('List of members of the Planet');?></p>
		</div>
<?php

# Valeurs par defaut
$num_page = 0;
$num_start = 0;
$nb_items = 30;

# Verification du contenu du get
if (isset($_GET) && isset($_GET['nb_items']) && !empty($_GET['nb_items'])){
	$nb_items = $_GET['nb_items'];
}
if (isset($_GET) && isset($_GET['page']) && is_numeric(trim($_GET['page']))) {
	# On recuepre la valeur du get
	$num_page = trim($_GET['page']);
	if ($num_page < 1) {
		$num_page = 0;
	}
	$num_start = $num_page * $nb_items;
}


# Connection a la base
connectBD();

# On recupere les informtions sur les membres
$sql = 'SELECT num_membre, nom_membre, site_membre, email_membre, statut_membre FROM membre ORDER by nom_membre ASC LIMIT '.$num_start.','.$nb_items;
$rqt = mysql_query($sql) or die("Error with request $sql");
$nb = mysql_num_rows($rqt);

include(dirname(__FILE__).'/pagination.php');
?>
<br /><br />
<table>
<table id="tbl1" class="table-membre">
		<thead>
			<tr>
				<th class="tc1 tcl" scope="col"><?=T_('Name');?></th>
				<th class="tc2" scope="col"><?=T_('Website');?></th>
				<th class="tc3" scope="col"><?=T_('Email');?></th>
				<th class="tc4" scope="col" ><?=T_('Status');?></th>
				<th class="tc5 tcr" scope="col"><?=T_('Action');?></th>
			</tr>
		</thead>

<?php

# On affiche la liste de membres
while($liste = mysql_fetch_row($rqt)) {
	# Couleur de la ligne en fonciton du statut du membre
	if($liste[4]) {
		$select  = '<select name="statut" class="actif">';
		$select .= '<option value="1" selected>'.T_('active').'</option>';
		$select .= '<option value="0">'.T_('inactive').'</option></select>';
		$statut  = "actif"; 
	} else {
		$select  = '<select name="statut" class="inactif">';
		$select .= '<option value="0" selected>'.T_('inactive').'</option>';
		$select .= '<option value="1">'.T_('active').'</option></select>';
		$statut  = "inactif";
	}


	# Affichage de la ligne de tableau
	echo '<form method="POST">
		<tr>
		<input type="hidden" name="num" value="'.$liste[0].'"/>
		<td class="tc1 tcl row1"><input class="input '.$statut.'" type="text" name="nom" value="'.$liste[1].'"/></td>
		<td class="tc2 row1"><input class="inputURL input" type="text" name="site" value="'.$liste[2].'" />&nbsp;&nbsp;<a href="'.$liste[2].'" title="'.T_('Go to user website / blog').'" target="_bank">'.T_('Show').'</a></td>
		<td class="tc3 row1"><input class="input zone-saisie" type="text" name="email" value="'.$liste[3].'"  /></td>
		<td class="tc4 row1">'.$select.'</td>
		<td class="tc5 tcr row1">
			<input type="submit" class="button br3px" name="submitModif" value="'.T_('Change').'" />
			<input type="submit" class="button br3px" name="submitDelete" value="'.T_('Delete').'" />
		</td>
		</tr>
		</form>';
}
?>

</table>

<?php 
$params = "page=$num_page&";
?>
<div class="nbitems">
<?=T_('Show items by : ');?> <a href="?<?php echo $params; ?>nb_items=10">10</a>, <a href="?<?php echo $params; ?>nb_items=20">20</a>, <a href="?<?php echo $params; ?>nb_items=50">50</a>
</div>

<?php
closeBD();
include(dirname(__FILE__).'/footer.php');
finCache();
?>
