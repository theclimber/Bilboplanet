<?php

function render_page ($page) {
	global $core;
	$user_id = $core->auth->userID();
	$user_settings = new bpSettings($core, $user_id);

	$tpl = new Hyla_Tpl(dirname(__FILE__).'/../tpl/');
	$tpl->importFile($page, $page.'.tpl');

	switch($page) {
	case 'dashboard':
		$sql = generate_SQL(
			0,
			10,
			array($user_id));
		//$sql = "SELECT * FROM ".$core->prefix."post
		//	WHERE user_id = '".$user_id."' LIMIT 0, 10";
		//	return $sql;
		$rs = $core->con->select($sql);
		while ($rs->fetch()) {
			$post = array(
				'id' => $rs->post_id,
				'title' => html_entity_decode($rs->title, ENT_QUOTES, 'UTF-8'),
				'permalink' => $rs->permalink,
				'pubdate' => $rs->pubdate,
				"date" => mysqldatetime_to_date("d/m/Y",$rs->pubdate)
				);
			$rs2 = $core->con->select("SELECT tag_id FROM ".$core->prefix."post_tag
				WHERE post_id = ".$rs->post_id);
			while ($rs2->fetch()) {
				$tpl->setVar('tag', $rs2->tag_id);
				$tpl->render('userpost.tags');
			}
			$tpl->setVar('post', $post);
			$tpl->render('userpost.item');
		}

		break;
	case 'profile':
		$rs = $core->con->select("SELECT * FROM ".$core->prefix."user
			WHERE user_id = '".$user_id."'");
		$user = array(
			"user_id" => $user_id,
			'user_fullname' => $rs->f('user_fullname'),
			'user_email' => $rs->f('user_email')
			);
		$tpl->setVar('user', $user);
		break;
	case 'social':
		$newsletter_options = array(
			"nomail" => array(
				"selected" => "",
				"value" => "nomail",
				"text" => T_('Disable newsletter')),
			"dayly" => array(
				"selected" => "",
				"value" => "dayly",
				"text" => T_('Every day')),
			"weekly" => array(
				"selected" => "",
				"value" => "weekly",
				"text" => T_('Every week')),
			"monthly" => array(
				"selected" => "",
				"value" => "monthly",
				"text" => T_('Every month'))
			);
		$newsletter_options[$user_settings->get('newsletter')]['selected'] = "selected";
		foreach ($newsletter_options as $news) {
			$tpl->setVar('news', $news);
			$tpl->render('newsletter.option');
		}
		$checked = array(
			"twitter" => $user_settings->get('twitter_share'),
			"facebook" => $user_settings->get('facebook_share'),
			"statusnet" => $user_settings->get('statusnet_share')
			);
		$tpl->setVar('checked', $checked);
		$tpl->setVar('statusnet_account', $user_settings->get('statusnet_account'));
		break;
	default:
		break;
	}
	return $tpl->render();
}
?>
