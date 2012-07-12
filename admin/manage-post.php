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
debutCache();
# Parametre par defaut
$num_membre = 0;
$flash='';
$status_article = "all";

# Valeurs par defaut
$num_page = 0;
$num_start = 0;
$nb_items = 30;


# Verification du contenu du get
if (isset($_POST) && isset($_POST['nb_items']) && !empty($_POST['nb_items'])){
	$nb_items = $_POST['nb_items'];
}
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

# Si il y a filtrage sur le membre
if(isset($_GET['num_membre']) && !empty($_GET['num_membre'])) {
	$num_membre = urldecode(trim($_GET['num_membre']));
}
elseif(isset($_POST['num_membre']) && trim($_POST['num_membre'])) {
	$num_membre = urldecode(trim($_POST['num_membre']));
}
if(isset($_GET['status_article']) && !empty($_GET['status_article'])) {
	$status_article = trim($_GET['status_article']);
}
elseif(isset($_POST['num_membre']) && is_numeric(trim($_POST['status_article']))) {
	$status_article = trim($_POST['status_article']);
}

if(isset($_POST) && (
    (isset($_POST['submitModif']) && !empty($_POST['submitModif'])) ||
    (isset($_POST['submitDelete']) && !empty($_POST['submitDelete']))
))

# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['num']) && isset($_POST['statut'])) {
	# On recupere les infos
	$num = trim($_POST['num']);
	$title = trim($_POST['title']);
	$statut = trim($_POST['statut']);
	$action = trim($_POST['submitModif']);
	$action = trim($_POST['submitDelete']);

	# On insert une nouvelle entree
	if(isset($_POST) && isset($_POST['submitDelete']) && !empty($_POST['submitDelete'])){
		$sql = "DELETE FROM ".$core->prefix."post WHERE post_id ='".$num."'";
		$core->con->execute($sql);
		$flash = array('type' => 'notice', 'msg' => sprintf(T_("The post %s was removed"),$title));
	}
	else {
		$cur = $core->con->openCursor($core->prefix.'post');
		$cur->post_status = $statut;
		$cur->modified = array(' NOW() ');
		$cur->update("WHERE post_id = '$num'");
		$flash = array('type' => 'notice', 'msg' => sprintf(T_("The post %s was changed"),$title));
	}
}

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>
<script type="text/javascript" src="meta/js/manage-post.js"></script>
<div id="BP_page" class="page">
	<div class="inpage">

<?php
if (!empty($flash)) {
	echo '<div id="post_flash" class="flash_'.$flash['type'].'" style="display:none;">'.$flash['msg'].'</div>';
}
?>

<fieldset><legend><?php echo T_('Filtering of the posts');?></legend>
		<div class="message">
			<p><?php echo T_('Filter articles');?></p>
		</div><br />

<form action="" method="POST">
<table class="table-news-filter">
<tr>
<th class="tc1 tcl"><?php echo T_('Posts of the member :');?></th>
<th class="tc2 tcr"><select name="num_membre" class="userslist">

<?php
# Execution de la requete
$sql = 'SELECT user_id, user_fullname FROM '.$core->prefix.'user ORDER BY lower(user_fullname) ASC;';
$rs = $core->con->select($sql);

# Traitement de la liste
while($rs->fetch()) {
	if($num_membre == $rs->user_id) {
		echo '<option value="'.urlencode($rs->user_id).'" selected>'.$rs->user_fullname.'</option>';
	} else {
		echo '<option value="'.urlencode($rs->user_id).'">'.$rs->user_fullname.'</option>';
	}
}

# On rajoute l'option Tous
if($num_membre == "0") {
	echo '<option value="0" selected>'.T_('All').'</option>';
} else {
	echo '<option value="0">'.T_('All').'</option>';
}
?>
</select></th>
<?php if ($blog_settings->get('planet_moderation')):?>
 </tr>
 <tr>
<td class="tc1 tcl"><?php echo T_('Post status');?> :</th>
<td class="tc2 tcr">
	<select name="status_article" class="userslist">
			<option value="all"><?php echo T_('All')?></option>
			<option value="0" <?php if ($status_article == '0') echo "selected";?>><?php echo T_('inactive')?></option>
			<option value="1" <?php if ($status_article == '1') echo "selected";?>><?php echo T_('active')?></option>
			<option value="2" <?php if ($status_article == '2') echo "selected";?>><?php echo T_('pending')?></option>
	</select>
</th>
</tr>
<?php endif;?>
<tr>
<td class="tc1 tcl"><?php echo T_('Number of posts');?></td>
<td class="tc2 tcr"><input type="text" class="nbfilter input" name="nb_items"  value="<?php echo $nb_items; ?>" /></center></td>
</tr>
</table><br />
<div class="button br3px"><input type="reset" value="<?php echo T_('Reset');?>" class="reset" onClick="this.form.reset()"></div>&nbsp;&nbsp;
<div class="button br3px"><input type="submit" class="valide" value="<?php echo T_('Send');?>"></div></p>

<br/>
</form>
</fieldset>

<fieldset><legend><?php echo T_('List of the posts')?></legend>
		<div class="message">
			<p><?php echo T_('NOTE: If you delete a post which is too recent, it will be refetched next time the update happen !!');?></p>
		</div><br />

<?php

# Debut de la requete
$sql = "SELECT
		post_id,
		".$core->prefix."user.user_id as user_id,
		post_pubdate as pubdate,
		post_title as title,
		post_status as status,
		post_score as score,
		post_permalink as permalink
	FROM ".$core->prefix."post, ".$core->prefix."user
	WHERE ".$core->prefix."post.user_id = ".$core->prefix."user.user_id ";

# Si on filtre un membre
if($num_membre != '0') $sql .= "AND ".$core->prefix."post.user_id = '".$num_membre."'";

#si on filtre un status
if($status_article != "all" && is_numeric($status_article)) $sql .= " AND ".$core->prefix."post.post_status = '$status_article'";

# Fin de la requete
$sql .= "ORDER by pubdate DESC LIMIT $nb_items OFFSET $num_start";

# Execution de la requete
$rs = $core->con->select($sql);
$nb = $rs->count();

include(dirname(__FILE__).'/pagination.php');
?><br /><br />
<table id="tbl1" class="table-news">
		<thead>
			<tr>
				<th class="tc1 tcr" scope="col"><?php echo T_('Name');?></th>
				<th class="tc2" scope="col"><?php echo T_('Date');?></th>
				<th class="tc3" scope="col"><?php echo T_('Title');?></th>
				<th class="tc4" scope="col" ><?php echo T_('Status');?></th>
				<th class="tc5" scope="col"><?php echo T_('Nb votes');?></th>
				<th class="tc6 tcr" scope="col"><?php echo T_('Action');?></th>
			</tr>
		</thead>

<?php

/* Traitement de la liste */
while($rs->fetch()) {

	$date = mysqldatetime_to_date("d/m/Y",$rs->pubdate);

	# Couleur de la ligne en fonciton du statut du membre
	if($rs->status == "1") {
		$select  = '<select name="statut" class="actif">';
		$select .= '<option value="1" selected>'.T_('active').'</option>';
		$select .= '<option value="0">'.T_('inactive').'</option>';
		if ($blog_settings->get('planet_moderation'))
			$select .= '<option value="2">'.T_('pending').'</option>';
		$select .= '</select>';
		$statut  = T_("active");
		$style = 'white';
	} elseif ($rs->status == "0" || ($rs->status == "2" && !$blog_settings->get('planet_moderation'))) {
		$select  = '<select name="statut" class="inactif">';
		$select .= '<option value="0" selected>'.T_('inactive').'</option>';
		$select .= '<option value="1">'.T_('active').'</option>';
		if ($blog_settings->get('planet_moderation'))
			$select .= '<option value="2">'.T_('pending').'</option>';
		$select .= '</select>';
		$style = '#FFFF99';
		$statut  = T_("inactive");
	} else {
		$select  = '<select name="statut" class="inactif">';
		$select .= '<option value="2" selected>'.T_('pending').'</option>';
		$select .= '<option value="0">'.T_('inactive').'</option>';
		$select .= '<option value="1">'.T_('active').'</option></select>';
		$statut  = T_("pending");
		$style = '#FFCC66';
	}
	$style = 'style="background-color:'.$style.';"';

	echo '<tr><form method="POST">
		<input type="hidden" name="num" value="'.$rs->post_id.'"/>
		<input type="hidden" name="title" value="'.$rs->title.'"/>
		<td class="tc1 tcl" '.$style.'>'.$rs->user_id.'</td>
		<td class="tc2" '.$style.'>'.$date.'</td>
		<td class="tc3" '.$style.'><a href="'.$rs->permalink.'" target="_blank">'.substr($rs->title,0,70).'</a></td>
		<td class="tc4" '.$style.'>'.$select.'</td>
		<td class="tc5" '.$style.'>'.$rs->score.'</td>
		<td class="tc6 tcr" '.$style.'><center>
			<input type="submit" class="button br3px" name="submitModif" value="'.T_('Change').'" />
			<input type="submit" class="button br3px" name="submitDelete" value="'.T_('Delete').'" />
			</center>';
	if($num_membre != 0 || $nb_items != 10) {
		echo '<input type="hidden" id="num_membre" name="num_membre" value="'.urlencode($num_membre).'" />';
		echo '<input type="hidden" id="nb_items" name="nb_items" value="'.$nb_items.'" /></td>';
		echo '<input type="hidden" id="status_article" name="status_article" value="'.$status_article.'" />';
	}
	echo '</form></tr>';
}
?>
</table>

<?php
$params = "page=$num_page&";
?>
<div class="nbitems">
<?php echo T_('Show items by : ');?> <a href="?<?php echo $params; ?>nb_items=10">10</a>, <a href="?<?php echo $params; ?>nb_items=20">20</a>, <a href="?<?php echo $params; ?>nb_items=50">50</a>
</div>
<?php
include(dirname(__FILE__).'/footer.php');
finCache();
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
