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
		$user_id = $core->auth->userID();

		$tags = getArrayFromList($_POST['tags']);

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
			AND user_status = '1'
            AND post_status = '1' ".$filter."
			ORDER BY post_pubdate DESC
			LIMIT ".$count;

		$list = '<table id="post-list" class="table-member">';
		$list .= '<thead>';
		$list .= '<tr class="title">';
		$list .= '<th>'.T_('Date').'</td>';
		$list .= '<th>'.T_('Title').'</td>';
		$list .= '</tr></thead>';


		$rs = $core->con->select($sql);
		if ($rs->count() > 0){
			while($rs->fetch()){
                # Get tags from post
                $sql2 = "SELECT tag_id
                    FROM ".$core->prefix."post_tag
                    WHERE post_id = ".$rs->post_id.";";
                $rs2 = $core->con->select($sql2);
                $tags = "";
                while($rs2->fetch()) {
                    $tags .= '<span class="tag">'.$rs2->tag_id.'
                        <a href="javascript:rm_tag(\''.$rs->post_id.'\', \''.$rs2->tag_id.'\')">x</a></span>';
                }

				$post_title = decode_strip($rs->post_title, 100);
				$post_title = '<a href="'.$rs->post_permalink.'" target="_blank">'.$post_title.'</a>';

				$action = '<a href="javascript:add_tags('.$rs->post_id.', \''.htmlspecialchars($rs->post_title).'\')">';
                $action .= '<img src="meta/icons/add_tag.png" title="'.T_('Tag post').'" /></a>';

				$list .= '<tr id="line'.$rs->post_id.'" class="'.$status.'">';
				$list .= '<td>'.mysqldatetime_to_date("d/m/Y", $rs->post_pubdate).'</td>';
                $list .= '<td>'.$post_title.'<br /><div class="tag-line">'.$tags;
                $list .= ' <span id="action'.$rs->post_id.'">'.$action.'</span></div></td></tr>';
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
