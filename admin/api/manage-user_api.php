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
# GET USER PROFILE
##########################################################
	case 'profile':
		$user_id = urldecode(trim($_POST['user_id']));
		$rs = $core->con->select("SELECT * FROM ".$core->prefix."user WHERE user_id = '$user_id'");
		$user = array(
			"user_id" => $rs->f('user_id'),
			"user_fullname" => html_entity_decode(stripslashes($rs->f('user_fullname')), ENT_QUOTES, 'UTF-8'),
			"user_email" => $rs->f('user_email'),
			"user_lang" => $rs->f('user_lang'),
			"user_status" => $rs->f('user_status')
			);
		print json_encode($user);
		break;

##########################################################
# GET USERS LIST
##########################################################
	case 'get-users-list':
		$rs = $core->con->select("SELECT * FROM ".$core->prefix."user");
		$users = array();
		while($rs->fetch()){
			$users[] = array(
				"user_id" => $rs->user_id,
				"user_email" => $rs->user_email,
				"user_fullname" => $rs->user_fullname,
				"user_status" => $rs->user_status,
				"user_lang" => $rs->user_lang
				);
		}
		print json_encode($users);
		break;

##########################################################
# ADD USER
##########################################################
	case 'add':
		$user_id = check_field('user_id',trim($_POST['user_id']));
		$user_id['value'] = preg_replace("( )", "_", $user_id['value']);
		$user_fullname = check_field('fullname',trim($_POST['fullname']));
		$user_email = check_field('email',trim($_POST['email']),'email');
		$user_password = check_field('password', array("password" => trim($_POST['password']), "password2" => trim($_POST['password2'])), 'password');
		if(empty($_POST['site'])) {
			$user_site = check_field('site',trim($_POST['site']),'', false);
		} else {
			$user_site = check_field('site',trim($_POST['site']),'url');
		}
		$error = array();

		if ($user_id['success']
			&& $user_email['success']
			&& $user_fullname['success']
			&& $user_password['success']
			&& $user_site['success'])
		{
			if(!preg_match('/^[0-9A-Za-z_\-]+$/',$user_id['value'])) {
				$error[] = T_('Please do not use special chars in user_id field. Allowed chars are only a-z, A-Z and 0-9');
			}
			$user_id['value'] = htmlentities($user_id['value'],ENT_QUOTES,mb_detect_encoding($user_id['value']));
			$user_fullname['value'] = htmlentities($user_fullname['value'],ENT_QUOTES,mb_detect_encoding($user_fullname['value']));

			$rs1 = $core->con->select("SELECT user_id, user_fullname, user_email FROM ".$core->prefix."user
				WHERE lower(user_id) = '".strtolower($user_id['value'])."'
				OR lower(user_fullname) = '".strtolower($user_fullname['value'])."'
				OR lower(user_email) = '".strtolower($user_email['value'])."'");
			if ($rs1->count() > 0){
				if ($rs1->f('user_id') == $user_id['value']) {
					$error[] = sprintf(T_('The user %s already exists'),$user_id['value']);
				}
				if ($rs1->f('user_fullname') == $user_fullname['value']) {
					$error[] = sprintf(T_('The user %s already exists'),$user_fullname['value']);
				}
				if ($rs1->f('user_email') == $user_email['value']) {
					$error[] = sprintf(T_('The email address %s is already in use'),$user_email['value']);
				}
			}

			$rs2 = $core->con->select("SELECT ".$core->prefix."user.user_id FROM ".$core->prefix."user, ".$core->prefix."site
				WHERE ".$core->prefix."site.user_id = ".$core->prefix."user.user_id AND site_url = '".$site['value']."'");
			if ($rs2->count() > 0){
				$error[] = sprintf(T_('The website %s is already assigned to the user %s'),$user_site['value'], $user_id['value']);
			}

			if (empty($error)) {
				$cur = $core->con->openCursor($core->prefix.'user');
				$cur->user_id = $user_id['value'];
				$cur->user_fullname = $user_fullname['value'];
				$cur->user_email = $user_email['value'];
				$cur->user_pwd = crypt::hmac('BP_MASTER_KEY',$user_password['value']);
				$cur->user_token = generateUserToken($user_fullname['value'],$user_email['value'],$password['value']);
				$cur->user_status = 1;
				$cur->user_lang = 'en';
				$cur->created = array(' NOW() ');
				$cur->modified = array(' NOW() ');
				$cur->insert();

				if (!empty($user_site['value'])) {
					# Get next ID
					$rs3 = $core->con->select(
						'SELECT MAX(site_id) '.
						'FROM '.$core->prefix.'site '
						);
					$next_site_id = (integer) $rs3->f(0) + 1;
					$cur = $core->con->openCursor($core->prefix.'site');
					$cur->site_id = $next_site_id;
					$cur->user_id = $user_id['value'];
					$cur->site_name = '';
					$cur->site_url = $user_site['value'];
					$cur->site_status = 1;
					$cur->created = array(' NOW() ');
					$cur->modified = array(' NOW() ');
					$cur->insert();
				}

				$core->setUserRole($user_id['value'], 'user');
				$output = sprintf(T_("User %s successfully added"),$user_id['value']);
			}
		} else {
			if (!$user_id['success']) {
				$error[] = $user_id['error'];
			}
			if (!$user_fullname['success']) {
				$error[] = $user_fullname['error'];
			}
			if (!$user_email['success']) {
				$error[] = $user_email['error'];
			}
			if (!$user_password['success']) {
				$error[] = $user_password['error'];
			}
			if (!$user_site['success']) {
				$error[] = $user_site['error'];
			}
		}

		if (!empty($error)) {
			$output .= "<ul>";
			foreach($error as $value) {
				$output .= "<li>".$value."</li>";
			}
			$output .= "</ul>";
			print '<div class="flash_error">'.$output.'</div>';
		}
		else {
			print '<div class="flash_notice">'.$output.'</div>';
		}
		break;

##########################################################
# TOGGLE USER
##########################################################
	case 'toggle':
		$user_id = urldecode(trim($_POST['user_id']));
		$user = $core->con->select("SELECT user_status FROM ".$core->prefix."user WHERE user_id = '$user_id'");

		$cur = $core->con->openCursor($core->prefix.'user');
		if($user->f('user_status') == 1) {
			$cur->user_status = 0;
		} else {
			$cur->user_status = 1;
		}
		$cur->update("WHERE user_id = '$user_id'");

		print '<div class="flash_notice">'.T_('User status toggled').'</div>';
		break;

##########################################################
# UPDATE USER
##########################################################
	case 'update':
		$user_id = urldecode(trim($_POST['user_id']));
		$user = $core->con->select("SELECT * FROM ".$core->prefix."user WHERE user_id = '$user_id'");

		$new_fullname = !empty($_POST['efullname']) ? $_POST['efullname'] : $user->f('user_fullname');
		$new_email = !empty($_POST['eemail']) ? $_POST['eemail'] : $user->f('user_email');

		$new_fullname = check_field('fullname',$new_fullname);
		$new_email = check_field('email',$new_email,'email');
		$new_password = check_field('password', array("password" => trim($_POST['password']), "password2" => trim($_POST['password2'])), 'password', false);

		$error = array();

		if ($new_email['success']
			&& $new_fullname['success']
			&& $new_password['success'])
		{
			$new_fullname['value'] = htmlentities($new_fullname['value'],ENT_QUOTES,mb_detect_encoding($new_fullname['value']));

			$sql = "SELECT user_id, user_fullname, user_email FROM ".$core->prefix."user
				WHERE lower(user_id) != '".strtolower($user_id)."'
				AND (lower(user_fullname) = '".strtolower($new_fullname['value'])."'
				OR lower(user_email) = '".strtolower($new_email['value'])."')";
			$rs1 = $core->con->select($sql);
			if ($rs1->count() > 0){
				if ($rs1->f('user_fullname') == $new_fullname['value']) {
					$error[] = sprintf(T_('The user %s already exists'),$new_fullname['value']);
				}
				if ($rs1->f('user_email') == $new_email['value']) {
					$error[] = sprintf(T_('The email address %s is already in use by %s'),$new_email['value'], $rs1->f('user_id'));
				}
			}

			if (empty($error)) {
				$cur = $core->con->openCursor($core->prefix.'user');
				$cur->user_fullname = $new_fullname['value'];
				$cur->user_email = $new_email['value'];
				if (!empty($new_password['value'])) {
					$cur->user_pwd = crypt::hmac('BP_MASTER_KEY',$new_password['value']);
				}
				$cur->modified = array(' NOW() ');
				$cur->update("WHERE user_id = '$user_id'");

				$user_perms = $core->getUserRolePermissions($user_id);
				if ($user_perms->{'role'} == "god") {
					$blog_settings->put('author_mail', $new_email['value'], "string");
					$blog_settings->put('author', $new_fullname['value'], "string");
				}

				$output = sprintf(T_("User %s successfully updated"),$new_id['value']);
			}
		} else {
			if (!$new_fullname['success']) {
				$error[] = $new_fullname['error'];
			}
			if (!$new_email['success']) {
				$error[] = $new_email['error'];
			}
			if (!$new_password['success']) {
				$error[] = $new_password['error'];
			}
		}

		if (!empty($error)) {
			$output .= "<ul>";
			foreach($error as $value) {
				$output .= "<li>".$value."</li>";
			}
			$output .= "</ul>";
			print '<div class="flash_error">'.$output.'</div>';
		}
		else {
			print '<div class="flash_notice">'.$output.'</div>';
		}
		break;

##########################################################
# REMOVE USER
##########################################################
	case 'remove':
		$user_id = urldecode(trim($_POST['user_id']));
		$user_perms = $core->getUserRolePermissions($user_id);
		if ($user_perms->{'role'} == "god") {
			print '<div class="flash_error">'.T_('You are not allowed to remove a super user').'</div>';
		}
		else {
			$user = $core->con->select("SELECT user_fullname as fullname FROM ".$core->prefix."user WHERE user_id = '$user_id'");
			$confirmation = "<p>".sprintf(T_('Are you sure you want to remove user %s ?'),$user->f('fullname'))."?<br/>";
			$confirmation .= "<ul><li>".T_('This action can not be canceled')."</li>";
			$confirmation .= "<li>".T_('All the posts of the user will be removed')."</li>";
			$confirmation .= "<li>".T_('All the votes on these posts will be removed')."</li>";
			$confirmation .= "<li>".T_('All the feeds of this user will be removed')."</li>";
			$confirmation .= "<li>".T_('All the tribes of this user will be removed')."</li>";
            $confirmation .= "<li>".T_('All the settings of this user will be removed')."</li>";
            $confirmation .= "</ul><br/>";
			$confirmation .= "<form id='removeConfirm_form'><input type='hidden' name='user_id' value='".urlencode($user_id)."'/>";
			$confirmation .= "<div class='button br3px'><input type='button' class='reset' value='".T_('Reset')."'/></div>&nbsp;&nbsp;";
			$confirmation .= "<div class='button br3px'><input type='submit' class='valide' name='confirm' value='".T_('Confirm')."'/></div></form></p>";
			print '<div class="flash_warning">'.$confirmation.'</div>';
		}
		break;

##########################################################
# CONFIRM REMOVE USER
##########################################################
	case 'removeConfirm':
		sleep(1);
		$user_id = urldecode(trim($_POST['user_id']));
		$user_perms = $core->getUserRolePermissions($user_id);
		if ($user_perms->{'role'} == "god") {
			print '<div class="flash_error">'.T_('You are not allowed to remove a super user').'</div>';
		}
		else {
			$user = $core->con->select("SELECT user_fullname as fullname FROM ".$core->prefix."user WHERE user_id = '$user_id'");
			$core->con->execute("DELETE FROM ".$core->prefix."votes
				USING ".$core->prefix."post, ".$core->prefix."votes
				WHERE ".$core->prefix."post.post_id = ".$core->prefix."votes.post_id
				AND ".$core->prefix."post.user_id = '$user_id'");
			$core->con->execute("DELETE FROM ".$core->prefix."post WHERE user_id = '$user_id'");
			$core->con->execute("DELETE FROM ".$core->prefix."feed WHERE user_id ='$user_id'");
			$core->con->execute("DELETE FROM ".$core->prefix."site WHERE user_id ='$user_id'");
			$core->con->execute("DELETE FROM ".$core->prefix."permissions WHERE user_id = '$user_id'");
			$core->con->execute("DELETE FROM ".$core->prefix."setting WHERE user_id = '$user_id'");
			$core->con->execute("DELETE FROM ".$core->prefix."tribe WHERE user_id = '$user_id'");
			$core->con->execute("DELETE FROM ".$core->prefix."user WHERE user_id = '$user_id'");
			$shaarli_dir = dirname(__FILE__).'/../../data/shaarli/'.$user_id;
			if (is_dir($shaarli_dir))
				rrmdir($shaarli_dir);

			print '<div class="flash_notice">'.sprintf(T_("Delete of user %s succeeded"),$user->f('fullname')).'</div>';
		}
		break;

##########################################################
# FILTERED USER LIST RETURN
##########################################################
	case 'filter':
		$user_id = urldecode(trim($_POST['fuser_id']));
		$user_status = trim($_POST['user_status']);
		$sql_cond = array();
		if ($user_id != 'all') {
			$sql_cond[] = $core->prefix.'user.user_id=\''.$user_id.'\'';
		}
		if ($user_status != 'all') {
			$sql_cond[] = $core->prefix.'user.user_status='.$user_status;
		}
		if (!empty($sql_cond)) {
			$where_clause = "WHERE ";
			if (sizeof($sql_cond) < 1) {
				print "error";
				break;
			}
			elseif (sizeof($sql_cond) == 1) {
				$where_clause .= $sql_cond[0];
			} else {
				$where_clause .= $sql_cond[0].' AND '.$sql_cond[1];
			}
		}

		# On recupere les informtions sur les membres
		$sql = 'SELECT
			user_id,
			user_fullname,
			user_email,
			user_status
			FROM '.$core->prefix.'user
			'.$where_clause.'
			ORDER by lower(user_fullname)';

		print getOutput($sql, -1);
		break;

##########################################################
# USERS LIST RETURN
##########################################################
	case 'list':
		$num_page = !empty($_POST['num_page']) ? $_POST['num_page'] : 0;
		$nb_items = !empty($_POST['nb_items']) ? $_POST['nb_items'] : 30;
		$num_start = $num_page * $nb_items;

		# On recupere les informtions sur les membres
		$sql = 'SELECT
			user_id,
			user_fullname,
			user_email,
			user_status
			FROM '.$core->prefix.'user
			ORDER by lower(user_fullname)
			ASC LIMIT '.$nb_items.' OFFSET '.$num_start;

		print getOutput($sql, $num_page, $nb_items);
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
	$output = showPagination($rs->count(), $num_page, $nb_items, 'updateUserList');

	$output .= '
<br />
<table id="userlist" class="table-member">
<thead>
	<tr>
		<th class="tc7 tcr" scope="col">'.T_('Avatar').'</th>
		<th class="tc9 tcr" scope="col">'.T_('User Informations').'</th>
		<th class="tc8 tcr" scope="col">'.T_('Website').'</th>
		<th class="tc10 tcr" scope="col">'.T_('Action').'</th>
	</tr>
</thead>';
	# On affiche la liste de membres
	$author_id = $blog_settings->get('author_id');
	while($rs->fetch()) {
		$sql = "SELECT site_id, site_name, site_url FROM ".$core->prefix."site WHERE user_id = '$rs->user_id'";
		$sites = $core->con->select($sql);
		$god_class = '';
		$is_god = false;
		if ($rs->user_id == $author_id) {
			$is_god = true;
			$god_class = 'god';
		}
		if($rs->user_status) {
			$status = 'active';
			$toggle_status = 'disable';
			$toggle_msg = T_('Disable user');
		} else {
			$toggle_status = 'enable';
			$toggle_msg = T_('Enable user');
			$status = 'inactive';
		}
		$avatar_email = strtolower($rs->user_email);
		$avatar_url = "http://cdn.libravatar.org/avatar/".md5($avatar_email)."?d=".urlencode(BP_PLANET_URL."/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png")."&s=40";
		# Affichage de la ligne de tableau
		$output .= '<tr class="line '.$status.'"><td class="'.$god_class.'" style="text-align: center;"><img src="'.$avatar_url.'"></td>
			<td class="'.$god_class.'"><ul>
				<li>User id : '.$rs->user_id.'</li>
				<li>Fullname : '.html_entity_decode(stripslashes($rs->user_fullname), ENT_QUOTES, 'UTF-8').'</li>
				<li>Email : '.$rs->user_email.'</li>';
		if ($is_god) {
			$output .= '<li>'.T_('Planet author').'</li>';
		}
		$output .= '</ul></div></td>';
		$output .= '<td class="'.$god_class.'">';
		if ($sites->count()){
			$output .= '<ul>';
			while($sites->fetch()){
				$s_name = '';
				if ($sites->site_name != "") {
					$s_name = $sites->site_name.' : ';
				}
				$output .= '<li>'.$s_name.'<a href="'.$sites->site_url.'" target="_blank">'.$sites->site_url.'</a>&nbsp;&nbsp;';
				$output .= '<a href="#" class="edit-website" onclick="javascript:editSite(\''.$sites->site_id.'\', \''.$num_page.'\', \''.$nb_items.'\')"></a>';
				$output .= '<a href="#" class="del-website" onclick="javascript:removeSite(\''.$sites->site_id.'\', \''.$num_page.'\', \''.$nb_items.'\')"></a>';
				$output .= '</li>';
			}
			$output .= '</ul><br/>';
		}
		$output .= '<a class="add-website" href="#" onclick="javascript:addSite(\''.urlencode($rs->user_id).'\', \''.$num_page.'\', \''.$nb_items.'\')">'.T_("Add a new website").'</a></td>';
		$output .= '<td  class="'.$god_class.'" style="text-align: center;">';
		if (!$is_god) {
			$output .= '<a href="#" onclick="javascript:toggleUserStatus(\''.urlencode($rs->user_id).'\', \''.$num_page.'\', \''.$nb_items.'\')">
			<img src="meta/icons/action-'.$toggle_status.'.png" title="'.$toggle_msg.'" /></a>';
		}
		$output .= '<a href="#" onclick="javascript:profile(\''.urlencode($rs->user_id).'\', \''.$num_page.'\', \''.$nb_items.'\')">
				<img src="meta/icons/action-edit.png" title="'.T_('Update').'" /></a>';
		if (!$is_god) {
		$output .= '<a href="#" onclick="javascript:removeUser(\''.urlencode($rs->user_id).'\', \''.$num_page.'\', \''.$nb_items.'\')">
				<img src="meta/icons/action-remove.png" title="'.T_('Delete').'" /></a>';
		}
		$output .= '</td></tr>';
	}
	$output .= '</table>';
	$output .= showPagination($rs->count(), $num_page, $nb_items, 'updateUserList');

	return $output;
}
?>
