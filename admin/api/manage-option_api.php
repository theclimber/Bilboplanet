<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : http://chili.kiwais.com/projects/bilboplanet
* Blog : blog.bilboplanet.com
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
if(isset($_POST) && isset($_POST['action'])) {

	switch (trim($_POST['action'])){
######################################
# Update Options
######################################
		case 'update':
			$flash = array();

			if (!empty($_POST['title'])) {
				$title = htmlentities(stripslashes(trim($_POST['title'])), ENT_QUOTES, 'UTF-8');
				$blog_settings->put('planet_title', $title, "string");
			}
			else {
				$flash[] = array('type' => 'error', 'msg' => T_("The title can not be empty"));
			}

			if (!empty($_POST['desc'])) {
				$desc = htmlentities(stripslashes(trim($_POST['desc'])), ENT_QUOTES, 'UTF-8');
				$blog_settings->put('planet_desc', $desc, "string");
			}
			else {
				$blog_settings->put('planet_desc', '', "string");
			}

			$blog_settings->put('planet_theme', trim($_POST['theme']), "string");

			$lang = trim($_POST['lang']);
			$blog_settings->put('planet_lang', $lang, "string");
			if ($lang == 'ar') {
				$blog_settings->put('planet_rtl', '1', 'boolean');
			} else {
				$blog_settings->put('planet_rtl', '0', 'boolean');
			}

			if (!empty($_POST['msg_info'])) {
				$msg_info = htmlentities(stripslashes(trim($_POST['msg_info'])), ENT_QUOTES, 'UTF-8');
				$blog_settings->put('planet_msg_info', $msg_info, "string");
			}
			else {
				$blog_settings->put('planet_msg_info', '', "string");
			}

			if (!empty($_POST['planet_reserved_tags'])) {
				$planet_reserved_tags = stripslashes(trim($_POST['planet_reserved_tags']));
				$blog_settings->put('planet_reserved_tags', $planet_reserved_tags, "string");
			}
			else {
				$blog_settings->put('planet_reserved_tags', '', "string");
			}

			if (isset($_POST['show_contact'])) {
				$blog_settings->put('planet_contact_page', '1', "boolean");
			}
			else {
				$blog_settings->put('planet_contact_page', '0', "boolean");
			}

			if (isset($_POST['show_votes'])) {
				$blog_settings->put('planet_vote', '1', "boolean");
			}
			else {
				$blog_settings->put('planet_vote', '0', "boolean");
			}

			$blog_settings->put('planet_votes_system', trim($_POST['system_votes']), "string");

			if (isset($_POST['moderation'])) {
				$blog_settings->put('planet_moderation', '1', "boolean");
			}
			else {
				$blog_settings->put('planet_moderation', '0', "boolean");
			}

			if (isset($_POST['allow_post_comments'])) {
				$blog_settings->put('allow_post_comments', '1', "boolean");
			}
			else {
				$blog_settings->put('allow_post_comments', '0', "boolean");
			}

			if (isset($_POST['avatar'])) {
				$blog_settings->put('planet_avatar', '1', "boolean");
			}
			else {
				$blog_settings->put('planet_avatar', '0', "boolean");
			}

			if (isset($_POST['maintenance'])) {
				$blog_settings->put('planet_maint', '1', "boolean");
			}
			else {
				$blog_settings->put('planet_maint', '0', "boolean");
			}

			$blog_settings->put('planet_log', trim($_POST['log_level']), "string");

			if (is_numeric(trim($_POST['nb_posts_page']))) {
				$nb_posts_page = trim($_POST['nb_posts_page']);
				$blog_settings->put('planet_nb_post', $nb_posts_page, "integer");
			}
			else {
				$flash[] = array('type' => 'error', 'msg' => T_("Only numeric value are allowed for number of posts by page"));
			}

			if (!empty($_POST['planet_analytics'])) {
				$planet_analytics = $_POST['planet_analytics'];
				$blog_settings->put('planet_analytics', $planet_analytics, "string");
			} else {
				$blog_settings->put('planet_analytics', '', "string");
			}
			if (!empty($_POST['ganalytics'])) {
				$ganalytics = $_POST['ganalytics'];
				$blog_settings->put('planet_ganalytics', $ganalytics, "string");
			} else {
				$blog_settings->put('planet_ganalytics', '', "string");
			}
			if (!empty($_POST['piwik_url'])) {
				$piwik_url = $_POST['piwik_url'];
				$blog_settings->put('piwik_url', $piwik_url, "string");
			} else {
				$blog_settings->put('piwik_url', '', "string");
			}
			if (!empty($_POST['piwik_id'])) {
				$piwik_id = $_POST['piwik_id'];
				$blog_settings->put('piwik_id', $piwik_id, "integer");
			} else {
				$blog_settings->put('piwik_id', '', "integer");
			}

			if (isset($_POST['internal_links'])) {
				$blog_settings->put('internal_links', '1', "boolean");
			}
			else {
				$blog_settings->put('internal_links', '0', "boolean");
			}

			if (isset($_POST['allow_uncensored_feed'])) {
				$blog_settings->put('allow_uncensored_feed', '1', "boolean");
			}
			else {
				$blog_settings->put('allow_uncensored_feed', '0', "boolean");
			}

			if (isset($_POST['show_similar_posts'])) {
				$blog_settings->put('show_similar_posts', '1', "boolean");
			}
			else {
				$blog_settings->put('show_similar_posts', '0', "boolean");
			}

			if (isset($_POST['planet_shaarli'])) {
				$blog_settings->put('planet_shaarli', '1', "boolean");
			}
			else {
				$blog_settings->put('planet_shaarli', '0', "boolean");
			}

			if (isset($_POST['subscription']) && !empty($_POST['subscription_content'])) {
				$blog_settings->put('planet_subscription', '1', "boolean");
				$subscription_content = htmlentities(stripslashes(trim($_POST['subscription_content'])), ENT_QUOTES, 'UTF-8');
				$blog_settings->put('planet_subscription_content', $subscription_content, "string");
			}
			elseif (isset($_POST['subscription']) && empty($_POST['subscription_content'])){
				$flash[] = array('type' => 'error', 'msg' => T_("Subscription page content can not be empty"));
			}
			else {
				$blog_settings->put('planet_subscription', '0', "boolean");
			}


			############
			# Add post values for statusNet
			############
			# Needed options :
			# * statusnet host
			# * statusnet username
			# * statusnet password
			# * status message formating
			# * enable/disable publishing new posts

			# statusnet_host :
			if (!empty($_POST['statusnet_host'])) {
				$statusnet_host = $_POST['statusnet_host'];

				$blog_settings->put('statusnet_host', $statusnet_host, "string");
			} else {
				$blog_settings->put('statusnet_host', '', "string");
			}

			# statusnet_username :
			if (!empty($_POST['statusnet_username'])) {
				$statusnet_username = stripslashes(trim($_POST['statusnet_username']));;
				$blog_settings->put('statusnet_username', $statusnet_username, "string");
			} else {
				$blog_settings->put('statusnet_username', '', "string");
			}

			# statusnet_password :
			if (!empty($_POST['statusnet_password'])) {
				$statusnet_passw = stripslashes(trim($_POST['statusnet_password']));
				$blog_settings->put('statusnet_password', $statusnet_passw, "string");
			} else {
				$blog_settings->put('statusnet_password', '', "string");
			}

			# statusnet_post_format :
			if (!empty($_POST['statusnet_post_format'])) {
				$format = stripslashes(trim($_POST['statusnet_post_format']));
			} else {
				$format = stripslashes(trim('[%(author)s] %(title)s'));;
			}
			$blog_settings->put('statusnet_post_format', $format, "string");

			# statusnet enable/disable posts :
			if (isset($_POST['statusnet_auto_post'])) {

				if (!isset($statusnet_host)) {
					$statusnet_host = '';
				}
				if (!isset($statusnet_username)) {
					$statusnet_username = '';
				}
				if(!isset($statusnet_passw)){
					$statusnet_passw = '';
				}

				$config_url = $statusnet_host."/api/statusnet/config.json";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $config_url);
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_USERPWD, $statusnet_username.":".$statusnet_passw);
				curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
				curl_setopt($ch, CURLOPT_GET, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

				$result = json_decode(curl_exec($ch));
				// Look at the returned header
				$resultArray = curl_getinfo($ch);

				curl_close($ch);

				if($resultArray['http_code'] == "200") {
					$blog_settings->put('statusnet_textlimit',$result->site->textlimit,"integer");
					$blog_settings->put('statusnet_auto_post', '1', "boolean");
				}
				else {
					$flash[] = array('type' => 'error', 'msg' => T_("Impossible to reach statusnet server : the service could not be activated"));
				}
			}
			else {
				$blog_settings->put('statusnet_auto_post', '0', "boolean");
			}

			if (!empty($flash)) {
				$output = '<ul>';
				foreach($flash as $value) {
					$output .= "<li>".$value['msg']."</li>";
				}
				$output .= '</ul>';
				print '<div class="flash_error">'.$output.'</div>';
			}
			else {
				$output = T_("Modification succeeded");
				print '<div class="flash_notice">'.$output.'</div>';
			}

			break;
######################################
# Get Options
######################################
		case 'options-form':

			# Init var
			$output = "";
			$theme_list = array();
			$lang_list = array();
			$arr_system_votes = array('yes-no', 'yes-warn');
			$arr_log_level = array('notice', 'debug');

			# Load value from setting table
			$title = stripslashes($blog_settings->get('planet_title'));
			$desc = stripslashes($blog_settings->get('planet_desc'));
			$msg_info = stripslashes($blog_settings->get('planet_msg_info'));
			$planet_reserved_tags = stripslashes($blog_settings->get('planet_reserved_tags'));
			$votes = $blog_settings->get('planet_vote');
			$contact = $blog_settings->get('planet_contact_page');
			$theme = $blog_settings->get('planet_theme');
			$lang = $blog_settings->get('planet_lang');
			$moderation = $blog_settings->get('planet_moderation');
			$maintenance = $blog_settings->get('planet_maint');
			$system_votes = $blog_settings->get('planet_votes_system');
			$allow_post_comments = $blog_settings->get('allow_post_comments');
			$avatar = $blog_settings->get('planet_avatar');
			$log_level = $blog_settings->get('planet_log');
			$nb_posts_page = $blog_settings->get('planet_nb_post');
			$subscription = $blog_settings->get('planet_subscription');
			$subscription_content = html_entity_decode(stripslashes($blog_settings->get('planet_subscription_content')), ENT_QUOTES, 'UTF-8');
			$planet_analytics = $blog_settings->get('planet_analytics');
			$ganalytics = $blog_settings->get('planet_ganalytics');
			$piwik_url = $blog_settings->get('piwik_url');
			$piwik_id = $blog_settings->get('piwik_id');
			$internal_links = $blog_settings->get('internal_links');
			$allow_uncensored_feed = $blog_settings->get('allow_uncensored_feed');
			$show_similar_posts = $blog_settings->get('show_similar_posts');
			$planet_shaarli = $blog_settings->get('planet_shaarli');

			# statusnet options :
			$statusnet = array(
				'host' => $blog_settings->get('statusnet_host'),
				'username' => stripslashes($blog_settings->get('statusnet_username')),
				'password' => stripslashes($blog_settings->get('statusnet_password')),
				'post_format' => stripslashes($blog_settings->get('statusnet_post_format')),
				'auto_post' => $blog_settings->get('statusnet_auto_post')
				);

			# Build an array of available theme
			$theme_path = dirname(__FILE__)."/../../themes/";
			$dir_handle = @opendir($theme_path) or die("Unable to open $theme_path");
			while ($file = readdir($dir_handle)){
				if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess" && is_dir($theme_path.$file)){
					if ($file == $theme) {
						$theme_list[] = array('name' => $file, 'selected' => true);
					}
					else {
						$theme_list[] = array('name' => $file, 'selected' => false);
					}
				}
			}
			closedir($dir_handle);

			# Build an array of available lang
			$lang_path = dirname(__FILE__)."/../../i18n/";
			$dir_handle = @opendir($lang_path) or die("Unable to open $lang_path");
			while ($file = readdir($dir_handle)){
				if($file!="." && $file!=".." && $file!=".svn" && $file!=".DS_Store" && $file!=".htaccess" && is_dir($lang_path.$file)){
					if ($file == $lang) {
						$lang_list[] = array('name' => $file, 'selected' => true);
					}
					else {
						$lang_list[] = array('name' => $file, 'selected' => false);
					}
				}
			}

			# Display form
			$output .= '<form method="POST" id="manage_options">
	<table id="tbl1" class="table-news">
		<thead>
			<tr>
				<th class="tc5 tcr" scope="col">'.T_('Option').'</th>
				<th class="tc2 tcr" scope="col">'.T_('Value').'</th>
			</tr>
		</thead>
		<tr>
			<td>'.T_('Title of the Planet').'</td>
			<td><input id="cadre_options" class="input field" type="text" name="title" value="'.$title.'" /></td>
		</tr>
		<tr>
			<td>'.T_('Description of the Planet').'</td>
			<td><input id="cadre_options" class="input field" type="text" name="desc" value="'.$desc.'" /></td>
		</tr>
		<tr>
			<td>'.T_('Graphical theme').'</td>
			<td>
				<select name="theme" class="field">';
				foreach($theme_list as $value) {
					if($value['selected']) {
						$output .= '<option value="'.$value['name'].'" selected >'.$value['name'].'</option>'."\n";
					}
					else {
						$output .= '<option value="'.$value['name'].'" >'.$value['name'].'</option>'."\n";
					}
				}
				$output .= '</select>
			</td>
		</tr>
		<tr>
			<td>'.T_('Language of the Planet').'</td>
			<td>
				<select name="lang" class="field">';
				foreach($lang_list as $value) {
					if($value['selected']) {
						$output .= '<option value="'.$value['name'].'" selected >'.$value['name'].'</option>'."\n";
					}
					else {
						$output .= '<option value="'.$value['name'].'" >'.$value['name'].'</option>'."\n";
					}
				}
				if($lang == "en") {
					$output .= '<option value="en" selected >en</option>'."\n";
				}
				else {
					$output .= '<option value="en" >en</option>'."\n";
				}
				$output .= '</select>
			</td>
		</tr>
		<tr>
			<td>'.T_('Information message (optional)').'</td>
			<td><textarea class="cadre_option field" name="msg_info" rows=3>'.$msg_info.'</textarea></td>
		</tr>
		<tr>
			<td>'.T_('Reserved tags').'
				<div class="comment"><p>
					'.T_('comma separated tags (i.e : pl-agenda, pl-selected)').'
				</p></div>
			</td>
			<td><input type="text" class="input field" name="planet_reserved_tags" value="'.$planet_reserved_tags.'"</input></td>
		</tr>
		<tr>
			<td>'.T_('Show the contact page').'</td>';
			if($contact) {
				$output .= '<td><input type="checkbox" class="input field" id="show_contact" name="show_contact" checked/></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="show_contact" name="show_contact" /></td>';
			}
			$output .='</tr>
		<tr>
			<td>'.T_('Enable voting').'</td>';
			if($votes) {
				$output .= '<td><input type="checkbox" class="input field" id="show_votes" name="show_votes" checked onclick="javascript:votes_state(\'options-form\');" /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="show_votes" name="show_votes" onclick="javascript:votes_state(\'options-form\');" /></td>';
			}
			$output .='</tr>
		<tr id="votes_system" style="display: none;">
			<td>
				'.T_('Votes system').'
				<div class="comment">
					<p>
						'.T_('yes-no').' : '.T_('Positive and Negative').'
						<br />
						'.T_('yes-warn').' : '.T_('Positive and Warning').'
					</p>
				</div>
			</td>
			<td>';
				$output .= '<select name="system_votes" class="field">';
					foreach($arr_system_votes as $value) {
						if($value == $system_votes) {
							$output .= '<option value="'.$value.'" selected>'.T_($value).'</option>';
						}
						else {
							$output .= '<option value="'.$value.'" >'.T_($value).'</option>';
						}
					}
				$output .= '</select>
			</td>
		</tr>
		<tr>
			<td>'.T_('Option of moderation').'</td>';
			if($moderation) {
				$output .= '<td><input type="checkbox" class="input field" id="moderation" name="moderation" checked ></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="moderation" name="moderation" ></td>';
			}
		$output .= '</tr>
		<tr>
			<td>
				'.T_('Allow comments on posts').'
				<div class="comment">
					<p>
						'.T_('This will allow some post to accept some comments').'
					</p>
				</div>
			</td>';
			if($allow_post_comments) {
				$output .= '<td><input type="checkbox" class="input field" id="allow_post_comments" name="allow_post_comments" checked /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="allow_post_comments" name="allow_post_comments" /></td>';
			}
		$output .= '</tr>
		<tr>
			<td>
				'.T_('Enable avatar').'
				<div class="comment">
					<p>
						'.T_('Work with').' <a href="https://www.libravatar.org" target="_blank" >https://www.libravatar.org/</a>
					</p>
				</div>
			</td>';
			if($avatar) {
				$output .= '<td><input type="checkbox" class="input field" id="avatar" name="avatar" checked /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="avatar" name="avatar" /></td>';
			}
		$output .= '</tr>
		<tr>
			<td>'.T_('Maintenance mode').'</td>';
			if($maintenance) {
				$output .= '<td><input type="checkbox" class="input field" id="maintenance" name="maintenance" checked /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="maintenance" name="maintenance" /></td>';
			}
		$output .= '</tr>
		<tr>
			<td>'.T_('Log level').'</td>
			<td>
				<select name="log_level" class="field">';
				foreach($arr_log_level as $value) {
					if($value == $log_level) {
						$output .= '<option value="'.$value.'" selected>'.T_($value).'</option>';
					}
					else {
						$output .= '<option value="'.$value.'" >'.T_($value).'</option>';
					}
				}
				$output .= '</select>
			</td>
		</tr>
		<tr>
			<td>'.T_('Number of posts by page').'</td>
			<td><input id="cadre_options" class="input field" type="text" name="nb_posts_page" value="'.$nb_posts_page.'" /></td>
		</tr>
		<tr>
			<td>'.T_('Analytics engine').'</td>
			<td>';
			$selected = array('google' => '', 'piwik' => '', 'disabled' => '');
			if ($planet_analytics == "google-analytics") {
				$selected['google'] = 'selected';
			} elseif ($planet_analytics == 'piwik') {
				$selected['piwik'] = 'selected';
			} else {
				$selected['disabled'] = 'selected';
			}
			$output .= '
				<select name="planet_analytics" class="field">
					<option value="google-analytics" '.$selected['google'].'>'.T_('Google-Analytics').'</option>
					<option value="piwik" '.$selected['piwik'].'>'.T_('Piwik').'</option>
					<option value="disabled" '.$selected['disabled'].'>'.T_('Disabled').'</option>
				</select>
			</td>
		<tr>
			<td>'.T_('Google Analytics id').'</td>
			<td><input id="cadre_options" class="input field" type="text" name="ganalytics" value="'.$ganalytics.'" /></td>
		</tr>
		<tr>
			<td>'.T_('Piwik site URL').'</td>
			<td><input id="cadre_options" class="input field" type="text" name="piwik_url" value="'.$piwik_url.'" /></td>
		</tr>
		<tr>
			<td>'.T_('Piwik site id').'</td>
			<td><input id="cadre_options" class="input field" type="text" name="piwik_id" value="'.$piwik_id.'" /></td>
		</tr>
		<tr>
			<td>'.T_('Enable Internal links').'</td>';
			if($internal_links) {
				$output .= '<td><input type="checkbox" class="input field" id="internal_links" name="internal_links" checked /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="internal_links" name="internal_links" /></td>';
			}

			$output .= '</tr>
		<tr>
				<td>'.sprintf(T_('Allow using feed with no moderation at <br/>%s'),
					'<a href="'.BP_PLANET_URL.'/feed.php?type=atom&uncensored=true">'.
						BP_PLANET_URL.'/feed.php?type=atom&uncensored=true'.'</a>').'</td>';
			if($allow_uncensored_feed) {
				$output .= '<td><input type="checkbox" class="input field" id="allow_uncensored_feed" name="allow_uncensored_feed" checked /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="allow_uncensored_feed" name="allow_uncensored_feed" /></td>';
			}

			$output .= '</tr>
		<tr>
				<td>'.T_('Show similar posts at the bottom of each post').'</td>';
			if($show_similar_posts) {
				$output .= '<td><input type="checkbox" class="input field" id="show_similar_posts" name="show_similar_posts" checked /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="show_similar_posts" name="show_similar_posts" /></td>';
			}

			$output .= '</tr>
		<tr>
				<td>'.T_('Enable shaarli instances on /shaarli').'</td>';
			if($planet_shaarli) {
				$output .= '<td><input type="checkbox" class="input field" id="planet_shaarli" name="planet_shaarli" checked /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="planet_shaarli" name="planet_shaarli" /></td>';
			}
			$output .= '</tr>';
			############
			# Add post values for statusNet
			############

			# statusnet_host :
			$output .= '<tr><td>'.T_('Planet statusnet host').'</td>';
			$output .= '<td><input id="cadre_options" class="input field" type="text" name="statusnet_host" value="'.$statusnet['host'].'" /> '.T_('(ex: http://identi.ca)').'</td>';
			$output .= '</tr>';

			# statusnet_username :
			$output .= '<tr><td>'.T_('Planet statusnet username').'</td>';
			$output .= '<td><input id="cadre_options" class="input field" type="text" name="statusnet_username" value="'.$statusnet['username'].'" /></td>';
			$output .= '</tr>';

			# statusnet_password :
			$output .= '<tr><td>'.T_('Planet statusnet password').'</td>';
			$output .= '<td><input id="cadre_options" class="input field" type="password" name="statusnet_password" value="'.$statusnet['password'].'" /></td>';
			$output .= '</tr>';

			# statusnet_post_format :
			$output .= '<tr><td>'.T_('Statusnet messages format').'</td>';
			$output .= '<td><input id="cadre_options" class="input field" type="text" name="statusnet_post_format" value="'.$statusnet['post_format'].'" /> '.T_('(ex: "[Site name] %(author)s %(title)s" where "%(author)s" is the author of the post and "%(title)s is the title")').'</td>';
			$output .= '</tr>';

			# statusnet_auto_post :
			$output .= '<tr><td>'.T_('Statusnet activate automatic post').'</td>';
			if($statusnet['auto_post']) {
				$output .= '<td><input type="checkbox" class="input field" id="statusnet_auto_post" name="statusnet_auto_post" checked /></td>';
			} else {
				$output .= '<td><input type="checkbox" class="input field" id="statusnet_auto_post" name="statusnet_auto_post" /></td>';
			}
			$output .= '</tr>';


			$output .= '<tr>
				<td>'.T_('Enable subscription').'</td>';
			if($subscription) {
				$output .= '<td><input type="checkbox" class="input field" id="subscription" name="subscription" checked onclick="javascript:subscription_state(\'options-form\');" /></td>';
			}
			else {
				$output .= '<td><input type="checkbox" class="input field" id="subscription" name="subscription" onclick="javascript:subscription_state(\'options-form\');" /></td>';
			}
		//	$output .= '</tr>';
			$output .= '</table>
	<br />
	<div id="subscription_content" style="display:none;">
		'.T_('Subscription page content').'
		<br />
		<div class="wysiwyg"><script type="text/javascript">edToolbar(\'form_subscription_content\')</script></div>
		<textarea id="form_subscription_content" class="cadre_option field" name="subscription_content" rows="30">'.$subscription_content.'</textarea>
		<br /><br />
	</div>
	<div id="preview_button" style="display:none;" class="MyPreview">
		<div id="toto" class="button br3px">
			<input type="button" class="preview field" name="preview" onclick="javascript:call_preview(\'form_subscription_content\')" value="'.T_('Preview').'" />
		</div>
	</div>
	<div class="button br3px">
		<input type="button" class="valide field" name="submit" value="'.T_('Apply').'" onclick="javascript:updateopt()"/>
	</div>
	<div id="options-loading" ><!--Spinner--></div>
</form>';

			print $output;
			break;
##############################
# Display settings of planet
##############################
		case 'list':
			# Load value from setting table
			$title = stripslashes($blog_settings->get('planet_title'));
			$desc = stripslashes($blog_settings->get('planet_desc'));
			$msg_info = stripslashes($blog_settings->get('planet_msg_info'));
			$planet_reserved_tags = stripslashes($blog_settings->get('planet_reserved_tags'));
			$votes = $blog_settings->get('planet_vote');
			$contact = $blog_settings->get('planet_contact_page');
			$theme = $blog_settings->get('planet_theme');
			$lang = $blog_settings->get('planet_lang');
			$moderation = $blog_settings->get('planet_moderation');
			$maintenance = $blog_settings->get('planet_maint');
			$system_votes = $blog_settings->get('planet_votes_system');
			$allow_post_comments = $blog_settings->get('allow_post_comments');
			$avatar = $blog_settings->get('planet_avatar');
			$log_level = $blog_settings->get('planet_log');
			$nb_posts_page = $blog_settings->get('planet_nb_post');
			$subscription = $blog_settings->get('planet_subscription');
			$subscription_content = html_entity_decode(stripslashes($blog_settings->get('planet_subscription_content')), ENT_QUOTES, 'UTF-8');
//			$subscription_content = html_encode_code_tags($subscription_content);
			$subscription_content = code_htmlentities($subscription_content, 'code', 'code');
			$ganalytics = $blog_settings->get('planet_ganalytics');
			$planet_analytics = $blog_settings->get('planet_analytics');
			$piwik_url = $blog_settings->get('piwik_url');
			$piwik_id = $blog_settings->get('piwik_id');
			$internal_links = $blog_settings->get('internal_links');
			$allow_uncensored_feed = $blog_settings->get('allow_uncensored_feed');
			$show_similar_posts = $blog_settings->get('show_similar_posts');
			$planet_shaarli = $blog_settings->get('planet_shaarli');

			# statusnet options :
			$statusnet = array(
				'host' => $blog_settings->get('statusnet_host'),
				'username' => stripslashes($blog_settings->get('statusnet_username')),
				'password' => stripslashes($blog_settings->get('statusnet_password')),
				'post_format' => stripslashes($blog_settings->get('statusnet_post_format')),
				'auto_post' => $blog_settings->get('statusnet_auto_post')
				);

			$output = "";
			$output .= '<table id="tbl1" class="table-news">
			<thead>
				<tr>
					<th class="tc5 tcr" scope="col">'.T_('Option').'</th>
					<th class="tc2 tcr" scope="col">'.T_('Value').'</th>
				</tr>
			</thead>
			<tr>
				<td>'.T_('Title of the Planet').'</td>
				<td>'.$title.'</td>
			</tr>
			<tr>
				<td>'.T_('Description of the Planet').'</td>
				<td>'.$desc.'</td>
			</tr>
			<tr>
				<td>'.T_('Graphical theme').'</td>
				<td>'.$theme.'</td>
			</tr>
			<tr>
				<td>'.T_('Language of the Planet').'</td>
				<td>'.$lang.'</td>
			</tr>
			<tr>
				<td>'.T_('Information message (optional)').'</td>
				<td>'.$msg_info.'</td>
			</tr>
			<tr>
				<td>'.T_('Reserved tags').'</td>
				<td>'.$planet_reserved_tags.'</td>
			</tr>
			<tr>
				<td>'.T_('Show the contact page').'</td>';
				if($contact) {
					$output .= '<td>'.T_('Enabled').'</td>';
				}
				else {
					$output .= '<td>'.T_('Disabled').'</td>';
				}
			$output .= '</tr>
			<tr>
				<td>'.T_('Enable voting').'</td>';
				if($votes) {
					$output .= '<td>'.T_('Enabled').'</td>
					</tr>
					<tr>
						<td>'.T_('Votes system').'</td>
						<td>'.$system_votes.'</td>
					</tr>';
				}
				else {
					$output .= '<td>'.T_('Disabled').'</td>
					</tr>';
				}
			$output .= '<tr>
				<td>'.T_('Option of moderation').'</td>';
				if($moderation) {
					$output .= '<td>'.T_('Enabled').'</td>';
				}
				else {
					$output .= '<td>'.T_('Disabled').'</td>';
				}
			$output .= '</tr>
			<tr>
				<td>'.T_('Allow post comments').'</td>';
				if ($allow_post_comments) {
					$output .= '<td>'.T_('Enabled').'</td>';
				}
				else {
					$output .= '<td>'.T_('Disabled').'</td>';
				}
			$output .= '</tr>
			<tr>
				<td>'.T_('Enable avatar').'</td>';
				if ($avatar) {
					$output .= '<td>'.T_('Enabled').'</td>';
				}
				else {
					$output .= '<td>'.T_('Disabled').'</td>';
				}
			$output .= '</tr>
			<tr>
				<td>'.T_('Maintenance mode').'</td>';
				if($maintenance) {
					$output .= '<td>'.T_('Enabled').'</td>';
				}
				else {
					$output .= '<td>'.T_('Disabled').'</td>';
				}
			$output .= '</tr>
			<tr>
				<td>'.T_('Log level').'</td>
				<td>'.$log_level.'</td>
			</tr>
			<tr>
				<td>'.T_('Number of posts by page').'</td>
				<td>'.$nb_posts_page.'</td>
			</tr>
			<tr>
				<td>'.T_('Planet Analytics Engine').'</td>';
				if (empty($planet_analytics)) {
					$output .= '<td>'.T_('Disabled').'</td>';
				}
				else {
					$output .= '<td>'.$planet_analytics.'</td>';
				}
			$output .= '</tr>
			<tr>
				<td>'.T_('Google Analytics id').'</td>';
				if (empty($ganalytics)) {
					$output .= '<td></td>';
				}
				else {
					$output .= '<td>'.$ganalytics.'</td>';
				}
			$output .= '</tr>
			<tr>
				<td>'.T_('Piwik URL (don\'t forget the "/" at the end)').'</td>';
			$output .= '<td>'.$piwik_url.'</td>';
			$output .= '</tr>
			<tr>
				<td>'.T_('Piwik Site ID').'</td>';
			$output .= '<td>'.$piwik_id.'</td>';
			$output .= '</tr>
			<tr>
				<td>'.T_('Enable Internal links').'</td>';
			if($internal_links) {
				$output .= '<td>'.T_('Enabled').'</td>';
			}
			else {
				$output .= '<td>'.T_('Disabled').'</td>';
			}

			$output .= '</tr>
			<tr>
				<td>'.sprintf(T_('Allow using feed with no moderation at <br/>%s'),
					'<a href="'.BP_PLANET_URL.'/feed.php?type=atom&uncensored=true">'.
						BP_PLANET_URL.'/feed.php?type=atom&uncensored=true'.'</a>').'</td>';
			if($allow_uncensored_feed) {
				$output .= '<td>'.T_('Enabled').'</td>';
			}
			else {
				$output .= '<td>'.T_('Disabled').'</td>';
			}

			$output .= '</tr>
			<tr>
				<td>'.T_('Show similar posts at the bottom of each post').'</td>';
			if($show_similar_posts) {
				$output .= '<td>'.T_('Enabled').'</td>';
			}
			else {
				$output .= '<td>'.T_('Disabled').'</td>';
			}

			$output .= '</tr>
			<tr>
				<td>'.T_('Enable shaarli instances on /shaarli').'</td>';
			if($planet_shaarli) {
				$output .= '<td>'.T_('Enabled').'</td>';
			}
			else {
				$output .= '<td>'.T_('Disabled').'</td>';
			}
			$output .= '</tr>';

			############
			# Add post values for statusNet
			############

			# statusnet_host :
			$output .= '<tr><td>'.T_('Planet statusnet host (ex: http://identi.ca)').'</td>';
			$output .= '<td>'.$statusnet['host'].'</td>';
			$output .= '</tr>';

			# statusnet_username :
			$output .= '<tr><td>'.T_('Planet statusnet username').'</td>';
			$output .= '<td>'.$statusnet['username'].'</td>';
			$output .= '</tr>';

			# statusnet_post_format :
			$output .= '<tr><td>'.T_('Statusnet messages format').'</td>';
			$output .= '<td>'.$statusnet['post_format'].'</td>';
			$output .= '</tr>';

			# statusnet_auto_post :
			$output .= '<tr><td>'.T_('Statusnet activate automatic post').'</td>';
			if($statusnet['auto_post']) {
				$output .= '<td>'.T_('Enabled').'</td>';
			} else {
				$output .= '<td>'.T_('Disabled').'</td>';
			}
			$output .= '</tr>';

			$output .= '<tr>
			<td>'.T_('Enable subscription').'</td>';
			if($subscription) {
				$output .= '<td>'.T_('Enabled').'</td>
			<tr>
				<td>'.T_('Subscription page content').'</td>
					<td>
						<div style="display:none;">
							<textarea id="list_subscription_content" name="subscription_content" >'.$subscription_content.'</textarea>
						</div>
						<div class="button br3px">
							<input type="button" class="preview field" name="preview" onclick="javascript:call_preview(\'list_subscription_content\')" value="'.T_('Preview').'" />
						</div>
					</td>
			</tr>';
			}
			else {
				$output .= '<td>'.T_('Disabled').'</td>';
			}
			$output .= '</tr>
			</table>';

		print $output;
		break;
	case "join":
		$url = $blog_settings->get('planet_url');
		$title = $blog_settings->get('planet_title');
		$desc = $blog_settings->get('planet_desc');
		$author = $blog_settings->get('author');
		$mail = $blog_settings->get('author_mail');
		$text = joinBilboplanetCommunity($url,$title,$desc,$author,$mail);
		print $text;
		break;
	}
}
else {
	$output = T_("Nothing to do");
	print '<div class="flash_warning">'.$output.'</div>';
}
?>
