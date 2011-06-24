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

class GenericView extends AbstractView
{

	public function __construct(&$core, $page)
	{
		$this->instantiateTPL();
		$this->core =& $core;
		$this->con = $core->con;
		$this->prefix = $core->prefix;
		$this->page = $page;
	}

	protected function renderContactPage() {
		require_once(dirname(__FILE__).'/../lib/recaptchalib.php');
		$publickey = "6LdEeQgAAAAAACLccbiO8TNaptSmepfMFEDL3hj2";
		$captcha_html = recaptcha_get_html($publickey);

		$form_values = array(
			"name" => "",
			"email" => "",
			"subject" => "",
			"content" => "",
		);

		$this->tpl->setVar('captcha_html', $captcha_html);
		$this->tpl->setVar('form', $form_values);
		$this->tpl->render('content.contact');
	}

	protected function renderSubscribePage() {
		global $blog_settings;
		if(!$blog_settings->get('planet_subscription')) {
			$content = "<img src=\"themes/".$blog_settings->get('planet_theme')."/images/closed.png\" />";
			$this->tpl->setVar('html', $content);
			$this->tpl->render('content.html');
			exit;
		} else {
			$content = $blog_settings->get('planet_subscription_content');
			$content = stripslashes($content);
			$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
			$content = code_htmlentities($content, 'code', 'code', 1);

			require_once(dirname(__FILE__).'/../lib/recaptchalib.php');
			$publickey = "6LdEeQgAAAAAACLccbiO8TNaptSmepfMFEDL3hj2";
			$captcha_html = recaptcha_get_html($publickey);

			$form_values = array(
				"user_id" => "",
				"fullname" => "",
				"email" => "",
				"url" => "",
				"feed" => "",
			);

			$this->tpl->setVar('form', $form_values);
			$this->tpl->setVar('subscription_content', $content);
			$this->tpl->setVar('captcha_html', $captcha_html);
			$this->tpl->render('content.subscription');
		}
	}

	protected function render404Page() {
		$this->tpl->setVar('params', array('title' => '404 Error'));
		$error = array(
			"title" => T_('404 Error'),
			"text" => T_("Page not found")
		);
		$this->tpl->setVar("error", $error);
		$this->tpl->render('content.404');
	}

	protected function renderPage() {
		$this->{'render'.ucfirst($this->page).'Page'}();
	}

	public function render() {
		header('Content-type: text/html; charset=utf-8');
		$this->renderGlobals();
		$this->renderPage();
		echo $this->tpl->render();
	}
}
?>

