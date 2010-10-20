<?php
if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# UPDATE ACCOUNT
##########################################################
	case 'update':
		$user_id = $core->auth->userID();
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
				WHERE user_id != '".$user_id."'
				AND (user_fullname = '".$new_fullname['value']."'
				OR user_email = '".$new_email['value']."')";
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
			print '<div class="flash error">'.$output.'</div>';
		}
		else {
			print '<div class="flash notice">'.$output.'</div>';
		}
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
