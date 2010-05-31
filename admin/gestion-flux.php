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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/fonctions.php');
connectBD();
debutCache();
$flash = '';
global $error;

if(isset($_POST) && (
    (isset($_POST['submitModify']) && !empty($_POST['submitModify'])) ||
    (isset($_POST['submitDelete']) && !empty($_POST['submitDelete'])) ||
    (isset($_POST['submitAjout']) && !empty($_POST['submitAjout']))
))
{
	$num  = trim($_POST['num']);
	$num_membre  = trim($_POST['num_membre']);
	$sql = "SELECT site_membre FROM membre WHERE num_membre='".$num_membre."'";
	$result1 = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
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
	$trust_flux = 1;
	if (isset($_POST['trust']) && empty($_POST['trust'])){
		$trust_flux = $_POST['trust'];
	}else if (isset($_GET['trust']) && empty($_GET['trust'])){
		$trust_flux = $_GET['trust'];
	}
	$flux['value'] = trim($_POST['flux']);
	if ($flux['success'] && !empty($num_membre)){
		if(isset($_POST) && isset($_POST['submitDelete']) && !empty($_POST['submitDelete'])) {
			$sql = "DELETE FROM flux WHERE num_flux='$num'";
			$flash = array('type' => 'notice', 'msg' => sprintf(T_("The feed %s was correctly deleted"),$flux['value']));
			$result = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
			if(!$result)
				$flash = array('type' => 'error', 'msg' => T_("Error while trying to change the informations of the feed"));
		}
		if(isset($_POST) && isset($_POST['submitAjout']) && !empty($_POST['submitAjout'])) {
			$sql = "SELECT url_flux FROM flux WHERE url_flux='".$flux['value']."' AND num_membre='".$num_membre."'";
			$result1 = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
			if (mysql_result($result1,0)){
				$flash = array('type' => 'error', 'msg' => sprintf(T_("The user already has a feed %s"),$flux['value']));
				$error['flux']=true;
			}
			else{
				$sql = "INSERT INTO flux (`num_flux`, `url_flux`, `num_membre`, `trust`) VALUES ('', '".$flux['value']."', '$num_membre', '$trust_flux')";
				$result = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
				if(!$result)
					$flash = array('type' => 'error', 'msg' => T_("Error while trying to change the informations of the feed"));
			}
			$flash = array('type' => 'notice', 'msg' => sprintf(T_("Adding the feed %s succeeded"),$flux['value']));
		}
		if(isset($_POST) && isset($_POST['submitModify']) && !empty($_POST['submitModify'])) {
			$statut = trim($_POST['statut']);
			$sql = "UPDATE flux 
				SET url_flux = '".$flux['value']."', status_flux = '$statut', trust = '$trust_flux'
				WHERE num_flux = '$num'";
			$flash = array('type' => 'notice', 'msg' => sprintf(T_("Changing the feed %s succeeded"),$flux['value']));
			$result = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
			if(!$result)
				$flash = array('type' => 'error', 'msg' => T_("Error while trying to change the informations of the feed"));
		}
		closeBD();
	}
	else {
		if (empty($num_membre)){
			$flash = array('type' => 'error', 'msg' => T_('You are not allowed to create a feed without selecting an user'));
		} else {
			if(isset($_POST) && isset($_POST['submitAjout']) && !empty($_POST['submitAjout'])) {
				$error['flux']=true;
			}
			$flash = array('type' => 'error', 'msg' => $flux['error']);
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

<fieldset><legend><?php echo T_('Add a feed');?></legend>
	<div class="message">
		<p><?php echo T_("Manage member's feeds"); ?></p>
	</div>
		
<br/>

<center>
<?php error_bool($error, "flux"); ?>
<table class="table-flux-add">
		<thead>
			<tr>
				<th class="tc1 tcl" scope="col"><?php echo T_('Full url of the feed');?></th>
				<th class="tc2 tcr" scope="col"><?php echo T_('Name of the user');?></th>
				<?php if (MODERATION == true):?><th class="tc2 tcr" scope="col"><?php echo T_('Trust URL');?></th><?php endif;?>
			</tr>
		</thead>
			<tr>
			<form method="post">
				<td class="tc1 tcl row2">
					<input class="input" value="<?php if($error["flux"]) { echo $_POST['flux']; } else { echo 'http://www.exemple.com/feed';}?>" onfocus="if (this.value==='http://www.exemple.com/feed') {this.value='';}" size="49" type="text" name="flux" />
				</td>
				<td class="tc2 row2">
					<center>
					<select name="num_membre" class="userslist">
					<?php
					# Connection a la base 
					connectBD();

					# Execution de la requete
					$sql = 'SELECT num_membre, nom_membre FROM membre ORDER BY nom_membre ASC;';
					$rqt = mysql_query($sql) or die("Error with request $sql : ".mysql_error());

					# Traitement de la liste
					while($liste = mysql_fetch_row($rqt)) {
					  echo '<option value="'.$liste[0].'">'.$liste[1].'</option>';
					}
					?>
					</select>
					</center>
				</td>
				<?php if (MODERATION == true):?>
				<td class="tc1 tcl row2">
					<select name="trust" class="userlist">
						<option value="1"><?php echo T_('true')?></option>
						<option value="0" selected=""><?php echo T_('false')?></option>
					</select>
				</td>
				<?php endif;?>
			</tr>
</table>
<br />
<div class="button"><input type="reset" class="reset" name="reset" onClick="this.form.reset()" value="<?php echo T_('Reset');?>"></div>
<div class="button"><input type="submit" name="submitAjout" class="valide" value="<?php echo T_('Send');?>"></div>
</center>
</form>
</fieldset>

<fieldset><legend><?php echo T_('Manage feeds');?></legend>
	<div class="message">
		<p><?php echo T_('Manage member feed.');?></p>
	</div>
	

<?php
# On recupere les informtions sur les membres
$sql = 'SELECT nom_membre, site_membre, email_membre, statut_membre FROM membre ORDER by nom_membre ASC';
$rqt = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
?>
<center>
<table class="table-flux" >
		<thead>
			<tr>
				<th class="tc1 tcl" scope="col"><?php echo T_('Name');?></th>
				<th class="tc2" scope="col"><?php echo T_('URL of the feed');?></th>
				<th class="tc3" scope="col"><?php echo T_('Status');?></th>
				<th class="tc4 tcr" scope="col"><?php echo T_('Action');?></th>
				<?php if (MODERATION == true):?>
				<th class="tc3" scope="col"><?php echo T_('Trust URL');?></th>
				<?php endif;?>
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
$sql = 'SELECT num_flux, url_flux, nom_membre, site_membre,statut_membre,status_flux,membre.num_membre,trust
	FROM flux, membre 
	WHERE flux.num_membre = membre.num_membre 
	ORDER by nom_membre ASC
	LIMIT '.$num_start.','.$nb_items;
$rqt = mysql_query($sql) or die("Error with request $sql : ".mysql_error());
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
	if ($liste[7])
	{
		$select2  = '<select name="trust" class="actif">';
		$select2 .= '<option value="1" selected>'.T_('true').'</option>';
		$select2 .= '<option value="0">'.T_('false').'</option></select>';
		$statut2  = "true";
	} else {
		$select2  = '<select name="trust" class="inactif">';
		$select2 .= '<option value="0" selected>'.T_('false').'</option>';
		$select2 .= '<option value="1">'.T_('true').'</option></select>';
		$statut2  = "false";
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
	$line .= '	<td class="tc3">'.$select.'</td>';
	if (MODERATION == true)
			$line .= '<td class="tc3">'.$select2.'</td>';
	$line .= '	<td class="tc4 tcr">
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
<?php echo T_('Show items by : ');?> <a href="?<?php echo $params; ?>nb_items=10">10</a>, <a href="?<?php echo $params; ?>nb_items=20">20</a>, <a href="?<?php echo $params; ?>nb_items=50">50</a>
</div>

<?php 
closeBD();
include(dirname(__FILE__).'/footer.php');
finCache();
?>
