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
# GET TRIBE INFO
##########################################################
	case 'get':
		$tribe_id = trim($_POST['tribe_id']);
		$rs = $core->con->select("SELECT
				".$core->prefix."tribe.user_id as user_id,
				tribe_id,
				tribe_name,
				tribe_tags,
				tribe_users,
				tribe_search,
				tribe_icon,
				ordering,
				visibility
			FROM ".$core->prefix."tribe
			WHERE
				tribe_id = '$tribe_id'");
		$tribe = array(
			"user_id" => $rs->f('user_id'),
			"tribe_id" => $rs->f('tribe_id'),
			'tribe_name' => $rs->f('tribe_name'),
			'tribe_tags' => $rs->f('tribe_tags'),
			'tribe_users' => $rs->f('tribe_users'),
			'tribe_search' => $rs->f('tribe_search'),
			'ordering' => $rs->f('ordering'),
			'visibility' => $rs->f('visibility'),
			'tribe_icon' => $rs->f('tribe_icon'),
			);
		print json_encode($tribe);
		break;

##########################################################
# CREATE TRIBE
##########################################################
	case 'add':

		if ($core->auth->sessionExists() && $core->hasPermission('administration')){
			$user_id = urldecode(trim($_POST['user_id']));
		} else {
			$user_id = $core->auth->userID();
		}
		$tribe_name = check_field('tribe_name',trim($_POST['tribe_name']));
		$ordering = intval(trim($_POST['ordering']));
		$error = array();

		if ($tribe_name['success']
			&& !empty($user_id))
		{
			$patterns = array('/ /');
			$replacement = array('_');
			$tribe_id = stripAccents($tribe_name['value']);
			$tribe_id = urldecode(strtolower($tribe_id));
			$tribe_id = $user_id.'-'.preg_replace($patterns, $replacement, $tribe_id);

			# Get next ID
			$rs3 = $core->con->select(
				'SELECT tribe_id '.
				'FROM '.$core->prefix."tribe WHERE tribe_id = '".$tribe_id."'"
				);
			if ($rs3->count() == 0) {
				$cur = $core->con->openCursor($core->prefix.'tribe');
				$cur->tribe_id = $tribe_id;
				$cur->user_id = $user_id;
				$cur->tribe_name = $tribe_name['value'];
				if (!empty($ordering) && is_int($ordering)) {
					$cur->ordering = $ordering;
				}
				$cur->visibility = 1;
				$cur->created = array(' NOW() ');
				$cur->modified = array(' NOW() ');
				$cur->insert();

				$output = sprintf(T_("Tribe %s was successfully added"), $tribe_name['value']);
			} else {
				$error[] = T_('This tribe id is already existing. This error should not occur : please report to developper');
			}
		}
		else {
			if (!$tribe_name['success']) {
				$error[] = $tribe_name['error'];
			}
			if (empty($user_id)) {
				$error[] = T_("Please select the user that you want to add to the tribe");
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
# EDIT FEED
##########################################################
	case 'edit':
		$tribe_id = trim($_POST['tribe_id']);
		$rs_tribe = $core->con->select("SELECT * FROM ".$core->prefix."tribe WHERE tribe_id = '$tribe_id'");

		$new_name = !empty($_POST['tribe_name']) ? $_POST['tribe_name'] : $rs_tribe->f('tribe_name');
		$new_ordering = !empty($_POST['tribe_order']) ? intval($_POST['tribe_order']) : $rs_tribe->f('ordering');

		$new_name = check_field('Tribe name',$new_name);

		$error = array();

		if ($new_name['success']
		&& is_int($new_ordering))
		{
			$new_name['value'] = htmlentities($new_name['value'],ENT_QUOTES,mb_detect_encoding($new_name['value']));

			$cur = $core->con->openCursor($core->prefix.'tribe');
			$cur->tribe_name = $new_name['value'];
			$cur->ordering = $new_ordering;
			$cur->modified = array(' NOW() ');
			$cur->update("WHERE tribe_id = '$tribe_id'");

			$output = sprintf(T_("Tribe %s successfully updated"),$new_name['value']);
		} else {
			if (!$new_name['success']) {
				$error[] = $new_name['error'];
			}
			if (!is_int($new_ordering)) {
			$error[] = T_('The ordering has to be an integer value.');
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
# TOGGLE FEED
##########################################################
	case 'toggle':
		$tribe_id = trim($_POST['tribe_id']);
		$rs_tribe = $core->con->select("SELECT visibility FROM ".$core->prefix."tribe WHERE tribe_id = '$tribe_id'");

		if ($rs_tribe->count() > 0) {
			$cur = $core->con->openCursor($core->prefix.'tribe');
			if($rs_tribe->f('visibility') == 1) {
				$cur->visibility = 0;
			} else {
				$cur->visibility = 1;
			}
			$cur->update("WHERE tribe_id = '$tribe_id'");
			print '<div class="flash_notice">'.T_('Tribe visibility toggled').'</div>';
		} else {
			print '<div class="flash_error">'.T_('Tribe does not exist').'</div>';
		}
		break;

##########################################################
# REMOVE TRIBE
##########################################################
	case 'remove':
		$tribe_id = trim($_POST['tribe_id']);
		$rs = $core->con->select("SELECT tribe_name FROM ".$core->prefix."tribe WHERE tribe_id = '$tribe_id'");
		$confirmation = "<p>".sprintf(T_('Are you sure you want to remove tribe %s ?'),
			$rs->f('tribe_name'))."?<br/>";
		$confirmation .= "<ul><li>".T_('This action can not be canceled')."</li>";
		$confirmation .= "</ul><br/>";
		$confirmation .= "<form id='removeTribeConfirm_form'><input type='hidden' name='tribe_id' value='".$tribe_id."'/>";
		$confirmation .= "<div class='button br3px'><input class='notvalide' type='button' onclick=\"javascript:$('#flash-msg').html('')\" value='".T_('Reset')."'/></div>&nbsp;&nbsp;";
		$confirmation .= "<div class='button br3px'><input class='valide' type='submit' name='confirm' value='".T_('Confirm')."'/></div></form></p>";
		print '<div class="flash_warning">'.$confirmation.'</div>';
		break;

##########################################################
# CONFIRM REMOVE TRIBE
##########################################################
	case 'removeConfirm':
		sleep(1);
		$tribe_id = trim($_POST['tribe_id']);
		$rs2 = $core->con->select("SELECT * FROM ".$core->prefix."tribe WHERE tribe_id = '$tribe_id'");
		if (!$rs2->isEmpty()) {
			$core->con->execute("DELETE FROM ".$core->prefix."tribe WHERE tribe_id ='$tribe_id'");
			print '<div class="flash_notice">'.sprintf(T_("Delete of tribe %s succeeded"),$rs2->f('tribe_name')).'</div>';
		} else {
			print '<div class="flash_error">'.T_("This tribe does not exist in the database").'</div>';
		}
		break;


##########################################################
# Remove user on tribe
##########################################################
	case 'rm_user':
		$tribe_id = $_POST['tribe_id'];
        $user_to_rm = $_POST['user'];
        $error = array();

		$sql = "SELECT tribe_users
			FROM ".$core->prefix."tribe
            WHERE tribe_id = '".$tribe_id."'";
		$rs = $core->con->select($sql);
		$tribe_users = preg_split('/,/',$rs->f('tribe_users'), -1, PREG_SPLIT_NO_EMPTY);

		foreach ($tribe_users as $k=>$user) {
            if ($tag == $user_to_rm) {
                unset($tribe_users[$k]);
            }
        }

		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_users = implode(',', $tribe_users);
		$cur->update("WHERE tribe_id='".$tribe_id."'");

        $output = T_("The tag was successfuly removed");

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
# Add users to tribe
##########################################################
	case 'add_users':
		$tribe_id = $_POST['tribe_id'];
        $patterns = array( '/, /', '/ ,/');
        $replacement = array(',', ',');
        $users = urldecode($_POST['users']);
        $users = preg_replace($patterns, $replacement, $users);
        $new_users = preg_split('/,/',$users, -1, PREG_SPLIT_NO_EMPTY);

		$sql = "SELECT tribe_users
			FROM ".$core->prefix."tribe
            WHERE tribe_id = '".$tribe_id."'";
		$rs = $core->con->select($sql);
		$tribe_users = preg_split('/,/',$rs->f('tribe_users'), -1, PREG_SPLIT_NO_EMPTY);

		foreach ($new_users as $user) {
            if (!in_array($user, $tribe_users)) {
				// check if user_id exist before adding
				$rs_search = $core->con->select("SELECT * FROM ".$core->prefix."user WHERE user_id='".$user."'");
				if ($rs_search->count() == 1) {
					$tribe_users[] = $user;
				}
            }
        }

        $output .= T_("Users added : ");
		$all_users_string=implode(',',$tribe_users);
		$output .= $all_users_string;
		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_users = $all_users_string;
		$cur->update("WHERE tribe_id='".$tribe_id."'");

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
# Remove search on tribe
##########################################################
	case 'rm_search':
		$tribe_id = $_POST['tribe_id'];
        $error = array();

		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_search = '';
		$cur->update("WHERE tribe_id='".$tribe_id."'");

        $output = T_("The search was successfuly removed");

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
# Add search to tribe
##########################################################
	case 'add_search':
		$tribe_id = $_POST['tribe_id'];
		$search = trim($_POST['search']);

        $output .= T_("Search added : ".$search);
		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_search = $search;
		$cur->update("WHERE tribe_id='".$tribe_id."'");

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
# Remove tag on tribe
##########################################################
	case 'rm_tag':
		$tribe_id = $_POST['tribe_id'];
        $tag_to_rm = $_POST['tag'];
        $error = array();

		$sql = "SELECT tribe_tags
			FROM ".$core->prefix."tribe
            WHERE tribe_id = '".$tribe_id."'";
		$rs = $core->con->select($sql);
		$tribe_tags = preg_split('/,/',$rs->f('tribe_tags'), -1, PREG_SPLIT_NO_EMPTY);

		foreach ($tribe_tags as $k=>$tag) {
            if ($tag == $tag_to_rm) {
                unset($tribe_tags[$k]);
            }
        }

		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_tags = implode(',', $tribe_tags);
		$cur->update("WHERE tribe_id='".$tribe_id."'");

        $output = T_("The tag was successfuly removed");

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
# Add tags to tribe
##########################################################
	case 'add_tags':
		$tribe_id = $_POST['tribe_id'];
        $patterns = array( '/, /', '/ ,/');
        $replacement = array(',', ',');
        $tags = urldecode($_POST['tags']);
        $tags = preg_replace($patterns, $replacement, $tags);
        $new_tags = preg_split('/,/',$tags, -1, PREG_SPLIT_NO_EMPTY);

		$sql = "SELECT tribe_tags
			FROM ".$core->prefix."tribe
            WHERE tribe_id = '".$tribe_id."'";
		$rs = $core->con->select($sql);
		$tribe_tags = preg_split('/,/',$rs->f('tribe_tags'), -1, PREG_SPLIT_NO_EMPTY);

		foreach ($tribe_tags as $tag) {
            if (in_array($tag, $new_tags)) {
                $key = array_keys($new_tags, $tag);
                unset($new_tags[$key]);
            }
        }

        $output .= T_("Tags added : ");
		$all_tags = array_merge($tribe_tags, $new_tags);
		$all_tags_string=implode(',',$all_tags);
		$output .= $all_tags_string;
		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_tags = $all_tags_string;
		$cur->update("WHERE tribe_id='".$tribe_id."'");

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
# Remove notag on tribe
##########################################################
	case 'rm_notag':
		$tribe_id = $_POST['tribe_id'];
        $tag_to_rm = $_POST['tag'];
        $error = array();

		$sql = "SELECT tribe_notags
			FROM ".$core->prefix."tribe
            WHERE tribe_id = '".$tribe_id."'";
		$rs = $core->con->select($sql);
		$tribe_notags = preg_split('/,/',$rs->f('tribe_notags'), -1, PREG_SPLIT_NO_EMPTY);

		foreach ($tribe_notags as $k=>$tag) {
            if ($tag == $tag_to_rm) {
                unset($tribe_notags[$k]);
            }
        }

		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_notags = implode(',', $tribe_notags);
		$cur->update("WHERE tribe_id='".$tribe_id."'");

        $output = T_("The unwanted tag was successfuly removed");

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
# Add notags to tribe
##########################################################
	case 'add_notags':
		$tribe_id = $_POST['tribe_id'];
        $patterns = array( '/, /', '/ ,/');
        $replacement = array(',', ',');
        $tags = urldecode($_POST['tags']);
        $tags = preg_replace($patterns, $replacement, $tags);
        $new_tags = preg_split('/,/',$tags, -1, PREG_SPLIT_NO_EMPTY);

		$sql = "SELECT tribe_notags
			FROM ".$core->prefix."tribe
            WHERE tribe_id = '".$tribe_id."'";
		$rs = $core->con->select($sql);
		$tribe_notags = preg_split('/,/',$rs->f('tribe_notags'), -1, PREG_SPLIT_NO_EMPTY);

		foreach ($tribe_notags as $tag) {
            if (in_array($tag, $new_tags)) {
                $key = array_keys($new_tags, $tag);
                unset($new_tags[$key]);
            }
        }

        $output .= T_("Notags added : ");
		$all_tags = array_merge($tribe_notags, $new_tags);
		$all_tags_string=implode(',',$all_tags);
		$output .= $all_tags_string;
		$cur = $core->con->openCursor($core->prefix.'tribe');
		$cur->tribe_notags = $all_tags_string;
		$cur->update("WHERE tribe_id='".$tribe_id."'");

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
# ADD TRIBE ICON
##########################################################
	case 'add_icon':
		$tribe_id = $_POST['tribe_id'];

		$icon = $_POST['icon'];
		$userfile_name = $_FILES["icon"]["name"];
		$userfile_tmp = $_FILES["icon"]["tmp_name"];
		$userfile_size = $_FILES["icon"]["size"];
		$userfile_type = $_FILES["icon"]["type"];
		$filename = basename($_FILES["icon"]["name"]);

		$tribe_icon_folder = 'data/images';
		$folder = dirname(__FILE__).'/../../'.$tribe_icon_folder;

		// check upload
		if (intval($_FILES['icon']['error']) != 0) {
			$error[] = T_('There was an error during file upload');
		}

		// check destination folder
		if (!is_dir($folder)) {
			$error[] = T_('The folder data/images does not exists !');
		}
		if (!is_writable($folder)) {
			$error[] = T_('The folder data/images must writable !');
		}

		// check file size
		if (intval($userfile_size) > 150000) {
			$error[] = T_('The image file should not exceed 150Kb');
		}

		// check extension and format
		$extensions = array('jpg' => 'image/jpeg', 'jpeg'=>'image/jpeg', 'png'=>'image/png', 'gif'=>'image/gif');
		$userfile_ext = explode('.', $userfile_name);
		$userfile_ext = strtolower($userfile_ext[count($userfile_ext)-1]);
		if (!array_key_exists($userfile_ext, $extensions)) {
			$error[] = T_('The format of the file is not supported : ').$userfile_ext.' ; '.$userfile_type;
		}
		$userfile_imgsize = getimagesize($userfile_tmp);
		if ($userfile_imgsize['mime'] != $extensions[$userfile_ext]) {
			$error[] = T_('This file has the wrong file extension');
		}

		// check the image size
		$allowed_ratio = 0.75;
		if ($userfile_imgsize[0]/$userfile_imgsize[1] < $allowed_ratio
			|| $userfile_imgsize[1]/$userfile_imgsize[0] < $allowed_ratio) {
			$error[] = T_('The tribe icon has to be almost square (allowed rate of 0.9)');
		}

		// create virtual image resource
		$image = null;
		switch($userfile_ext) {
		case 'jpg': $image = imagecreatefromjpeg($userfile_tmp); break;
		case 'jpeg': $image = imagecreatefromjpeg($userfile_tmp); break;
		case 'png': $image = imagecreatefrompng($userfile_tmp); break;
		case 'gif': $image = imagecreatefromgif($userfile_tmp); break;
		}
		if ($image == null) {
			$error[] = sprintf(T_('The image could not be modified. (File extension is %s)'),$userfile_ext);
		}

		if (empty($error)) {
			// resize image
			$height = 100; // defined height
			$width = ( ($userfile_imgsize[0] * (($height)/$userfile_imgsize[1]))); // relative width
			$final_image = imagecreatetruecolor($width , $height)
				or $error[] = T_('Error when creating final image');

			// Adding Alpha channel to created image :
			imagealphablending($final_image,false);
			imagesavealpha($final_image, true);
			$trans_colour = imagecolorallocatealpha($final_image, 0, 0, 0, 127);
			imagefilledrectangle($final_image,0,0,$width,$height,$trans_colour);
			//imagefill($final_image, 0, 0, $trans_colour);
			//$red = imagecolorallocate($final_image, 255, 0, 0);
			//imagefilledellipse($final_image, 400, 300, 400, 300, $red);

			imagecopyresampled($final_image ,$image , 0,0, 0,0, $width, $height, $userfile_imgsize[0],$userfile_imgsize[1])
				or $error[] = T_('Error while resizing final image');
			imagedestroy($image)
				or $error[] = T_('Error while deleting temporary image');

			$filename = 'tribe-'.$tribe_id.'-'.time().'.'.$userfile_ext;
			$file_fullpath = $folder.'/'.strtolower($filename);
			if (is_file($file_fullpath)) {
				$error[] = T_('The file you want to copy is alreay existing. Please try again.');
			} else {
				$file_relativepath = $tribe_icon_folder.'/'.strtolower($filename);

				// save image to folder
				if ($userfile_ext == 'jpg') {
					$save = imagejpeg($final_image , $file_fullpath, 100);
				}
				if ($userfile_ext == 'png') {
					$save = imagepng($final_image , $file_fullpath, 0);
				}
				if ($userfile_ext == 'gif') {
					$save = imagegif($final_image, $file_fullpath);
				}
				if ($save) {
					$cur = $core->con->openCursor($core->prefix.'tribe');
					$cur->tribe_icon = $file_relativepath;
					$cur->update("WHERE tribe_id='".$tribe_id."'");
					$output = T_("The image was successfully uploaded to the tribe");
				} else {
					$error[] = sprintf(T_('The file could not be saved to %s'), $file);
				}
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
# REMOVE TRIBE ICON
##########################################################
	case 'rm_icon':
		$tribe_id = $_POST['tribe_id'];

		$rs_tribe = $core->con->select("SELECT tribe_icon FROM ".$core->prefix."tribe
				WHERE tribe_id = '".$tribe_id."'");
		if ($rs_tribe->count() != 1) {
			$error[] = T_('This tribe have no icons');
		}
		$iconfile = dirname(__FILE__).'/../../'.$rs_tribe->f('tribe_icon');
		if (!is_file($iconfile)) {
			$error[] = sprintf(T_('The icon file %s does not exists'),$iconfile);
		}
		if (empty($error)) {
			$del = unlink($iconfile);
			if ($del) {
					$cur = $core->con->openCursor($core->prefix.'tribe');
					$cur->tribe_icon = '';
					$cur->update("WHERE tribe_id='".$tribe_id."'");
					$output = T_("The icon was successfully removed from the tribe");
			} else {
				$error[] = T_('Impossible to delete file');
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
# GET TRIBE LIST
##########################################################
	case 'list':
		$num_page = !empty($_POST['num_page']) ? $_POST['num_page'] : 0;
		$nb_items = !empty($_POST['nb_items']) ? $_POST['nb_items'] : 30;
		$num_start = $num_page * $nb_items;

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
			ORDER by ordering
			ASC LIMIT '.$nb_items.' OFFSET '.$num_start;

		print getOutput($sql, $num_page, $nb_items);
		break;

	default:
		print '<div class="flash_error">'.T_('User bad call').'</div>';
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
	$output = showPagination($rs->count(), $num_page, $nb_items, 'updateTribeList');

	$output .= '
<br />
<div id="tribelist" class="tribe-list">';

	if ($rs->count() > 0) {
		while ($rs->fetch()) {
			$sql_post = generate_tribe_SQL(
				$rs->tribe_id,
				0,
				0);
//			print $sql_post;
			$rs_post = $core->con->select($sql_post);

			$tribe_state = "private";
			$tribe_state_img="lock_locked";
			if ($rs->visibility == 1) {
				$tribe_state = "public";
				$tribe_state_img="lock_unlock";
			}
			$tribe_owner = T_('Admin');
			if ($rs->user_id != "root") {
				$tribe_owner = $rs->user_id;
			}
			$tribe_name = html_entity_decode($rs->tribe_name, ENT_QUOTES, 'UTF-8');

			$tribe_tags = preg_split('/,/',$rs->tribe_tags, -1, PREG_SPLIT_NO_EMPTY);
			$tag_list = "";
			foreach ($tribe_tags as $tag_item) {
				$tag_list .= '<span class="tag">'.$tag_item.' <a href="javascript:rm_tag('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.$tag_item.'\')">x</a>';
				$tag_list .= '</a></span>';
			}

			$tribe_notags = preg_split('/,/',$rs->tribe_notags, -1, PREG_SPLIT_NO_EMPTY);
			$notag_list = "";
			foreach ($tribe_notags as $tag_item) {
				$notag_list .= '<span class="tag">'.$tag_item.' <a href="javascript:rm_notag('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.$tag_item.'\')">x</a>';
				$notag_list .= '</a></span>';
			}

			$tribe_users = preg_split('/,/',$rs->tribe_users, -1, PREG_SPLIT_NO_EMPTY);
			$user_list = "";
			foreach ($tribe_users as $user_item) {
				$user_list .= '<span class="user">'.$user_item.' <a href="javascript:rm_user('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.$user_item.'\')">x</a>';
				$user_list .= '</a></span>';
			}

			$rm_search_action = '('.T_('empty').')';
			if ($rs->tribe_search) {
				$rm_search_action = '(<a href="javascript:rm_search('.$num_page.', '.$nb_items.',\''.$rs->tribe_id.'\')">'.T_('clear').'</a>)';
			}

			$tribe_icon = '';
			$icon_action = '<a href="javascript:add_icon('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')">
				<img src="meta/icons/add_icon.png" title="'.T_('Add icon to tribe').'" /></a>';
			if ($rs->tribe_icon) {
				$tribe_icon = '<p class="tribe-icon"><img class="tribe-icon" src="../'.$rs->tribe_icon.'" /></p>';
				$icon_action = '<a href="javascript:rm_icon('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\')">
				<img src="meta/icons/rm_icon.png" title="'.T_('Remove icon from tribe').'" /></a>';
			}

			$output .= '<div class="tribesbox tribe-'.$tribe_state.'" id="tribe-'.$rs->tribe_id.'">
				<h3><a href="'.BP_PLANET_URL.'/index.php?list=1&tribe_id='.$rs->tribe_id.'">'.$tribe_name.'</a></h3>
				'.$tribe_icon.'
				<p class="nickname">
					Tribe owner : '.$tribe_owner.'<br/>
					Tags : <div class="tag-line">'.$tag_list.'</div><br/>
					No-tags : <div class="notag-line">'.$notag_list.'</div><br/>
					Users : <div class="user-line">'.$user_list.'</div><br/>
					search : '.$rs->tribe_search.' '.$rm_search_action.'<br/>
					Last post : '.mysqldatetime_to_date("d/m/Y",$rs_post->last).'<br/>
					Post count : '.$rs_post->count.'<br/>
					Ordering : '.$rs->ordering.'
				</p>
				<ul class="actions">
					<li><a href="javascript:toggleTribeVisibility(\''.$rs->tribe_id.'\','.$num_page.','.$nb_items.')"><img src="meta/icons/'.$tribe_state_img.'.png" title="'.T_('Toggle tribe visibility').'"/></a></li>
					<li><a href="javascript:edit(\''.$rs->tribe_id.'\', '.$num_page.', '.$nb_items.')"><img src="meta/icons/action-edit.png" title="'.T_('Edit tribe').'" /></a></li>
					<li><a href="javascript:removeTribe(\''.$rs->tribe_id.'\','.$num_page.','.$nb_items.')"><img src="meta/icons/cross.png" title="'.T_('Remove tribe').'" /></a></li>
					<li><a href="javascript:add_tags('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')"><img src="meta/icons/add_tag.png" title="'.T_('Add tags to tribe').'"/></a></li>
					<li><a href="javascript:add_notags('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')"><img src="meta/icons/add_notag.png" title="'.T_('Add unwanted tags to tribe').'"/></a></li>
					<li><a href="javascript:add_users('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')"><img src="meta/icons/add_user.png" title="'.T_('Add users to tribe').'" /></a></li>
					<li><a href="javascript:add_search('.$num_page.','.$nb_items.',\''.$rs->tribe_id.'\',\''.addslashes($tribe_name).'\')"><img src="meta/icons/add_search.png" title="'.T_('Add search to tribe').'" /></a></li>
					<li>'.$icon_action.'</li>
				</ul>
				<div class="feedlink"><a href="'.BP_PLANET_URL.'/index.php?list=1&tribe_id='.$rs->tribe_id.'">
						<img alt="RSS" src="'.BP_PLANET_URL.'/themes/'.$blog_settings->get('planet_theme').'/images/rss_24.png" /></a></div>
				</div>';
		}
	} else {
		$output .= '<div class="tribebox">
				'.T_('No tribes found').'
			</div>';
	}

	$output .= '</div>';
	$output .= showPagination($rs->count(), $num_page, $nb_items, 'updateFeedList');

	return $output;
}
?>
