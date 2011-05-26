<?php

function render_page ($page) {
	global $core, $blog_settings;
	$user_id = $core->auth->userID();
	$user_settings = new bpSettings($core, $user_id);

	$tpl = new Hyla_Tpl(dirname(__FILE__).'/../tpl/');
	$tpl->importFile($page, $page.'.tpl');
	$tpl->setVar('planet', array(
		"url"	=>	$blog_settings->get('planet_url'),
		"theme"	=>	$blog_settings->get('planet_theme'),
		"title"	=>	$blog_settings->get('planet_title'),
		"desc"	=>	$blog_settings->get('planet_desc'),
		"keywords"	=>	$blog_settings->get('planet_keywords'),
		"desc_meta"	=>	$blog_settings->get('planet_desc_meta'),
		"msg_info" => $blog_settings->get('planet_msg_info'),
	));

	switch($page) {
	case 'dashboard':
		$sql = generate_SQL(
			0,
			10,
			array($user_id),
			array(),
			'',
			'',
			false,
			null,
			2);
		$rs = $core->con->select($sql);
		while ($rs->fetch()) {
			$status = "";
			if (!$rs->status) {
				$status = "disabled";
			}
			$post = array(
				'id' => $rs->post_id,
				'title' => html_entity_decode($rs->title, ENT_QUOTES, 'UTF-8'),
				'title2' => htmlspecialchars($rs->title),
				'permalink' => $rs->permalink,
				'pubdate' => $rs->pubdate,
				"date" => mysqldatetime_to_date("d/m/Y",$rs->pubdate),
				"status" => $status
				);
			$rs2 = $core->con->select("SELECT tag_id FROM ".$core->prefix."post_tag
				WHERE post_id = ".$rs->post_id);
			$tpl->setVar('post', $post);
			while ($rs2->fetch()) {
				$tpl->setVar('tag', $rs2->tag_id);
				$tpl->setVar('post_id', $rs->post_id);
				$tpl->render('userpost.tags');
			}
			if (!$rs->status) {
				$tpl->render('userpost.action');
			}
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
