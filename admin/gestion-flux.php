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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/fonctions.php');
	connectBD();
debutCache();
$flash = '';
global $error;

# On verifie que le formulaire est bien saisie
if( isset($_POST) && isset($_POST['submit']))  {
	securiteCheck();

}
if(isset($_POST) && (
    (isset($_POST['submitModify']) && !empty($_POST['submitModify'])) ||
    (isset($_POST['submitDelete']) && !empty($_POST['submitDelete'])) ||
    (isset($_POST['submitAjout']) && !empty($_POST['submitAjout']))
))
{
	$num  = trim($_POST['num']);
	$num_membre  = trim($_POST['num_membre']);
	$sql = "SELECT site_membre FROM membre WHERE num_membre='".$num_membre."'";
	$result1 = mysql_query($sql) or die("Error with request $sql");
	$site = mysql_result($result1,0);
	if (isset($_POST['flux']) && empty($_POST['flux'])){
		$flux = check_field('flux',trim($_POST['flux']),'feed');
	}
	elseif ((isset($_POST['submitModify']) && !empty($_POST['submitModify'])) || (isset($_POST['submitAjout']) && !empty($_POST['submitAjout']))) {
		if (isset($_POST['statut']) && trim($_POST['statut'])==1){
			$flux = check_field('flux',trim($_POST['flux']),'feed');
		}
		else {
			$flux = check_field('flux',trim($_POST['flux']),'feed');
		}
	}
	else {
		$flux = check_field('flux',trim($_POST['flux']),'not_empty');
	}
	$flux['value'] = trim($_POST['flux']);
	if ($flux['success']){
		if(isset($_POST) && isset($_POST['submitDelete']) && !empty($_POST['submitDelete'])) {
			$sql = "DELETE FROM flux WHERE num_flux='$num'";
			$flash = array('type' => 'notice', 'msg' => sprintf(T_("The feed %s was correctly deleted"),$flux['value']));
			$result = mysql_query($sql) or die("Error with request $sql");
			if(!$result)
				$flash = array('type' => 'error', 'msg' => T_("Error while trying to change the informations of the feed"));
		}
		if(isset($_POST) && isset($_POST['submitAjout']) && !empty($_POST['submitAjout'])) {
			$sql = "SELECT url_flux FROM flux WHERE url_flux='".$flux['value']."' AND num_membre='".$num_membre."'";
			$result1 = mysql_query($sql) or die("Error with request $sql");
			if (mysql_result($result1,0)){
				$flash = array('type' => 'error', 'msg' => sprintf(T_("The user already has a feed %s"),$flux['value']));
				$error['flux']=true;
			}
			else{
				$sql = "INSERT INTO flux (`num_flux`, `url_flux`, `num_membre`) VALUES ('', '".$flux['value']."', '$num_membre')";
				$result = mysql_query($sql) or die("Error with request $sql");
				if(!$result)
					$flash = array('type' => 'error', 'msg' => T_("Error while trying to change the informations of the feed"));
			}
			$flash = array('type' => 'notice', 'msg' => sprintf(T_("Adding the feed %s succeeded"),$flux['value']));
		}
		if(isset($_POST) && isset($_POST['submitModify']) && !empty($_POST['submitModify'])) {
			$statut = trim($_POST['statut']);
			$sql = "UPDATE flux 
				SET url_flux = '".$flux['value']."', status_flux = '$statut'
				WHERE num_flux = '$num'";
			$flash = array('type' => 'notice', 'msg' => sprintf(T_("Changing the feed %s succeeded"),$flux['value']));
			$result = mysql_query($sql) or die("Error with request $sql");
			if(!$result)
				$flash = array('type' => 'error', 'msg' => T_("Error while trying to change the informations of the feed"));
		}
		closeBD();
	}
	else {
		if(isset($_POST) && isset($_POST['submitAjout']) && !empty($_POST['submitAjout'])) {
			$error['flux']=true;
		}
		$flash = array('type' => 'error', 'msg' => $flux['error']);
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

<fieldset><legend><?=T_('Add a feed');?></legend>
	<div class="message">
		<p>G&eacute;rer les Flus RSS des membres.</p>
	</div>
		
<br/>

<center>
<?php error_bool($error, "flux"); ?>
<table class="table-flux-add">
		<thead>
			<tr>
				<th class="tc1 tcl" scope="col"><?=T_('Full url of the feed (Without the ending /)');?></th>
				<th class="tc2 tcr" scope="col"><?=T_('Name of the user');?></th>
			</tr>
		</thead>
			<tr>
			<form method="post">
				<td class="tc1 tcl row2">
					<input class="input" value="http://www.exemple.com/feed" onfocus="if (this.value==='http://www.exemple.com/feed') {this.value='';}" size="49" type="text" name="flux" value="<?php if($error["flux"]) echo $_POST['flux'];?>" />
				</td>
				<td class="tc2 row2">
					<center>
					<select name="num_membre" style="width:98%">
					<?php
					# Connection a la base 
					connectBD();

					# Execution de la requete
					$sql = 'SELECT num_membre, nom_membre FROM membre ORDER BY nom_membre ASC;';
					$rqt = mysql_query($sql) or die("Error with request $sql");

					# Traitement de la liste
					while($liste = mysql_fetch_row($rqt)) {
					  echo '<option value="'.$liste[0].'">'.$liste[1].'</option>';
					}
					?>
					</select>
					</center>
				</td>
			</tr>
</table>
<br />
<div class="button"><input type="reset" class="reset" name="reset" onClick="this.form.reset()" value="<?=T_('Reset');?>"></div>
<div class="button"><input type="submit" name="submitAjout" class="valide" value="<?=T_('Send');?>"></div>
</center>
</form>
</fieldset>

<fieldset><legend><?=T_('Manage feeds');?></legend>
	<div class="message">
		<p><?=T_('Manage member feed.');?></p>
	</div>
	

<?php
# On recupere les informtions sur les membres
$sql = 'SELECT nom_membre, site_membre, email_membre, statut_membre FROM membre ORDER by nom_membre ASC';
$rqt = mysql_query($sql) or die("Error with request $sql");
?>
<center>
<table class="table-flux sortable" >
		<thead>
			<tr>
				<th class="tc1 tcl" scope="col"><?=T_('Name');?></th>
				<th class="tc2" scope="col"><?=T_('URL of the feed');?></th>
				<th class="tc3" scope="col"><?=T_('Status');?></th>
				<th class="tc4 tcr" scope="col"><?=T_('Action');?></th>
			</tr>
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


# Execution de la requete
$sql = 'SELECT num_flux, url_flux, nom_membre, site_membre,statut_membre,status_flux,membre.num_membre
	FROM flux, membre 
	WHERE flux.num_membre = membre.num_membre 
	ORDER by nom_membre ASC
	LIMIT '.$num_start.','.$nb_items;
$rqt = mysql_query($sql) or die("Error with request $sql");
$nb = mysql_num_rows($rqt);

include(dirname(__FILE__).'/pagination.php');
echo '<br /><br />';
# Traitement de la liste
while($liste = mysql_fetch_row($rqt)) {

	# Construction de l'url
	$url = $liste[3].$liste[1];

	# Couleur de la ligne en fonciton du statut du membre
	if($liste[4] or $liste[5]) {
		$statut = 'actif';
	} else {
		$statut = 'inactif';
	}
	if($liste[5]) {
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

	# Affichage
	$parse = @parse_url($liste[1]);
	$line = '<form method="POST">
			<tr>
				<input type="hidden" name="num" value="'.$liste[0].'"/>
				<input type="hidden" name="num_membre" value="'.$liste[6].'"/>
				<td class="tc1 '.$statut.'">'.$liste[2].'</td>';
	if (!$parse['scheme']){
		$line .= '<td class="tc2">'.$liste[3].'<input class="input zone-saisie" style="width:50%" type="text" name="flux" value="'.$liste[1].'" size="40" />&nbsp;&nbsp;<a href="'.$liste[1].'" target="_bank">'.T_('show').'</a></td>';
	}
	else {
		$line .= '<td class="tc2"><input class="input zone-saisie" style="width:80%" type="text" name="flux" value="'.$liste[1].'" size="40" />&nbsp;&nbsp;<a href="'.$liste[1].'" target="_bank">'.T_('show').'</a></td>';
	}
	
	$line .= '	<td class="tc3">'.$select.'</td>
				<td class="tc4 tcr">
					<center>
					<input class="button br3px" type="submit" name="submitModify" value="'.T_('Change').'"> 
					<input class="button br3px" type="submit" name="submitDelete" value="'.T_('Delete').'"> 
					</center>
				</td>
			</tr>
			</form>';
	echo $line;
}
?>
</table>
</center>
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
