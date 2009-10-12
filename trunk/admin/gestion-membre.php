<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.org
* Website : www.bilboplanet.org
* Tracker : redmine.bilboplanet.org
* Blog : blog.bilboplanet.org
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
if(isset($_POST) && isset($_POST['submit'])) {
	# Fonction de securite
	securiteCheck();

	# On recupere les infos
	$nom = check_field('nom',trim($_POST['nom']));
	$email = check_field('email',trim($_POST['email']),'email');
	$action = trim($_POST['action']);
	if ($action == "ajout" || $action == "mod")
		if (isset($_POST['statut']) && trim($_POST['statut'])==1)
			$site = check_field('site',$site.trim($_POST['site']),'url');
		else
			$site = check_field('site',$site.trim($_POST['site']),'not_empty');
	else
		$site = check_field('site',$site.trim($_POST['site']),'not_empty');

	# On convertie tous les caracteres speciaux en code html
	if ($nom['success'] && $email['success'] && $site['success']){
		$nom['value'] = htmlentities($nom['value'],ENT_QUOTES,mb_detect_encoding($nom['value']));
		connectBD();
		# On insert une nouvelle entree
		if($action=="ajout"){
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
		elseif ($action=="mod" && isset($_POST['statut']) && isset($_POST['num'])) {
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
		elseif($action=="del_confirm"){
			$confirmation = "<p>".sprintf(T_('Are you sure you want to remove user %s ?'),$nom['value'])."?<br/>";
			$confirmation .= "<ul><li>".T_('This action can not be canceled')."</li>";
			$confirmation .= "<li>".T_('All the posts of the user will be removed')."</li>";
			$confirmation .= "<li>".T_('All the votes on these posts will be removed')."</li>";
			$confirmation .= "<li>".T_('All the feeds of this user will be removed')."</li></ul><br/>";
			$confirmation .= "<form method='post'><input type='hidden' name='num' value='".trim($_POST['num'])."'/>";
			$confirmation .= "<input type='submit' name='reset' value='".T_('Reset')."'/>";
			$confirmation .= "<input type='submit' name='Confirm_delete' value='".T_('Confirm')."'/></form></p>";
			$flash = array('type' => 'error', 'msg' => $confirmation);
		}

		# Femeture de la base
		closeBD();
	}
	elseif($action=="ajout"){
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
	}
	else {
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
?>

<h2><?=T_('Add an user');?></h2>
<?php if (!empty($flash))echo '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>'; ?>

<form method="post">
<table width="450">
<tr>
<?php error_bool($error, "nom"); ?><?=T_('Name');?></td>
<td><input type="text" name="nom" size="30" value="<?php if($flash['type']=='error' && $_POST['nom']) echo $_POST['nom'];?>"/></td>
</tr>
<tr>
<?php error_bool($error, "email"); ?><?=T_('Email');?>:</td>
<td><input type="text" name="email" size="30" value="<?php if($flash['type']=='error' && $_POST['email']) echo $_POST['email'];?>"/></td>
</tr>
<tr>
<?php error_bool($error, "site"); ?><?=T_('Website (without the ending /)');?></td>
<td><input type="text" name="site" size="30" value="<?php if($flash['type']=='error' && $_POST['site']) echo $_POST['site'];?>"/></td>
</tr>
<tr>
<td  colspan="2" align="center"><br/>
<center><input type="hidden" name="action" value="ajout"/>
<input type="reset" name="reset" value="<?=T_('Reset');?>" onClick="this.form.reset()">&nbsp;&nbsp;
<input type="submit" name="submit" value="<?=T_('Send');?>"></center>
</tr>
</table/><br/>
</form>

<h2><?=T_('List of the users');?></h2>

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
<tr id="tr_head"><td><?=T_('Name');?></td><td><?=T_('Website');?></td><td><?=T_('Email');?></td><td><?=T_('Status');?></td><td><?=T_('Action');?></td><td></td></tr>
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
	echo '<form method="POST"><tr>
		<input type="hidden" name="num" value="'.$liste[0].'"/>
		<td><input type="text" name="nom" value="'.$liste[1].'" class="'.$statut.'"/></td>
		<td><input type="text" name="site" value="'.$liste[2].'" size="30" class="zone-saisie" /></td>
		<td><input type="text" name="email" value="'.$liste[3].' " class="zone-saisie" /></td>
		<td>'.$select.'</td>
		<td><input type="radio" name="action" value="mod"> '.T_('Change').'<br />
		<input type="radio" name="action" value="del_confirm"> '.T_('Delete').'</td>
		<td><center><input type="submit" name="submit" value="'.T_('Apply').'"/><br/><a href="'.$liste[2].'" target="_bank">'.T_('Show').'</a></center></td></tr></form>';
	echo '<tr><td  colspan="6" id="td_separateur"></td></tr>';
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
