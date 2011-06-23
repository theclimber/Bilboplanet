<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
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

abstract class AbstractView
{
	protected $tpl;
	protected $prefix;
	protected $con;
	protected $core;
	protected $styles = array();
	protected $scripts = array();
	protected $search;
	protected $page;

	public function addStylesheet($css) {
		$this->styles[] = $css;
	}

	public function addJavascript($js) {
		$this->scripts[] = $js;
	}

	#######################
	# RENDER SIDEBAR
	#######################
	protected function renderSidebar() {
		global $blog_settings;
		if ($blog_settings->get('planet_msg_info')) {
			$this->tpl->render('sidebar.alert');
		}
		if($blog_settings->get('planet_vote')) {
			$this->tpl->render('sidebar.popular');
		}

		$sql_side = "SELECT
			user_fullname as fullname,
			".$this->prefix."user.user_id as id,
			".$this->prefix."site.site_url as site_url,
			".$this->prefix."site.site_name as site_name
			FROM ".$this->prefix."user, ".$this->prefix."site
			WHERE ".$this->prefix."user.user_id = ".$this->prefix."site.user_id
			AND user_status = '1'
			ORDER BY lower(user_fullname)";
		$rs_side = $this->con->select($sql_side);

		while ($rs_side->fetch()) {
			$user_info = array(
				"id" => urlencode($rs_side->f('id')),
				"fullname" => $rs_side->f('fullname'),
				"site_url" => $rs_side->f('site_url')
				);
			$this->tpl->setVar("user", $user_info);
			$this->tpl->render("sidebar.users.list");
		}
	}

	#######################
	# RENDER MENU
	#######################
	protected function renderMenu() {
		global $blog_settings;
		if ($blog_settings->get('planet_vote')) {
			$this->tpl->render('menu.votes');
		}
		if ($blog_settings->get('planet_contact_page')) {
			$this->tpl->render('menu.contact');
		}
		if ($blog_settings->get('planet_subscription')) {
			$this->tpl->render('menu.subscription');
		}

		if ($this->core->auth->sessionExists() ) {
			$login = array(
			'username' => $this->core->auth->userID()
				);
			$this->tpl->setVar('login', $login);
			if ($this->core->hasRole('manager')) {
				$this->tpl->render('page.loginadmin');
			}
			$this->tpl->render('page.loginbox');
		}
	}

	######################
	# RENDER STYLES
	######################
	protected function renderStyles() {
		foreach ($this->styles as $css) {
			$this->tpl->setVar('css_file', $css);
			$this->tpl->render('css.import');
		}
	}

	######################
	# RENDER JAVASCRIPT
	######################
	protected function renderJavascript() {
		global $blog_settings;
		$page_js = dirname(__FILE__).'/themes/'.$blog_settings->get('planet_theme').'/js/'.$this->page.'.js';
		if (file_exists($page_js)) {
			$this->addJavascript('themes/'.$blog_settings->get('planet_theme').'/js/'.$this->page.'.js');
		}
		foreach ($this->scripts as $js) {
			$this->tpl->setVar('js_file', $js);
			$this->tpl->render('js.import');
		}
	}

	#####################
	# RENDER FOOTER
	#####################
	protected function renderFooter() {
		global $blog_settings;
		$this->tpl->setVar('footer1', array(
			'text' => T_('Powered by Bilboplanet'),
			'url' => 'http://www.bilboplanet.com'));
		$this->tpl->setVar('footer2', array(
			'text' => T_('Valid CSS - Xhtml'),
			'url' => 'http://validator.w3.org/check?verbose=1&uri='.$blog_settings->get('planet_url')));
		$this->tpl->setVar('footer3', array(
			'text' => T_('Designed by BilboPlanet'),
			'url' => 'http://www.bilboplanet.com'));
	}

	#####################
	# RENDER WIDGETS
	#####################
	//TODO : it would be more reliable if we put in the settings table the widgets to load and where
	// if we put a json dict in the database with for each element in the list:
	// * the place where we want to place the widget (sidebar, footer)
	// * the file to load
	// * the order in which is have to load
	// After that we need to check, before loading the tpl, if the widget function is returning a well formatted array
	// * if yes => show it
	// * if not => show an error in the widget place
	protected function renderWidgets() {
		global $blog_settings;
		$widget_path = dirname(__FILE__).'/widgets';
		$widget_files = array();
		if ($blog_settings->get('planet_widget_files')) {
			$widget_files = json_decode($blog_settings->get('planet_widget_files'));
		}
		foreach ($widget_files as $file){
			if (is_dir($widget_path) && is_file($widget_path.'/'.$file->{'name'})) {
				# Build an array of available widgets
				require_once($widget_path.'/'.$file->{'name'});
				$wgt = getWidget();
				foreach ($wgt['styles'] as $sty) {
					$styles[] = $sty;
				}
				foreach ($wgt['scripts'] as $spt) {
					$scripts[] = $spt;
				}
				if ($file->{'position'} == "sidebar") {
					$this->tpl->setVar("sidebar-widget", array(
						'title' => $wgt['title'],
						'html' => $wgt['html'],
						'id' => $wgt['id']));
					$this->tpl->render('sidebar.widget');
				}
				if ($file->{"position"} == "footer") {
					$this->tpl->setVar("footer-widget", array(
						'title' => $wgt['title'],
						'html' => $wgt['html'],
						'id' => $wgt['id']));
					$this->tpl->render('footer.widget');
				}
			}
		}
	}

	protected function renderSinglePost($post, $isAlone = true) {
		global $blog_settings;
		$this->tpl->setVar('post', $post->getPostArray());

		# Gravatar
		if($blog_settings->get('planet_avatar')) {
			$gravatar_email = strtolower($post->getAuthor()->getEmail());
			$this->tpl->setVar('gravatar_url', "http://www.gravatar.com/avatar.php?gravatar_id=".
				md5($gravatar_email)."&default=".
				urlencode($blog_settings->get('planet_url').
				"/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png"));
			$this->tpl->render('post.block.gravatar');
		}

		if ($blog_settings->get('planet_vote')) {
		$votes = array("html" => $this->afficheVotes($post->getScore(), $post->getId()));
			$this->tpl->setVar('votes', $votes);
			$this->tpl->render('post.block.votes');
		}
		foreach ($post->getTags() as $tag) {
			$this->tpl->setVar('post_tag', $tag);
			$this->tpl->render('post.tags');
		}
		if ($blog_settings->get('allow_post_modification')) {
			if($blog_settings->get('allow_tagging_everything')) {
				$this->tpl->render('post.action.tags');
			} else {
				if($this->core->auth->userID() == $post->getAuthor()->getId()) {
					$this->tpl->render('post.action.tags');
				}
			}
		}
		if ($blog_settings->get('allow_post_comments')) {
		if($this->core->auth->userID() == $post->getAuthor()->getId() || $this->core->hasRole('manager')) {
			if ($post->allowComments()) {
					$this->tpl->render('post.action.uncomment');
				} else {
					$this->tpl->render('post.action.comment');
				}
			}
		}
		if ($blog_settings->get('allow_post_comments') && $post->allowComments()) {
			$sql = "SELECT * FROM ".$this->prefix."comment
				WHERE post_id=".$id;
			$rs_comment = $this->con->select($sql);
			while ($rs_comment->fetch()) {
				$fullname = $rs_comment->user_fullname;
				if (!empty($rs_comment->user_site)) {
					$fullname = '<a href="'.$rs_comment->user_site.'">'.$fullname.'</a>';
				}
				$content = $this->core->wikiTransform($rs_comment->content);
				$comment = array(
					"id" => $rs_comment->comment_id,
					"post_id" => $rs_comment->post_id,
					"user_fullname_link" => $fullname,
					"user_fullname" => $rs_comment->user_fullname,
					"user_site" => $rs_comment->user_site,
					"content" => $content,
					"pubdate" => mysqldatetime_to_date("d/m/Y",$rs_comment->created)
					);
				$this->tpl->setVar("comment", $comment);
				$this->tpl->render('post.comment.element');
			}
			$this->tpl->render('post.comment.block');
		}
		if (!$isAlone) {
			$this->tpl->render('post.backsummary');
		}
		$this->tpl->render('post.block');
	}

	private function afficheVotes($nb_votes, $num_article) {
		global $blog_settings;
		# On met un s a vote si il le faut
		$vote = "vote";
		if($nb_votes > 1) $vote = "votes";

		# Score du vote en fonction du system
		$score = $nb_votes;
		if($blog_settings->get('planet_votes_system') != "yes-no" && $score < 0)
			$score = 0;

		# Bouton de vote
		$text =  '';
		if (checkVote($this->con, getIP(), $num_article)) {

			# Si le visiteur a deja vote
			$text .= '<span id="vote'.$num_article.'" class="avote">'.$score.' '.$vote.'.
					<span id="imgoui" title="'.T_('Vote yes').'"></span>
					<span id="imgnon" title="'.T_('Vote no').'"></span>';
			$text .= '</span>';

		} else {

			# Si il n'a jamais vote, on construit le token
			$ip = getIP();
			$token = md5($ip.$num_article);
			# On affiche le bouton de vote
			$text .= '<span id="vote'.$num_article.'" class="vote">'.$score.' '.$vote.'
					<a href="#blackhole" title="'.T_('This post seems pertinent to you').'" id="aoui'.$num_article.'"
					onclick="javascript:vote('."'$num_article','$token', 'positif'".');" >
					<span id="imgoui" title="'.T_('Vote yes').'"></span></a>';

			# En fonciton du systeme de vote
			if($blog_settings->get('planet_votes_system') == "yes-no") {
				$text .= '<a href="#blackhole" title="'.T_('This post seems not pertinent to you').'" id="anon'.$num_article.'"
					onclick="javascript:vote('."'$num_article','$token', 'negatif'".');" >
					<span id="imgnon" title="'.T_('Vote no').'"></span></a>';
			} else {
				$text .= '<a href="#blackhole" title="'.T_('This post should not be here').'" id="anon'.$num_article.'"
					onclick="if(confirm(\''.T_('Are you sure this post should not be on this planet and should be removed?').'\')) '."{ vote('$num_article','$token', 'negatif');}".' " >
					<span id="imgnon" title="'.T_('Vote no').'"></span></a>';
			}
			$text .= "</span>";
		}
		return $text;
	}

	protected function renderGlobals() {
		$this->renderSidebar();
		$this->renderMenu();
		$this->renderStyles();
		$this->renderJavascript();
		$this->renderFooter();
		$this->renderWidgets();
	}

	abstract public function render();
}
?>
