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
# Remove tag on post
##########################################################
	case 'rm_tag':
		$post_id = $_POST['post_id'];
        $tag = $_POST['tag'];
		if (!$blog_settings->get('allow_post_modification')) {
			print 'forbidden';
			exit;
		}
        $error = array();

		$sql = "SELECT tag_id
			FROM ".$core->prefix."post_tag
            WHERE post_id = ".$post_id."
            AND tag_id = '".$tag."';";
		$rs = $core->con->select($sql);
        if ($rs->count() == 0) {
            $error[] = T_("The tag you want to remove doesn't exist on this post");
        } else {
		    $core->con->execute(
                "DELETE FROM ".$core->prefix."post_tag WHERE post_id = '$post_id'
                    AND tag_id = '".$tag."'");
            $output = T_("The tag was successfuly removed");
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
# Add tags to post
##########################################################
	case 'add_tags':
		$post_id = $_POST['post_id'];
		$user_id = '';
		if ($core->auth->sessionExists()){
			$user_id = $core->auth->userID();
		}
		if (!$blog_settings->get('allow_post_modification')) {
			print 'forbidden';
			exit;
		}
		$patterns = array( '/, /', '/ ,/');
		$replacement = array(',', ',');
		$tags = urldecode($_POST['tags']);
		$tags = preg_replace($patterns, $replacement, $tags);
		$tags = preg_split('/,/',$tags, -1, PREG_SPLIT_NO_EMPTY);

		$sql = "SELECT tag_id
			FROM ".$core->prefix."post_tag
            WHERE post_id = ".$post_id.";";
		$rs = $core->con->select($sql);

		while($rs->fetch()){
			if (in_array($rs->tag_id, $tags)) {
				$key = array_keys($tags, $rs->tag_id);
				unset($tags[$key]);
			}
		}

		$output .= T_("Tags added : ");
		foreach($tags as $tag) {
			$cur = $core->con->openCursor($core->prefix.'post_tag');
			$cur->post_id = $post_id;
			$cur->tag_id = $tag;
			$cur->user_id = $user_id;
			$cur->created = array(' NOW() ');
			$cur->insert();
			$output .= $tag.",";
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
# Remove tag on feed
##########################################################
	case 'rm_feed_tag':
		$feed_id = $_POST['feed_id'];
        $tag = $_POST['tag'];
        $error = array();
		if (!$blog_settings->get('allow_feed_modification')) {
			print 'forbidden';
			exit;
		}

		$sql = "SELECT tag_id
			FROM ".$core->prefix."feed_tag
            WHERE feed_id = ".$feed_id."
            AND tag_id = '".$tag."';";
		$rs = $core->con->select($sql);
        if ($rs->count() == 0) {
            $error[] = T_("The tag you want to remove doesn't exist on this feed");
        } else {
		    $core->con->execute(
                "DELETE FROM ".$core->prefix."feed_tag WHERE feed_id = '$feed_id'
                    AND tag_id = '".$tag."'");
            $output = T_("The tag was successfuly removed");
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
# Add tags to feed
##########################################################
	case 'add_feed_tags':
		$feed_id = $_POST['feed_id'];
		if (!$blog_settings->get('allow_feed_modification')) {
			print 'forbidden';
			exit;
		}
        $patterns = array( '/, /', '/ ,/');
        $replacement = array(',', ',');
        $tags = urldecode($_POST['tags']);
        $tags = preg_replace($patterns, $replacement, $tags);
        $tags = preg_split('/,/',$tags, -1, PREG_SPLIT_NO_EMPTY);

		$sql = "SELECT tag_id
			FROM ".$core->prefix."feed_tag
            WHERE feed_id = ".$feed_id.";";
		$rs = $core->con->select($sql);

		while($rs->fetch()){
            if (in_array($rs->tag_id, $tags)) {
                $key = array_keys($tags, $rs->tag_id);
                unset($tags[$key]);
            }
        }

        $output .= T_("Tags added : ");
        foreach($tags as $tag) {
            $cur = $core->con->openCursor($core->prefix.'feed_tag');
            $cur->feed_id = $feed_id;
            $cur->tag_id = $tag;
            $cur->insert();
            $output .= $tag.",";
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


	default:
		print '<div class="flash_warning">'.T_('User bad call').'</div>';
		break;
	}
} else {
	print 'forbidden';
}

?>
