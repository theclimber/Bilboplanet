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
# Accept article
##########################################################
	case 'accept':
		$post_id = $_POST['post_id'];
		$sql = "SELECT post_status, post_id
			FROM ".$core->prefix."post
			WHERE post_id = ".$post_id;
		$rs = $core->con->select($sql);

		if ($rs->count() > 0){
			if ($rs->f('post_status') == 1) {
				$error[] = T_('The post is already accepted');
			}
			else {
				$cur = $core->con->openCursor($core->prefix."post");
				$cur->post_status = 1;
				$cur->update("WHERE post_id = $post_id");
				$output = T_("Post successfully updated");
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
# Refuse article
##########################################################
	case 'refuse':
		$post_id = $_POST['post_id'];
		$from = $_POST['from'];
		$subject = html_entity_decode(stripslashes($_POST['subject']), ENT_QUOTES, 'UTF-8');
		$content = html_entity_decode(stripslashes($_POST['content']), ENT_QUOTES, 'UTF-8');

		$sql = "SELECT post_status, post_id, user_fullname, user_email
			FROM ".$core->prefix."post, ".$core->prefix."user
			WHERE post_id = ".$post_id."
			AND ".$core->prefix."post.user_id = ".$core->prefix."user.user_id";
		$rs = $core->con->select($sql);

		$to = $rs->user_email.', '.$blog_settings->get('author_mail');
		$reply_to = $blog_settings->get('author_mail');

		if ($rs->count() > 0){
			if ($rs->f('post_status') == 0) {
				$error[] = T_('The post is already refused');
			}
			else {
				if (!sendmail($from, $to, $subject, $content, 'normal', $reply_to)){
					$error[] = T_("Mail could not be send");
				} else {
					$cur = $core->con->openCursor($core->prefix."post");
					$cur->post_status = 0;
					$cur->update("WHERE post_id = $post_id");
					$output = T_("Post successfully refused");
				}
			}
		}

		sleep (2);

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
# Get user email text
##########################################################
	case 'emailtext':
		$user_id = urldecode($_POST['user_id']);
		$post_permalink = $_POST['permalink'];

		if (!empty($user_id)) {
			$text_opening = sprintf(T_("Dear %s,"), $user_id);
		} else {
			$text_opening = T_("Dear user,");
		}

		$text_content = sprintf(T_("Our team has carefully read your latest post :".
				"\n%s\n\n".
				"However, this article raised questions among us and we hesitate to keep it ".
				"in the feed of our website. Indeed :"), $post_permalink);
		$text_content .= "\n- \n- \n- \n";
		$text_content .= T_("We decided to remove your post from our main feed. ".
				"We are of course disposed to answer to any question and to discuss with you about this.");

		$text_closing = sprintf(T_("Thank you for your contribution on the %s"),
				html_entity_decode(stripslashes($blog_settings->get('planet_title')), ENT_QUOTES, 'UTF-8'));
		$text_closing .= "\n".T_("Sincerely yours");

		print $text_opening."\n\n".$text_content."\n\n".$text_closing;
		break;

##########################################################
# Filter users
##########################################################
	case 'filter':
		$count = !empty($_POST['nb_items']) ? $_POST['nb_items'] : 20;
		$filter = "";
		if (!empty($_POST['user_id'])) {
			$filter = ' AND '.$core->prefix.'post.user_id = \''.urldecode($_POST['user_id']).'\' ';
		}
		$sql = "SELECT user_fullname, post_pubdate, post_title, post_permalink, post_status, post_id
			FROM ".$core->prefix."post, ".$core->prefix."user
			WHERE ".$core->prefix."post.user_id = ".$core->prefix."user.user_id
			AND user_status = '1' ".$filter."
			ORDER BY post_pubdate DESC
			LIMIT ".$count;

		$list = '<table id="post-list" class="table-member">';
		$list .= '<thead>';
		$list .= '<tr class="title">';
		$list .= '<th>'.T_('Date').'</td>';
		$list .= '<th style="text-align:center;">'.T_('Author').'</td>';
		$list .= '<th>'.T_('Title').'</td>';
		$list .= '<th style="text-align:center;">'.T_('Action').'</td></tr></thead>';


		$rs = $core->con->select($sql);
		if ($rs->count() > 0){
			while($rs->fetch()){
				$post_title = decode_strip($rs->post_title, 100);
				$post_title = '<a href="'.$rs->post_permalink.'" target="_blank">'.$post_title.'</a>';

				if($rs->post_status) {
					$status = 'active';
					$action = '<img src="meta/icons/true-light.png" title="'.T_('Accept').'" /> ';
					$action .= '<a href="javascript:refuse('.$rs->post_id.', \''.$rs->user_fullname.'\', \''.$rs->post_permalink.'\')">';
					$action .= '<img src="meta/icons/warn.png" title="'.T_('Refuse').'" /></a>';
				} else {
					$status = 'inactive';
					$action = '<a href="javascript:accept('.$rs->post_id.', \''.$rs->user_fullname.'\')">';
					$action .= '<img src="meta/icons/true.png" title="'.T_('Accept').'" /></a> ';
					$action .= '<img src="meta/icons/warn-light.png" title="'.T_('Refuse').'" />';
				}

				$list .= '<tr id="line'.$rs->post_id.'" class="'.$status.'">';
				$list .= '<td>'.mysqldatetime_to_date("d/m/Y", $rs->post_pubdate).'</td>';
				$list .= '<td style="text-align:center;"><b>'.$rs->user_fullname.'</b></td>';
				$list .= '<td>'.$post_title.'</td>';
				$list .= '<td style="text-align:center;"><span id="action'.$rs->post_id.'">'.$action.'</span></td></tr>';
			}
			$list .= "</table>";
			$content = '<h3>'.sprintf(T_('%s last posts :'), $count).'</h3><br/>';
			$content .= $list;

			echo $content;
		}
		else {
			echo '<p>'.T_('No posts found').'</p>';
		}
		break;

	default:
		print '<div class="flash_warning">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}

?>
