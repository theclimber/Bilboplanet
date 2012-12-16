<?php

function render_page ($page) {
	global $core, $blog_settings, $user_settings;
	$user_id = $core->auth->userID();
	if($user_settings == null) {
		$user_settings = new bpSettings($core, $user_id);
	}

	$planet_theme = $blog_settings->get('planet_theme');
	$tpl = new Hyla_Tpl(dirname(__FILE__).'/../../themes/'.$planet_theme.'/user');
	$tpl->setL10nCallback('T_');
	$tpl->importFile($page, $page.'.tpl');
	$tpl->setVar('planet', array(
		"url"	=>	BP_PLANET_URL,
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

		foreach (getAllSupportedLanguages() as $lang) {
			$tpl->setVar('lang', array(
				"code" => $lang['code'],
				"name" => $lang['name'],
				"selected" => $lang['code']==$rs->f('user_lang') ? 'selected="selected"' : ""));
			$tpl->render("lang.select");
		}

		$rs_feed = $core->con->select("SELECT * FROM ".$core->prefix."feed
			WHERE user_id ='".$user_id."'");
		while ($rs_feed->fetch()) {
			$status = "";
			if (!$rs_feed->feed_status || $rs_feed->feed_status == 2) {
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
		$option = $user_settings->get('social.newsletter');
		if (!isset($option))
			$option = 'nomail';
		$newsletter_options[$option]['selected'] = "selected";

		foreach ($newsletter_options as $news) {
			$tpl->setVar('news', $news);
			$tpl->render('newsletter.option');
		}
		$checked = array(
			"twitter" => $user_settings->get('social.twitter') ? 'checked' : '',
			"statusnet" => $user_settings->get('social.statusnet') ? 'checked' : '',
			"shaarli" => $user_settings->get('social.shaarli') ? 'checked' : '',
			"shaarli-type.remote" =>
				$user_settings->get('social.shaarli.type')=='remote' ? 'selected="selected"' : '',
			"shaarli-type.local" =>
				$user_settings->get('social.shaarli.type')=='local' ? 'selected="selected"' : '',
			"google" => $user_settings->get('social.google') ? 'checked' : '',
			"reddit" => $user_settings->get('social.reddit') ? 'checked' : ''
			);
		$tpl->setVar('checked', $checked);
		$tpl->setVar('statusnet_account', $user_settings->get('social.statusnet.account'));
		$tpl->setVar('shaarli_instance', $user_settings->get('social.shaarli.instance'));
		break;
	case 'tribes':

		$rs_users = $core->con->select('SELECT user_id, user_fullname
			FROM '.$core->prefix.'user
			WHERE user_status=1');
		while($rs_users->fetch()){
			$tpl->setVar('option', array(
				"user_id" => $rs_users->user_id,
				"user_name" => $rs_users->user_fullname
				));
			$tpl->render('tribe.option.userlist');
		}

		# On recupere les informtions sur les membres
		$sql = 'SELECT
			user_id,
			tribe_id,
			tribe_name,
			tribe_tags,
			tribe_notags,
			tribe_users,
			tribe_nousers,
			tribe_search,
			tribe_icon,
			visibility,
			ordering
			FROM '.$core->prefix.'tribe
			WHERE user_id=\''.$user_id.'\'
			ORDER by ordering
			ASC LIMIT 100 OFFSET 0';

		$rs = $core->con->select($sql);
		if ($rs->count() > 0) {
			while($rs->fetch()) {

				$sql_post = generate_tribe_SQL(
					$rs->tribe_id,
					0,
					0);
				$rs_post = $core->con->select($sql_post);

				$tribe_state = "private";
				if ($rs->visibility == 1) {
					$tribe_state = "public";
				}

				$tribe_tags = preg_split('/,/',$rs->tribe_tags, -1, PREG_SPLIT_NO_EMPTY);
				foreach ($tribe_tags as $tag_item) {
					$tpl->setVar('tribe_tag', $tag_item);
					$tpl->setVar('tribe_id', $rs->tribe_id);
					$tpl->render('tribes.tag');
				}

				$tribe_notags = preg_split('/,/',$rs->tribe_notags, -1, PREG_SPLIT_NO_EMPTY);
				foreach ($tribe_notags as $tag_item) {
					$tpl->setVar('tribe_notag', $tag_item);
					$tpl->setVar('tribe_id', $rs->tribe_id);
					$tpl->render('tribes.notag');
				}

				$tribe_users = preg_split('/,/',$rs->tribe_users, -1, PREG_SPLIT_NO_EMPTY);
				foreach ($tribe_users as $user_item) {
					$tpl->setVar('tribe_user', $user_item);
					$tpl->setVar('tribe_id', $rs->tribe_id);
					$tpl->render('tribes.user');
				}

				if ($rs->tribe_search) {
					$tpl->setVar('tribe_id', $rs->tribe_id);
					$tpl->render('tribes.search');
				}

                $tribe_icon = getTribeIcon($rs->tribe_id,$rs->tribe_name,$rs->tribe_icon);
                $tpl->setVar('tribe_id', $rs->tribe_id);
                $tpl->render('tribes.icon.action');

				$tribe_name = html_entity_decode($rs->tribe_name, ENT_QUOTES, 'UTF-8');

				$tpl->setVar('tribe', array(
					'id' => $rs->tribe_id,
					'name' => $rs->tribe_name,
					'stripped_name' => addslashes($rs->tribe_name),
					'state' => $tribe_state,
					'icon' => $tribe_icon,
					'last_post' => mysqldatetime_to_date("d/m/Y",$rs_post->last),
					'count' => $rs_post->count,
					'ordering' => $rs->ordering,
					'search' => $rs->tribe_search
					));
				$tpl->render('tribes.box');
			}
		}
		break;
	default:
		break;
	}
	return $tpl->render();
}
?>
