<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
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
?><?php
if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# USERS PENDING LIST RETURN
##########################################################
	case 'list':
		$num_page = !empty($_POST['num_page']) ? $_POST['num_page'] : 0;
		$nb_items = !empty($_POST['nb_items']) ? $_POST['nb_items'] : 30;
		$num_start = $num_page * $nb_items;

		# On recupere les informtions sur les membres
		$sql = 'SELECT
			puser_id,
			user_fullname,
			user_email ,
			site_url,
			feed_url,
			created
			FROM '.$core->prefix.'pending_user
			ORDER by created ASC 
			LIMIT '.$num_start.','.$nb_items;

		print getOutput($sql, $num_page, $nb_items);
		break;
		
##########################################################
# REFUSE PENDING USER
##########################################################
	case 'refuse':
		$puserid = trim($_POST['puserid']);
		$rs = $core->con->select("SELECT puser_id, user_fullname, user_email, site_url, feed_url FROM ".$core->prefix."pending_user WHERE puser_id = '$puserid'");
		$confirmation = '<p>'.T_('Are you sure you want to refuse this subscription?').'
			<ul>
				<li>'.T_('User id').': '.$rs->f('puser_id').'</li>
				<li>'.T_('Fullname').': '.$rs->f('user_fullname').'</li>
				<li>'.T_('Email').': '.$rs->f('user_email').'</li>
				<li>'.T_('Website').': '.$rs->f('site_url').'</li>
				<li>'.T_('Feed').': '.$rs->f('feed_url').'</li>				
			</ul><br />';
		$confirmation .= '<form id="refuseSubscription" method="POST"><input type="hidden" name="p_userid" value="'.$puserid.'"/>';
		$confirmation .= "<div class='button br3px'><input class='reset' type='button' value='".T_('Reset')."'/></div>&nbsp;&nbsp;";
		$confirmation .= "<div class='button br3px'><input class='valide' type='submit' name='confirm' value='".T_('Confirm')."'/></div></form></p>";
		print '<div class="flash_warning">'.$confirmation.'</div>';
		break;
		
##########################################################
# DEFAULT RETURN
##########################################################
	default:
		print '<div class="flash_error">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}

function getOutput($sql, $num_page=0, $nb_items=30) {
	global $core, $blog_settings;

	$rs = $core->con->select($sql);
	$output .= showPagination($rs->count(), $num_page, $nb_items, 'updateUserList');

	$output .= '
<br />
<table id="userlist" class="table-member">
<thead>
	<tr>
		<th class="tc7 tcr" scope="col">'.T_('Avatar').'</th>
		<th class="tc9 tcr" scope="col">'.T_('User Informations').'</th>
		<th class="tc8 tcr" scope="col">'.T_('Website').'&nbsp;&amp;&nbsp;'.T_('Feed').'</th>
		<th class="tc10 tcr" scope="col">'.T_('Action').'</th>
	</tr>
</thead>';
	
	while($rs->fetch()) {
		$gravatar_email = strtolower($rs->user_email);
		$gravatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($gravatar_email)."&default=".urlencode($blog_settings->get('planet_url')."/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png")."&size=40";

		$output .= '<tr class="line">
			<td style="text-align: center;">
				<img src="'.$gravatar_url.'">
			</td>';
			
		$output .= '<td>
				<ul>
					<li>'.T_('User id').' : '.$rs->puser_id.'</li>
					<li>'.T_('Fullname').' : '.html_entity_decode(stripslashes($rs->user_fullname), ENT_QUOTES, 'UTF-8').'</li>
					<li>'.T_('Email').' : '.$rs->user_email.'</li>
				</ul>
			</td>';
		
		$output .= '<td>
				<ul>
					<li>'.T_('Website').':&nbsp;<a href="'.$rs->site_url.'" target="_blank" title="'.$rs->site_url.'">'.$rs->site_url.'</a></li>
					<li>'.T_('Feed').':&nbsp;<a href="'.$rs->feed_url.'" target="_blank" title="'.$rs->feed_url.'">'.$rs->feed_url.'</a></li>
					
				</ul>
			</td>';
		$output .= '<td style="text-align: center;">
				<a href="#" onclick="javascript:refusePendingUser(\''.urlencode($rs->puser_id).'\')" >
					<img src="meta/icons/action-remove.png" title="'.T_("Refuse").'"/>
				</a>
				&nbsp;&nbsp;
				<a href="#" onclick="javascript:acceptPendingUser('.urlencode($rs->puser_id).')" >
					<img src="meta/icons/action-add.png" title="'.T_("Accept").'"/>
				</a>
			</td>';
		$output .= '</tr>';
	}
	$output .= '</table>';
	$output .= showPagination($rs->count(), $num_page, $nb_items, 'updatePendingUserList');

	return $output;
}
?>
