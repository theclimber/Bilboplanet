<?php
if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# TOGGLE USER ROLE
##########################################################
	case 'toggleRole':
		$user_id = urldecode(trim($_POST['user_id']));
		$user_role = trim($_POST['user_role']);
		if ($user_id == $core->auth->userID()) {
			print '<div class="flash error">'.T_('Impossible to change your own role').'</div>';
		}
		else {
			$core->setUserRole($user_id, $user_role);
			print '<div class="flash notice">'.sprintf(T_('User %s is now know as %s'), $user_id, $user_role).'</div>';
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
			print '<div class="flash error">'.T_('Impossible to change your own role').'</div>';
		}
		else {
			$core->setUserPermissions($user_id, $manager_perm);
			print '<div class="flash notice">'.sprintf(T_('User %s has new permissions : %s'), $user_id, '('.implode(',',$manager_perm).')').'</div>';
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
			ORDER by user_fullname
			ASC LIMIT '.$num_start.','.$nb_items;
		$rs = $core->con->select($sql);

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
			$gravatar_email = strtolower($rs->user_email);
			$gravatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($gravatar_email)."&default=".urlencode($blog_settings->get('planet_url')."/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png")."&size=40";

			# Affichage de la ligne de tableau
			$output .= '<tr class="line '.$status.'"><td><img src="'.$gravatar_url.'"></td>
				<td><ul>
					<li>User id : '.$rs->user_id.'</li>
					<li>Fullname : '.$rs->user_fullname.'</li>
					<li>Email : '.$rs->user_email.'</li>
				</ul></div></td>';
			$output .= '<td>'.
				form::combo('role'.$rs->user_id, $roles, $user_perms->{'role'},'','input',false,'onchange="javascript:toggleUserRole(\''.$rs->user_id.'\',\''.$num_page.'\', \''.$nb_items.'\')"')
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
				$output .= '<form id="permissions'.$rs->user_id.'" class="managerPerm">'.
					form::hidden('user_id',$rs->user_id);
				$output .= form::checkbox('config'.$rs->user_id, 'configuration', $config_checked, 'input').
					'<label class="required'.$config_class.'" for="config'.$rs->user_id.'">'.T_('Configuration').
					'</label><br />';
				$output .= form::checkbox('admin'.$rs->user_id, 'administration', $admin_checked, 'input').
					'<label class="required'.$admin_class.'" for="admin'.$rs->user_id.'">'.T_('Administration').
					'</label><br />';
				$output .= form::checkbox('moder'.$rs->user_id, 'moderation', $moder_checked, 'input').
					'<label class="required'.$moder_class.'" for="moder'.$rs->user_id.'">'.T_('Moderation').
					'</label><br />';
				$output .= '<div class="button br3px"><input class="valide" type="button" name="submit" value="'.T_('Apply').'" onclick="javascript:toggleUserPermission(\''.$rs->user_id.'\', '.$num_page.', '.$nb_items.')" /></div>';
				$output .= "</form>";
			}
			$output .= '</td></tr>';
		}
		$output .= showPagination($rs->count(), $num_page, $nb_items, 'updateUserList');
		$output .= '</table>';

		print $output;
		break;
		
##########################################################
# DEFAULT RETURN
##########################################################
	default:
		print '<div class="flash error">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}
?>
