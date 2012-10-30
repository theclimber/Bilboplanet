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
?><?php
if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# TOGGLE USER ROLE
##########################################################
	case 'toggleRole':
		$user_id = urldecode(trim($_POST['user_id']));
		$user_role = trim($_POST['user_role']);
		if ($user_id == $core->auth->userID()) {
			print '<div class="flash_error">'.T_('Impossible to change your own role').'</div>';
		}
		else {
			if (!empty($user_role)) {
				$core->setUserRole($user_id, $user_role);
				print '<div class="flash_notice">'.sprintf(T_('User %s is now know as %s'), $user_id, $user_role).'</div>';
			}
			else {
				print '<div class="flash_error">'.T_('There was a problem during toggling user role').'</div>';
			}
		}
		break;

##########################################################
# TOGGLE USER PERMISSIONS
##########################################################
	case 'togglePerms':
		$user_id = urldecode(trim($_POST['user_id']));
		$admin = (trim($_POST['admin']));
		$config = (trim($_POST['config']));
		$moder = (trim($_POST['moder']));

		$manager_perm = array();
		if ($admin == "set") {
			$manager_perm[] = "administration";
		}
		if ($config == "set") {
			$manager_perm[] = "configuration";
		}
		if ($moder == "set") {
			$manager_perm[] = "moderation";
		}

		if ($user_id == $core->auth->userID()) {
			print '<div class="flash_error">'.T_('Impossible to change your own role').'</div>';
		}
		else {
			$core->setUserPermissions($user_id, $manager_perm);
			print '<div class="flash_notice">'.sprintf(T_('User %s has new permissions : %s'), $user_id, '('.implode(',',$manager_perm).')').'</div>';
		}
		break;

##########################################################
# USERS LIST RETURN
##########################################################
	case 'list':
		$num_page = !empty($_POST['num_page']) ? $_POST['num_page'] : 0;
		$nb_items = !empty($_POST['nb_items']) ? $_POST['nb_items'] : 30;
		$num_start = $num_page * $nb_items;

		$next_page = $num_page + 1;
		$prev_page = $num_page - 1;

		# On recupere les informtions sur les membres
		$sql = 'SELECT
			user_id,
			user_fullname,
			user_email,
			user_status
			FROM '.$core->prefix.'user
			ORDER by lower(user_fullname)
			ASC LIMIT '.$nb_items.' OFFSET '.$num_start;
		$rs = $core->con->select($sql);

		$nb = 0;
		$output .= showPagination($rs->count(), $num_page, $nb_items, 'updateUserList');
		$output .= '
<br /><br />
<table id="userlist" class="table-member">
<thead>
		<tr>
			<th class="tc7 tcr" scope="col">'.T_('Avatar').'</th>
			<th class="tc9 tcr" scope="col">'.T_('User Informations').'</th>
			<th class="tc11 tcr" scope="col">'.T_('Role').'</th>
			<th class="tc11 tcr" scope="col">'.T_('Permissions').'</th>
		</tr>
</thead>';
		$roles = array(
			T_('Normal user') => 'user',
			T_('Website manager') => 'manager',
			T_('Super user') => 'god');
		# On affiche la liste de membres
		while($rs->fetch()) {
			$user_perms = $core->getUserRolePermissions($rs->user_id);
			if($rs->user_status) {
				$status = 'active';
			} else {
				$status = 'inactive';
			}
			$avatar_email = strtolower($rs->user_email);
			$avatar_url = "http://cdn.libravatar.org/avatar/".md5($avatar_email)."?d=".urlencode(BP_PLANET_URL."/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png")."&s=40";

			# Affichage de la ligne de tableau
			$output .= '<tr class="line '.$status.'"><td><img src="'.$avatar_url.'"></td>
				<td><ul>
					<li>User id : '.$rs->user_id.'</li>
					<li>Fullname : '.$rs->user_fullname.'</li>
					<li>Email : '.$rs->user_email.'</li>
				</ul></div></td>';
			$output .= '<td>'.
				form::combo('role'.$nb, $roles, $user_perms->{'role'},'','input',false,'onchange="javascript:toggleUserRole('.$nb.', \''.urlencode($rs->user_id).'\',\''.$num_page.'\', \''.$nb_items.'\')"')
				.'</td>';
			$output .= '<td>';
			if ($user_perms->{'role'} == 'manager') {

				$config_class = ' red';
				$admin_class = ' red';
				$moder_class = ' red';
				$config_checked = false;
				$admin_checked = false;
				$moder_checked = false;

				if (array_key_exists('configuration', $core->auth->parsePermissions($user_perms->{'permissions'}))) {
					$config_class = ' green';
					$config_checked = true;
				}
				if (array_key_exists('administration', $core->auth->parsePermissions($user_perms->{'permissions'}))) {
					$admin_class = ' green';
					$admin_checked = true;
				}
				if (array_key_exists('moderation', $core->auth->parsePermissions($user_perms->{'permissions'}))) {
					$moder_class = ' green';
					$moder_checked = true;
				}
				$output .= '<form id="permissions'.urlencode($rs->user_id).'" class="managerPerm">'.
					form::hidden('user_id',urlencode($rs->user_id));
				$output .= form::checkbox('config'.$nb, 'configuration', $config_checked, 'input').
					'<label class="required'.$config_class.'" for="config'.$nb.'">'.T_('Configuration').
					'</label><br />';
				$output .= form::checkbox('admin'.$nb, 'administration', $admin_checked, 'input').
					'<label class="required'.$admin_class.'" for="admin'.$nb.'">'.T_('Administration').
					'</label><br />';
				$output .= form::checkbox('moder'.$nb, 'moderation', $moder_checked, 'input').
					'<label class="required'.$moder_class.'" for="moder'.$nb.'">'.T_('Moderation').
					'</label><br />';
				$output .= '<div class="button br3px"><input class="valide" type="button" name="submit" value="'.T_('Apply').'" onclick="javascript:toggleUserPermission('.$nb.', \''.urlencode($rs->user_id).'\', '.$num_page.', '.$nb_items.')" /></div>';
				$output .= "</form>";
			}
			$output .= '</td></tr>';
			$nb = $nb + 1;
		}
		$output .= '</table>';
		$output .= showPagination($rs->count(), $num_page, $nb_items, 'updateUserList');

		print $output;
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
?>
