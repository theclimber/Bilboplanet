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
include_once (dirname(__FILE__).'/class.bp.abstractview.php');

class TribeView extends AbstractView
{
	protected $tribe;
	protected $page = 0;
	protected $period = null;
	protected $nbitems = 10;
	protected $popular = false;

	public function __construct(&$core)
	{
		global $blog_settings;
		$this->tribe = $core->tribes;
		$this->prefix = $core->prefix;
		$this->con = $core->con;

		$this->core =& $core;
//		$this->theme =& $theme;

		# Create the Hyla_Tpl object
		$this->tpl = new Hyla_Tpl();
		$this->tpl->setL10nCallback('T_');
		$this->tpl->importFile('index','index.tpl', dirname(__FILE__).'/../../themes/'.$blog_settings->get('planet_theme'));
		$this->tpl->setVar('planet', array(
			"url"	=>	$blog_settings->get('planet_url'),
			"theme"	=>	$blog_settings->get('planet_theme'),
			"title"	=>	$blog_settings->get('planet_title'),
			"desc"	=>	$blog_settings->get('planet_desc'),
			"keywords"	=>	$blog_settings->get('planet_keywords'),
			"desc_meta"	=>	$blog_settings->get('planet_desc_meta'),
			"msg_info" => $blog_settings->get('planet_msg_info')
		));
	}


	public function setPage($page) {
		$this->page = $page;
	}

	public function setPeriod($period) {
		$this->period = $period;
	}

	public function setNbItems($nb) {
		$this->nbitems = $nb;
	}

	public function setPopular() {
		$this->popular = true;
	}

	#######################
	# RENDER FILTER MENU
	#######################
	protected function renderPeriodFilter() {
		$this->tpl->render('menu.filter');
	}

	#######################
	# RENDER PAGINATION
	#######################
	protected function renderNavigation($count) {
		if($this->page == 0 && $count >= $this->nbitems) {
			# if we are on the first page
			$this->tpl->render('pagination.up.next');
			$this->tpl->render('pagination.low.next');
		} elseif($this->page == 0 && $count< $this->nbitems) {
			# we don't show any button
		} else {
			if($count == 0 || $count < $this->nbitems) {
				# if we are on the last page
				$this->tpl->render('pagination.up.prev');
				$this->tpl->render('pagination.low.prev');
			} else {
				$this->tpl->render('pagination.up.prev');
				$this->tpl->render('pagination.up.next');
				$this->tpl->render('pagination.low.prev');
				$this->tpl->render('pagination.low.next');
			}
		}
	}

	protected function renderTribe() {
		$posts = $this->tribe->getCurrentTribePosts(
			$this->nbitems,
			$this->page * $this->nbitems,
			$this->period,
			$this->popular
			);
		$this->tpl = $this->showPosts($posts, $this->tpl, $this->popular);
		return count($posts);
	}

	private function showPosts($post_list, $tpl, $strip_tags=false) {
		global $blog_settings;
		$gravatar = $blog_settings->get('planet_avatar');

		foreach ($post_list as $id=>$post){

			$post->setSearchWith($this->tribe->getCurrentSearchWith());
			if($strip_tags) {
				$post->setStripTags();
			}

			$post_array = array(
				"id"				=> $id,
				"date"				=> $post->getPubdate(),
				"day"				=> $post->getPubdateDay(),
				"month"				=> $post->getPubdateMonth(),
				"year"				=> $post->getPubdateYear(),
				"hour"				=> $post->getPubdateHour(),
				"permalink"			=> $post->getPermalink(),
				"title"				=> $post->getTitle(),
				"content"			=> $post->getContent(),
				"author_id"			=> $post->getAuthor()->getId(),
				"author_fullname"	=> $post->getAuthor()->getFullname(),
				"author_email"		=> $post->getAuthor()->getEmail(),
				"author_votes"		=> $post->getAuthor()->getUserVotes(),
				"author_posts"		=> $post->getAuthor()->getNbPosts(),
				"nbview"			=> $post->getNbViews(),
				"last_viewed"		=> $post->getLatestViewedFormat('d/m/Y H:i')
				);

			$tpl->setVar('post', $post_array);
			# Gravatar
			if($gravatar) {
				$gravatar_email = strtolower($post->getAuthor()->getEmail());
				$tpl->setVar('gravatar_url', "http://www.gravatar.com/avatar.php?gravatar_id=".
					md5($gravatar_email)."&default=".
					urlencode($blog_settings->get('planet_url').
					"/themes/".$blog_settings->get('planet_theme')."/images/gravatar.png"));
				$tpl->render('post.block.gravatar');
			}
			if ($blog_settings->get('planet_vote')) {
				$votes = array("html" => $this->afficheVotes($post->getScore(), $id));
				$tpl->setVar('votes', $votes);
				$tpl->render('post.block.votes');
			}
			foreach ($post->getTags() as $tag) {
				$tpl->setVar('post_tag', $tag);
				$tpl->render('post.tags');
			}
			if ($blog_settings->get('allow_post_modification')) {
				if($blog_settings->get('allow_tagging_everything')) {
					$tpl->render('post.action.tags');
				} else {
					if($this->core->auth->userID() == $post->getAuthor()->getId()) {
						$tpl->render('post.action.tags');
					}
				}
			}
			if ($blog_settings->get('allow_post_comments')) {
			if($this->core->auth->userID() == $post->getAuthor()->getId() || $this->core->hasRole('manager')) {
				if ($post->allowComments()) {
						$tpl->render('post.action.uncomment');
					} else {
						$tpl->render('post.action.comment');
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
					$tpl->setVar("comment", $comment);
					$tpl->render('post.comment.element');
				}
				$tpl->render('post.comment.block');
			}
			if (count($post_list)>1) {
				$tpl->render('post.backsummary');
			}
			$tpl->render('post.block');


			# Render summary
			$line = array(
				"date" => $post->getPubdate(),
				"title" => $post->getTitle(),
				"short_title" => $post->getShortTitle(),
				"url" => "#post".$id);
			$tpl->setVar('summary', $line);
			$tpl->render('summary.line');
		}
		$this->tpl->render('summary.block');
		return $tpl;
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

	protected function renderSearchBox() {
		$with = $this->tribe->getCurrentSearchWith();
		$without = $this->tribe->getCurrentSearchWithout();
		if (count($with) > 0 || count($without) > 0) {
			$s_with = $with[0];
			$this->tpl->setVar('search_value',$s_with);
			$this->tpl->render('search.box');
			$this->tpl->render('search.line');
		} else {
			$this->tpl->render('search.box');
		}
	}

	public function render() {
		header('Content-type: text/html; charset=utf-8');
		$this->renderGlobals();
		$nbitems = $this->renderTribe();
		$this->renderNavigation($nbitems);
		$this->renderPeriodFilter();
		$this->renderSearchBox();
		$this->tpl->render("content.posts");
		echo $this->tpl->render();
	}
}
?>
