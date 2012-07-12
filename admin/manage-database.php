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
?>
<?php
require_once(dirname(__FILE__).'/../inc/admin/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager') | !$core->hasPermission('configuration')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}

	$allowed_versions = array("1.1" => "1.1", "1.0" => "1.0", "0.3.2" => "0.3.2");

$possible_tables = array();
$possible_tables["1.1"] = array("user", "site", "feed", "post", "permissions", "setting", "votes", "comment", "feed_tag", "pending_user", "post_tag", "tribe");
$possible_tables["1.0"] = array("user", "site", "feed", "post", "permissions", "setting", "votes");
$possible_tables["0.3.2"] = array("article", "flux", "membre", "votes");

if (isset($_POST)) {
##########################################################
# IMPORT DATABASE VERSION
##########################################################
	if(isset($_POST['importform'])){
		$flash = array();
		$imported_version = (string) trim($_POST['import_version']);
		# On recupere les infos
		if(!is_uploaded_file($_FILES['imported_file']['tmp_name']) ){
			$flash['error'][] = T_('Your file could not be downloaded');
		}
		elseif ($_FILES["imported_file"]["error"] > 0){
			$flash['error'][] = T_('Your file could not be imported').'<br />'.$_FILES["imported_file"]["error"];
		}
/*strcmp($_FILES["imported_file"]["type"],"application/x-gzip")!=0 ||
			$_FILES["imported_file"]["type"] != 'application/gzip' ||
			$_FILES["imported_file"]["type"] != 'application/gzipped' ||
			$_FILES["imported_file"]["type"] != 'application/gzip-compressed'*/
		elseif(strpos($_FILES["imported_file"]["type"],"gzip") === false){
				$flash['error'][] = T_("Your file doesn't have the right format : ").'<br />'.$_FILES["imported_file"]["type"];
		}
		else {
			$response = T_('Congratulations! You imported an old data configuration successfully')."<br/>";
			$response .= T_("File name: ") . $_FILES["imported_file"]["name"] . "<br />";
			$response .= T_("Size: ") . ($_FILES["imported_file"]["size"] / 1024)." ".T_("Kb")."<br />";
			$flash['notice'][] = $response;
			$gzfile = file_get_contents($_FILES['imported_file']['tmp_name'], FILE_USE_INCLUDE_PATH);
			$content = my_gzdecode($gzfile);
			$tables = json_decode($content, true);
			$author_id = $blog_settings->get('author_id');

			foreach ($tables as $table){
				if (($imported_version == '1.1' && in_array($table['name'], $possible_tables["1.1"])) ||
					($imported_version == '1.0' && in_array($table['name'], $possible_tables["1.0"]))) {
						// on vide le contenu de la table
						if ($table['name'] == 'user') {
							$core->con->execute("DELETE FROM ".$core->prefix."user WHERE user_id != '".$author_id."'");
						} elseif ($table['name'] == "permissions") {
							$core->con->execute("DELETE FROM ".$core->prefix."permissions WHERE user_id != '".$author_id."'");
						} elseif ($table['name'] == "settings") {
							$core->con->execute("DELETE FROM ".$core->prefix."setting WHERE
								setting_value != 'author_id' AND
								setting_value != 'planet_url' AND
								setting_value != 'planet_version'"
								);
						} else {
							$core->con->execute("TRUNCATE TABLE `".$core->prefix.$table['name']."`");
						}
						$cols = $table['head'];

						if ($table['name'] == "post" && $imported_version == '1.0') {
							# Suppression de la colonne feed_id
							$cols_id = array_search('feed_id', $cols);
							if ($cols_id) {
								unset($cols[$cols_id]);
							}
						} else {
							$cols_id = false;
						}

						foreach($table['content'] as $key => $value){

							$cols_to_remove = array();
							$values_to_add = array();

							if ($cols_id) {
								# il y a une colonne de moins dans les valeurs
								$cols_to_remove[] = $cols_id-1;
							}

							if ($table['name'] == 'user' && $key == $author_id) {
								# Empecher d'overrider l'utilisateur principal
								continue;
							}
							if ($table['name'] == 'permissions' && $key == $author_id) {
								continue;
							}
							if ($table['name'] == 'setting') {
								# pour les settings on ne modifie pas tout
								if (in_array($key, array('author_id','planet_url', 'planet_version'))) {
									continue;
								}
							}
							if ($table['name'] == 'votes') {
								$value[0] = $author_id;
							}
							if ($table['name'] == 'post_tag') {
								# Ajouter un utilisateur aux tags
								if (!array_search('user_id', $cols)) {
									$cols[] = 'user_id';
								}
								$values_to_add[] = $author_id;
							}

							# Creation de la liste de valeurs
							$values = "'".$key."'";
							$n = 0;
							while ($n < count($value)){
								# On ajoute la valeur si elle n'est pas dans $cols_to_remove
								if (!in_array($n, $cols_to_remove)){
									if ($table['name'] == 'post') {
										$values .= ",'".$core->con->escape($value[$n])."'";
									} else {
										$values .= ",'".$value[$n]."'";
									}
								}
								$n+=1;
							}

							# On rajoute eventuellement des valeurs
							foreach($values_to_add as $val) {
								$values .= ",'".$val."'";
							}

							# Creation de la liste des colonnes
							$cols_list = '('.getListFromArray($cols).')';

							# Creation de la requeste
							$sql = "INSERT INTO ".$core->prefix.$table['name']." ".$cols_list." VALUES (".$values.")";
							try {
								$core->con->execute($sql);
							} catch (Exception $e) {
								print $sql;
								print "<p><b>$e</b></p>";
								exit;
							}
						}
				} elseif ($imported_version == '0.3.2') {
					# FIXME test this !!
					# Poeple should not use this version normally !!!
					switch($table['name']) {
					case "membre":
						$author_id = $blog_settings->get('author_id');
						$core->con->execute("DELETE FROM ".$core->prefix."user WHERE user_id != '".$author_id."'");
						foreach($table['content'] as $key => $value){
							$nom_membre = $value[0];
							$email_membre = $value[1];
							$site_membre = $value[2];
							$statut_membre = $value[3];
							$user_id = preg_replace("( )", "_", $nom_membre);
							$user_id = cleanString($user_id);

							$rs0 = $core->con->select(
								'SELECT COUNT(1) '.
								'FROM '.$core->prefix.'user WHERE user_id =\''.$user_id.'\'');
							if ($rs0->f('nb') > 0) {
								$flash['error'][] = T_("Two users have the same name, impossible to import. Please try again. Username : ".$user_id);
								break;
							}

							$cur = $core->con->openCursor($core->prefix."user");
							$cur->user_id = $user_id;
							$cur->user_fullname = $nom_membre;
							$cur->user_email = $email_membre;
							$cur->user_status = $statut_membre;
							$cur->user_lang = $blog_settings->get('planet_lang');
							$cur->created = array('NOW()');
							$cur->modified = array('NOW()');

							if($user_id == $author_id) {
								$cur->update("WHERE user_id == '".$author_id."'");
							} else {
								$cur->user_pwd = crypt::hmac($user_id,$email_membre);
								$cur->insert();
							}

							$rs3 = $core->con->select(
								'SELECT MAX(site_id) '.
								'FROM '.$core->prefix.'site '
								);
							$next_site_id = (integer) $rs3->f(0) + 1;
							$cur = $core->con->openCursor($core->prefix.'site');
							$cur->site_id = $next_site_id;
							$cur->user_id = $user_id;
							$cur->site_name = '';
							$cur->site_url = $site_membre;
							$cur->site_status = 1;
							$cur->created = array(' NOW() ');
							$cur->modified = array(' NOW() ');
							$cur->insert();
						}
						break;
					case "flux":
						if (empty($tables['membre']['content'])) {
							$flash['error'][] = T_("You can not import 'flux' table without importing the 'membres' table");
							break;
						}
						$core->con->execute("TRUNCATE TABLE `".$core->prefix."feed`");
						foreach($table['content'] as $key => $value){
							$url_flux = $value[0];
							$num_membre = $value[1];
							if ($table["head"][4] == "last_updated") {
								$last_updated = timestamp_to_mysqldatetime($value[3]);
								$status_flux = $value[4];
							} else {
								$last_updated = timestamp_to_mysqldatetime($value[2]);
								$status_flux = $value[3];
							}
							$user_id = $tables['membre']['content'][$num_membre][0];
							$user_id = preg_replace("( )", "_", $user_id);
							$user_id = cleanString($user_id);
							# We build the url of flux
							$parse = @parse_url($url_flux);
							if (!$parse['scheme']){
								$site_membre = $tables['membre']['content'][$num_membre][2];
								$url_flux = $site_membre.$url_flux;
							}

							$sql = "SELECT site_id FROM ".$core->prefix."site WHERE user_id = '".$user_id."'";
							$rs = $core->con->select($sql);
							$site_id = $rs->f('site_id');

							if (!empty($site_id)) {
								$rs3 = $core->con->select(
									'SELECT MAX(feed_id) '.
									'FROM '.$core->prefix.'feed '
									);
								$next_feed_id = (integer) $rs3->f(0) + 1;
								$cur = $core->con->openCursor($core->prefix."feed");
								$cur->feed_id = $next_feed_id;
								$cur->user_id = $user_id;
								$cur->site_id = $site_id;
								$cur->feed_url = $url_flux;
								$cur->feed_checked = $last_updated;
								$cur->feed_status = $status_flux;
								$cur->feed_trust = 1;
								$cur->created = array(' NOW() ');
								$cur->modified = array(' NOW() ');
								$cur->insert();
							} else {
								$flash['warning'][] = T_("site_id should not be null for user ".$user_id );
							}
						}
						break;
					case "article":
						if (empty($tables['membre']['content'])) {
							$flash['error'][] = T_("You can not import 'articles' table without importing the 'membres' table");
							break;
						}
						if (empty($tables['flux']['content'])) {
							$flash['error'][] = T_("You can not import 'articles' table without importing the 'flux' table");
							break;
						}
						$core->con->execute("TRUNCATE TABLE `".$core->prefix."post`");
						foreach($table['content'] as $key => $value){
							$num_membre = $value[0];
							$article_pub = timestamp_to_mysqldatetime($value[1]);
							$article_titre = $value[2];
							$article_url = $value[3];
							$article_content = $value[4];
							$article_statut = $value[5];
							$article_score = $value[6];
							$user_id = $tables['membre']['content'][$num_membre][0];
							$user_id = preg_replace("( )", "_", $user_id);
							$user_id = cleanString($user_id);
							# We build the url of article
							$parse = @parse_url($article_url);
							if (!$parse['scheme']){
								$site_membre = $tables['membre']['content'][$num_membre][2];
								$article_url = $site_membre.$article_url;
							}


							$rs3 = $core->con->select(
								'SELECT MAX(post_id) '.
								'FROM '.$core->prefix.'post '
								);
							$next_post_id = (integer) $rs3->f(0) + 1;
							$cur = $core->con->openCursor($core->prefix."post");
							$cur->post_id = $next_post_id;
							$cur->user_id = $user_id;
							$cur->post_pubdate = $article_pub;
							$cur->post_permalink = $article_url;
							$cur->post_title = $article_titre;
							$cur->post_content = $article_content;
							$cur->post_status = $article_statut;
							$cur->post_score = $article_score;
							$cur->created = array(' NOW() ');
							$cur->modified = array(' NOW() ');
							$cur->insert();
						}
						break;
					case "votes":
						/* We don't recover the votes */
						break;
					case "config":
						$config = $tables['config'];
						$blog_settings->put('planet_desc', $config['BP_DESC'], "string");
						$blog_settings->put('planet_title', $config['BP_TITLE'], "string");
						$blog_settings->put('author', $config['BP_AUTHOR'], "string");
						$blog_settings->put('author_mail', $config['BP_AUTHOR_MAIL'], "string");
						$blog_settings->put('author_site', $config['BP_AUTHOR_SITE'], "string");
						$blog_settings->put('planet_nb_post', $config['BP_NB_ART'], "integer");
						$blog_settings->put('planet_nb_art_mob', $config['BP_NB_ART_MOB'], "integer");
						$blog_settings->put('planet_msg_info', $config['BP_MSG_INFO'], "string");
						$blog_settings->put('planet_meta', $config['BP_META'], "string");
						$blog_settings->put('planet_keywords', $config['BP_KEYWORD'], "string");
						$blog_settings->put('planet_maint', $config['BP_MAINTENANCE'], "boolean");
						$blog_settings->put('planet_vote', $config['BP_VOTES'], "boolean");
						$blog_settings->put('planet_contact_page', $config['BP_CONTACT_PAGE'], "boolean");
						$blog_settings->put('auther_jabber', $config['BP_AUTHOR_JABBER'], "string");
						$blog_settings->put('auther_im', $config['BP_AUTHOR_IM'], "string");
						$blog_settings->put('auther_about', $config['BP_AUTHOR_ABOUT'], "string");
						$blog_settings->put('planet_index_update', $config['BP_INDEX_UPDATE'], "boolean");
						break;
					}
				}
				else {
					$flash['error'][] = T_('Forbidden');
				}
			}
		}
	}
}


include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>
<script type="text/javascript" src="meta/js/manage-database.js"></script>
<div id="BP_page" class="page">
	<div class="inpage">

<?php
if (!empty($flash)) {
	$msg = '<ul>';
	if(count(array_keys($flash['error'])) > 0) {
		$msg_type = 'error';
	}
	elseif (count(array_keys($flash['warning'])) > 0 ) {
		$msg_type = 'warning';
		$msg .= T_('Import successfully with warning :').'<br />';
	}
	else {
		$msg_type = 'notice';
	}
	foreach(array_keys($flash[$msg_type]) as $key_msg) {
		$msg .= '<li>'.$flash[$msg_type][$key_msg].'</li>';
	}
	$msg .= '</ul>';
	echo '<div id="post_flash" class="flash_'.$msg_type.'" style="display:none;">'.$msg.'</div>';
}
?>
<fieldset><legend><?php echo T_('Export planet configuration');?></legend>
<br />

<div class="message"><?php echo T_('Please select the data you want to export'); ?></div>
<p><?php echo T_('This will export the data to a file that you\'ll be able to import later on another installation'); ?></p>
<br/>

<div id="export-log" style="display:none;">
	<h3><?php echo T_('Exported data');?></h3>
	<div id="export_res"><!-- spanner --></div>
</div>

<form id="exportform">
<ul>
	<li>
		<label for="user_table">
		<input id="user_table" type="checkbox" class="input" name="list[]" value="user" />
		<?php echo T_('User table'); ?></label>
	</li>
	<li>
		<label for="site_table">
		<input id="site_table" type="checkbox" class="input" name="list[]" value="site" />
		<?php echo T_('Site table'); ?></label>
	</li>
	<li>
		<label for="feed_table">
		<input id="feed_table" type="checkbox" class="input" name="list[]" value="feed" />
		<?php echo T_('Feed table'); ?></label>
	</li>
	<li>
		<label for="post">
		<input id="post_table" type="checkbox" class="input" name="list[]" value="post" />
		<?php echo T_('Post table'); ?></label>
	</li>
	<li>
		<label for="votes_table">
		<input id="votes_table" type="checkbox" class="input" name="list[]" value="votes" />
		<?php echo T_('Votes table'); ?></label>
	</li>
	<li>
		<label for="perm_table">
		<input id="perm_table" type="checkbox" class="input" name="list[]" value="permissions" />
		<?php echo T_('Permission table'); ?></label>
	</li>
	<li>
		<label for="setting_table">
		<input id="setting_table" type="checkbox" class="input" name="list[]" value="setting" />
		<?php echo T_('Setting table'); ?></label>
	</li>
</ul>
<br/>
<div class="button br3px"><input type="submit" class="export" name="exportform" value="<?php echo T_("Export"); ?>"/></div>
</form>

</p>
<br />
</fieldset>
<fieldset><legend><?php echo T_('Import planet configuration');?></legend>
<br />
<div class="message">
<p><?php echo T_('Please select the file you want to restore.');?>
<br /><b><font color=red><?php echo T_('TAKE CARE !');?></font></b> <?=T_('If you apply the content of one of this file, this action can not be cancelled');?><br/>
<?php echo T_("This action can take several minutes. Be patient and don't cancel the process during execution"); ?></p>
</div>
<br />

<div id="import-log" style="display:none;">
	<h3><?php echo T_('Imported data');?></h3>
	<div id="import_res"><!-- spanner --></div>
</div>

<form id="importform" enctype="multipart/form-data" method="post">
<p>
<label class="required" for="file">
<?php echo T_('Select your backup file'); ?> : <input type="file" class="input import_style" name="imported_file" id="imported_file" /></label><br />
<?php
echo '<label class="required" for="import_version">'.T_('Specify the original version of your file').' : '.
form::combo('import_version',$allowed_versions,'', 'input').'</label><br />';
?>
</p>
<br/>
<div class="button br3px"><input  class="import" type="submit" name="importform" value="<?php echo T_("Import"); ?>"/></div>
</form>
<br />
<p><i>
<?php echo T_('NOTE : to import a backup file, the file needs to have the *.json.gz extension !');?>
</i></p><br></fieldset>

<script type="text/javascript" src="meta/js/jquery.ajaxfileupload.js"></script>
<script type="text/javascript" src="meta/js/import-export.js"></script>

<?php
include(dirname(__FILE__).'/footer.php');
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
