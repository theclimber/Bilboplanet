<?php

function render_page ($page) {
	global $core, $blog_settings, $user_settings;
	$user_id = $core->auth->userID();
	if($user_settings == null) {
		$user_settings = new bpSettings($core, $user_id);
	}

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
			if ($rs->comment) {
				$tpl->render('userpost.action.nocomment');
			} else {
				$tpl->render('userpost.action.comment');
			}
			if ($blog_settings->get('allow_post_modification')) {
				$tpl->render('userpost.action.activate');
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

		$rs_feed = $core->con->select("SELECT * FROM ".$core->prefix."feed
			WHERE user_id ='".$user_id."'");
		while ($rs_feed->fetch()) {
			$status = "";
			if (!$rs_feed->feed_status) {
				$status = "disabled";
			}
			$feed = array(
				"status" => $status,
				"id" => $rs_feed->feed_id,
				"url" => $rs_feed->feed_url,
				);
			$tpl->setVar('feed', $feed);
			if (!$rs_feed->feed_comment) {
				$tpl->render('userfeed.action');
			}
			$rs_tags = $core->con->select("SELECT tag_id FROM ".$core->prefix."feed_tag
				WHERE feed_id=".$rs_feed->feed_id);
			while($rs_tags->fetch()) {
				$tpl->setVar('tag', $rs_tags->tag_id);
				$tpl->setVar('feed_id', $rs_feed->feed_id);
				$tpl->render('userfeed.tags');
			}
			if ($blog_settings->get('allow_feed_modification')) {
				$tpl->render('userfeed.action.activate');
			}
			$tpl->render('userfeed.item');
		}

		$rs_pfeed = $core->con->select("SELECT * FROM ".$core->prefix."pending_feed
			WHERE user_id ='".$user_id."'");
		if ($rs_pfeed->count() > 0) {
			while ($rs_pfeed->fetch()) {
				$feed = array(
					"site" => $rs_pfeed->site_url,
					"url" => $rs_pfeed->feed_url
					);
				$tpl->setVar('pfeed', $feed);
				$tpl->render('userpfeed.item');
			}
			$tpl->render('pendingfeed');
		}

		$rs_esite = $core->con->select("SELECT * FROM ".$core->prefix."site WHERE user_id='".$user_id."'");
		while ($rs_esite->fetch()) {
			$tpl->setVar("esite", array(
				"id" => $rs_esite->site_id,
				"url" => $rs_esite->site_url));
			$tpl->render("existing.site");
		}
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
			"comments" => $user_settings->get('comments'),
			"twitter" => $user_settings->get('social.twitter'),
			"statusnet" => $user_settings->get('social.statusnet'),
			"google" => $user_settings->get('social.google')
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
