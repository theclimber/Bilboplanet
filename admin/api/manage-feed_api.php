<?php
if(isset($_POST['action'])) {
	switch (trim($_POST['action'])){

##########################################################
# GET FEED INFO
##########################################################
	case 'get':
		$feed_id = trim($_POST['feed_id']);
		$rs = $core->con->select("SELECT
				".$core->prefix."feed.user_id as user_id,
				feed_id,
				feed_name,
				feed_url,
				feed_status,
				feed_trust,
				".$core->prefix."feed.site_id as site_id,
				".$core->prefix."site.site_name as site_name,
				".$core->prefix."site.site_url as site_url
			FROM ".$core->prefix."feed, ".$core->prefix."site
			WHERE
				".$core->prefix."feed.site_id = ".$core->prefix."site.site_id
				AND feed_id = '$feed_id'");
		$user = array(
			"user_id" => $rs->f('user_id'),
			"feed_id" => $rs->f('feed_id'),
			'feed_name' => $rs->f('feed_name'),
			'feed_url' => $rs->f('feed_url'),
			'feed_status' => $rs->f('feed_status'),
			'feed_trust' => $rs->f('feed_trust'),
			'site_id' => $rs->f('site_id'),
			'site_url' => $rs->f('site_url'),
			'site_name' => $rs->f('site_name')
			);
		print json_encode($user);
		break;

##########################################################
# ADD FEED TO SITE
##########################################################
	case 'add':
		$user_id = trim($_POST['user_id']);
		$site_id = trim($_POST['site_id']);
		$feed_url = check_field('feed_url',trim($_POST['feed_url']), 'url');
		$feed_name = check_field('feed_name',trim($_POST['feed_name']));
		$feed_trust = trim($_POST['feed_trust']);
		$error = array();

		if ($feed_url['success']
			&& $feed_name['success'])
		{
			$rs = $core->con->select("SELECT
					".$core->prefix."feed.user_id as user_id,
					".$core->prefix."site.site_url as site_url
				FROM ".$core->prefix."feed, ".$core->prefix."site
				WHERE ".$core->prefix."feed.site_id = ".$core->prefix."site.site_id
					AND feed_url = '".$site_url['value']."'");
			if ($rs->count() > 0){
				if ($rs->f('site_id') == $site_id) {
					$error[] = sprintf(T_('The site %s already owns the feed %s'),$rs->f('site_url'), $feed_url['value']);
				}
				else {
					$error[] = sprintf(T_('The feed %s is owned by site %s'), $feed_url['value'], $rs->f('site_url'));
				}
			}

			if (empty($error)) {
				# Get next ID
				$rs3 = $core->con->select(
					'SELECT MAX(feed_id) '.
					'FROM '.$core->prefix.'feed'
					);
				$next_feed_id = (integer) $rs3->f(0) + 1;
				$cur = $core->con->openCursor($core->prefix.'feed');
				$cur->feed_id = $next_feed_id;
				$cur->user_id = $user_id;
				$cur->site_id = $site_id;
				$cur->feed_name = $feed_name['value'];
				$cur->feed_url = $feed_url['value'];
				$cur->feed_trust = $feed_trust;
				$cur->created = array(' NOW() ');
				$cur->modified = array(' NOW() ');
				$cur->insert();

				$output = sprintf(T_("Feed %s was successfully added"), $feed_url['value']);
			}
		}
		else {
			if (!$feed_url['success']) {
				$error[] = $feed_url['error'];
			}
			if (!$feed_name['success']) {
				$error[] = $feed_name['error'];
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
# EDIT FEED
##########################################################
	case 'edit':
		$feed_id = trim($_POST['ef_id']);
		$feed = $core->con->select("SELECT * FROM ".$core->prefix."feed WHERE feed_id = '$feed_id'");

		$new_name = !empty($_POST['ef_name']) ? $_POST['ef_name'] : $feed->f('feed_name');
		$new_url = !empty($_POST['ef_url']) ? $_POST['ef_url'] : $feed->f('feed_url');

		$new_name = check_field('Feed name',$new_name);
		$new_url = check_field('Feed url',$new_url,'feed');
		
		$error = array();

		if ($new_name['success'] 
			&& $new_url['success'])
		{
			#FIXME : check if this line is needed (also used in user_api)
			$new_name['value'] = htmlentities($new_name['value'],ENT_QUOTES,mb_detect_encoding($new_name['value']));

			$rs1 = $core->con->select("SELECT feed_url, user_id FROM ".$core->prefix."feed
				WHERE feed_id != '".$feed_id."'
				AND feed_url = '".$new_url['value']."'");
			if ($rs1->count() > 0){
				$error[] = sprintf(T_('The feed %s is already used by user %s'),$new_url['value'], $rs->f('user_id'));
			}

			if (empty($error)) {
				$cur = $core->con->openCursor($core->prefix.'feed');
				$cur->feed_name = $new_name['value'];
				$cur->feed_url = $new_url['value'];
				$cur->modified = array(' NOW() ');
				$cur->update("WHERE feed_id = '$feed_id'");

				$output = sprintf(T_("Feed %s successfully updated"),$new_url['value']);
			}
		} else {
			if (!$new_name['success']) {
				$error[] = $new_name['error'];
			}
			if (!$new_url['success']) {
				$error[] = $new_url['error'];
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
# TOGGLE FEED
##########################################################
	case 'toggle':
		$feed_id = trim($_POST['feed_id']);
		$feed = $core->con->select("SELECT feed_status FROM ".$core->prefix."feed WHERE feed_id = '$feed_id'");
		
		$cur = $core->con->openCursor($core->prefix.'feed');
		if($feed->f('feed_status') == 1) {
			$cur->feed_status = 0;
		} else {
			$cur->feed_status = 1;
		}
		$cur->update("WHERE feed_id = '$feed_id'");

		print '<div class="flash notice">'.T_('Feed status toggled').'</div>';
		break;

##########################################################
# CHANGE FEED TRUST
##########################################################
	case 'change-trust':
		$feed_id = trim($_POST['feed_id']);
		$feed = $core->con->select("SELECT feed_trust FROM ".$core->prefix."feed WHERE feed_id = '$feed_id'");
		
		$cur = $core->con->openCursor($core->prefix.'feed');
		if($feed->f('feed_trust') == 1) {
			$cur->feed_trust = 0;
		} else {
			$cur->feed_trust = 1;
		}
		$cur->update("WHERE feed_id = '$feed_id'");

		print '<div class="flash notice">'.T_('Feed trust changed').'</div>';
		break;

##########################################################
# REMOVE FEED
##########################################################
	case 'remove':
		$feed_id = trim($_POST['feed_id']);
		$rs = $core->con->select("SELECT feed_url FROM ".$core->prefix."feed WHERE feed_id = '$feed_id'");
		$confirmation = "<p>".sprintf(T_('Are you sure you want to remove feed %s ?'),
			'<a href="'.$rs->f('feed_url').'" target="_blank">'.$rs->f('feed_url').'</a>')."?<br/>";
		$confirmation .= "<ul><li>".T_('This action can not be canceled')."</li>";
		$confirmation .= "<li>".T_('All the posts comming from this feed will be removed')."</li>";
		$confirmation .= "</ul><br/>";
		$confirmation .= "<form id='removeFeedConfirm_form'><input type='hidden' name='feed_id' value='".$feed_id."'/>";
		$confirmation .= "<div class='button br3px'><input class='notvalide' type='button' onclick=\"javascript:$('#flash-msg').html('')\" value='".T_('Reset')."'/></div>&nbsp;&nbsp;";
		$confirmation .= "<div class='button br3px'><input class='valide' type='submit' name='confirm' value='".T_('Confirm')."'/></div></form></p>";
		print '<div class="flash error">'.$confirmation.'</div>';
		break;

##########################################################
# CONFIRM REMOVE FEED
##########################################################
	case 'removeConfirm':
		$feed_id = trim($_POST['feed_id']);
		$rs2 = $core->con->select("SELECT * FROM ".$core->prefix."feed WHERE feed_id = '$feed_id'");
		$core->con->execute("DELETE FROM ".$core->prefix."post WHERE feed_id ='$feed_id'");
		$core->con->execute("DELETE FROM ".$core->prefix."feed WHERE feed_id ='$feed_id'");

		print '<div class="flash notice">'.sprintf(T_("Delete of feed %s succeeded"),$rs2->f('feed_url')).'</div>';
		break;

##########################################################
# GET FILTERED FEED LIST
##########################################################
	case 'filter':
		$user_id = trim($_POST['fuser_id']);
		$feed_status = trim($_POST['feed_status']);
		$sql_cond = '';
		if ($user_id != 'all') {
			$sql_cond .= ' AND '.$core->prefix.'feed.user_id=\''.$user_id.'\'';
		}
		if ($feed_status != 'all') {
			$sql_cond .= ' AND '.$core->prefix.'feed.feed_status='.$feed_status;
		}
		# On recupere les informtions sur les membres
		$sql = 'SELECT
			feed_id,
			'.$core->prefix.'feed.user_id as user_id,
			site_url,
			site_name,
			feed_name,
			feed_url,
			feed_status,
			feed_trust
			FROM '.$core->prefix.'feed, '.$core->prefix.'site
			WHERE '.$core->prefix.'feed.site_id = '.$core->prefix.'site.site_id'.
			$sql_cond.' 
			ORDER by '.$core->prefix.'feed.user_id ASC';

		print getOutput($sql);
		break;

##########################################################
# GET FEED LIST
##########################################################
	case 'list':
		$num_page = !empty($_POST['num_page']) ? $_POST['num_page'] : 0;
		$nb_items = !empty($_POST['nb_items']) ? $_POST['nb_items'] : 30;
		$num_start = $num_page * $nb_items;

		# On recupere les informtions sur les membres
		$sql = 'SELECT
			feed_id,
			'.$core->prefix.'feed.user_id as user_id,
			site_url,
			site_name,
			feed_name,
			feed_url,
			feed_status,
			feed_trust
			FROM '.$core->prefix.'feed, '.$core->prefix.'site
			WHERE '.$core->prefix.'feed.site_id = '.$core->prefix.'site.site_id
			ORDER by '.$core->prefix.'feed.user_id
			ASC LIMIT '.$num_start.','.$nb_items;

		print getOutput($sql, $num_page, $nb_items);
		break;

	default:
		print '<div class="flash error">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}

function getOutput($sql, $num_page=0, $nb_items=30) {
	global $blog_settings, $core;
	$next_page = $num_page + 1;
	$prev_page = $num_page - 1;

	$rs = $core->con->select($sql);
	$output .= showPagination($rs->count(), $num_page, $nb_items, 'updateFeedList');

	$output .= '
<br />
<table id="feedlist" class="table-member">
<thead>
	<tr>
		<th class="tc7 tcr" scope="col">'.T_('User').'</th>
		<th class="tc9 tcr" scope="col">'.T_('Website(s) user Informations').'</th>
		<th class="tc8 tcr" scope="col">'.T_('Feed').'</th>
		<th class="tc10 tcr" scope="col">'.T_('Action').'</th>
	</tr>
</thead>'
;
	# On affiche la liste de membres
	while($rs->fetch()) {

		if($rs->feed_status == 1) {
			$status = 'active';
			$toggle_status = 'disable';
			$toggle_msg = T_('Disable feed');
		} elseif ($rs->feed_status == 2) {
			$status = 'auto-disabled';
			$toggle_status = 'enable';
			$toggle_msg = T_('Enable feed');
		} else {
			$status = 'inactive';
			$toggle_status = 'enable';
			$toggle_msg = T_('Enable feed');
		}
		
		if($rs->feed_trust) {
			$toggle_trust = 'untrust';
			$trust_msg = T_('Untrust this feed');
		} else {
			$toggle_trust = 'trust';
			$trust_msg = T_('Trust this feed');
		}

		$user = $core->con->select("SELECT user_email FROM ".$core->prefix."user WHERE user_id = '".$rs->user_id."'");
		$gravatar_email = strtolower($user->f('user_email'));
		$gravatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($gravatar_email)."&default=".urlencode($blog_settings->get('planet_url')."/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png")."&size=40";

		# Affichage de la ligne de tableau
		$output .= '<tr class="line '.$status.'"><td><img src="'.$gravatar_url.'" /><br />'.$rs->user_id.'</td>
			<td><ul>
				<li>'.T_('Feed name : ').$rs->feed_name.'</li>
				<li>'.T_('Site URL : ').'<a href="'.$rs->site_url.'" target="_blank">'.$rs->site_url.'</a></li>
			</ul></div></td>';
		$output .= '<td>';
		$output .=  '<a href="'.$rs->feed_url.'" target="_blank">'.$rs->feed_url.'</a></td>';
		$output .= '<td style="text-align: center;">';

		if ($blog_settings->get('planet_moderation')) {
			$output .= '<a href="#" onclick="javascript:toggleFeedTrust(\''.$rs->feed_id.'\', \''.$num_page.'\', \''.$nb_items.'\')">
				<img src="meta/icons/action-'.$toggle_trust.'.png" title="'.$trust_msg.'" /></a>';
		}
		$output .= '<a href="#" onclick="javascript:toggleFeedStatus(\''.$rs->feed_id.'\', \''.$num_page.'\', \''.$nb_items.'\')">
				<img src="meta/icons/action-'.$toggle_status.'.png" title="'.$toggle_msg.'" /></a>
			<a href="#" onclick="javascript:edit(\''.$rs->feed_id.'\', \''.$num_page.'\', \''.$nb_items.'\')">
				<img src="meta/icons/action-edit.png" title="'.T_('Update').'" /></a>
			<a href="#" onclick="javascript:removeFeed(\''.$rs->feed_id.'\', \''.$num_page.'\', \''.$nb_items.'\')">
				<img src="meta/icons/action-remove.png" title="'.T_('Delete').'" /></a>
			</td></tr>';
	}
	$output .= '</table>';
	$output .= showPagination($rs->count(), $num_page, $nb_items, 'updateFeedList');

	return $output;
}
?>
