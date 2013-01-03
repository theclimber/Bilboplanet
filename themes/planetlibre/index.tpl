<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta charset="utf-8" />
<meta name="description" content="{$planet.desc}" />
<title>{$params.title}</title>

<link rel="canonical" href="{$planet.url}" />
<!-- CSS -->
<link rel="stylesheet" type="text/css" title="default"
	href="{$planet.url}/themes/{$planet.theme}/css/style.css" />
<link rel="stylesheet" type="text/css"
	href="{$planet.url}/themes/{$planet.theme}/js/fancy/jquery.fancybox-1.3.1.css" />
<!-- BEGIN css.import -->
	<link rel="stylesheet" type="text/css" href="{$planet.url}/{$css_file}"/>
<!-- END css.import -->

<!-- OTHERS -->
<link rel="alternate" type="application/rss+xml"  title="RSS"  href="{$planet.url}/feed.php?type=rss" />
<link rel="alternate" type="application/atom+xml" title="ATOM" href="{$planet.url}/feed.php?type=atom" />
<link rel="alternate" type="application/rss+xml"  title="Popular RSS"  href="{$planet.url}/feed.php?type=rss&popular=true&filter={$params.filter}" />
<!-- BEGIN feed.tags -->
<link rel="alternate" type="application/rss+xml"  title="RSS with filter"  href="{$planet.url}/feed.php?type=rss&tags={$params.tags}&users={$params.users}" />
<link rel="alternate" type="application/atom+xml" title="ATOM with filter" href="{$planet.url}/feed.php?type=atomi&tags={$params.tags}&users={$params.users}" />
<!-- END feed.tags -->
<link rel="alternate" type="application/atom+xml" title="Uncensored feed" href="{$planet.url}/feed.php?type=atom&uncensored=true" />

<link rel="icon" type="image/png" href="{$planet.url}/themes/{$planet.theme}/favicon.png" />

<!-- JS -->
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery.boxy.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery.ba-outside-events.min.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/planet_fct.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/votes.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/fancy/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/fancy/jquery.easing-1.3.pack.js"></script>

<!-- ADD JAVASCRIPT IMPORT HERE -->
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->

</head>
<body>

	<div id="header-bg">
		<div id="site-nav">
			<ul class="menu">
				<li class="menu"><a href="http://blog.planet-libre.org/">Blog</a>
					<ul class="submenu">
						<li><a href="http://blog.planet-libre.org/a-propos/lequipe/">L'&eacute;quipe du Planet</a></li>
						<li><a href="http://blog.planet-libre.org/a-propos/structure-de-la-communaute/">La Communauté</a></li>
						<li><a href="http://blog.planet-libre.org/promotion/">Faire notre promotion</a></li>
						<li><a href="http://blog.planet-libre.org/a-propos/remerciements/">Remerciements</a></li>
					</ul>
				</li>
				<li class="menu"><a href="http://blog.planet-libre.org/a-propos/">&Agrave; propos</a></li>
				<li class="menu"><a href="http://www.planet-libre.org/contact.php">Nous contacter</a></li>
				<li class="menu"><a href="http://liens.planet-libre.org">Brèves</a>
				<li class="menu"><a href="http://www.planet-libre.org/charter.php">Charte</a></li>
				<li class="menu"><a href="http://blog.planet-libre.org/f-a-q/">F.A.Q.</a>
					<ul class="submenu">
						<li><a href="http://blog.planet-libre.org/f-a-q/conseil-decriture/">Conseils d'écriture</a></li>
						<li><a href="http://blog.planet-libre.org/f-a-q/video_libre_pour_planet_libre/">Le flash sur le Planet</a></li>
						<li><a href="http://blog.planet-libre.org/f-a-q/la-gestion-de-la-mise-a-jour-des-articles/">Mise &agrave; jour des articles</a></li>
						<li><a href="http://blog.planet-libre.org/f-a-q/flux-specifiques-par-tag-avec-wordpress/">Les flux WordPress</a></li>
						<li><a href="http://blog.planet-libre.org/f-a-q/flux-specifiques-par-tag-avec-dotclear/">Les flux DotClear</a></li>
					</ul>
				</li>
				<li class="menu"><a href="http://blog.planet-libre.org/plan-du-site/">Plan du site</a></li>
				<li class="menu"><a href="http://www.bilboplanet.com/">Bilboplanet</a></li>
			</ul>
		</div>

		<div id="header">
			<!-- Logo -->
			<div id="logo">
				<!-- BEGIN sidebar.action -->
				<div id="show-hide">
					<a href="javascript:showSidebar()">
						<img id="hide-sidebar-button" title="{_Hide sidebar}" src="{$planet.url}/themes/{$planet.theme}/images/sidebar-hide.png" />
						<img id="show-sidebar-button" title="{_Show sidebar}" src="{$planet.url}/themes/{$planet.theme}/images/sidebar-show.png" style="display:none;"/>
					</a>
				</div>
				<!-- END sidebar.action -->
				<a href="{$planet.url}"><img alt="{$planet.title}" src="{$planet.url}/themes/{$planet.theme}/images/logo.png" id="logo" /></a>
			</div>
		</div>

		<div id="header-right">
			<!-- BEGIN search.box -->
			<div class="search">
				<form id="search_form">
					<!-- BEGIN search.popular -->
					<input type="hidden" id="popular" name="popular"
						value="{$params.popular}" />
					<!-- END search.popular -->
					<!-- BEGIN search.user_id -->
					<input type="hidden" id="user_id" name="user_id"
						value="{$params.user_id}" />
					<!-- END search.user_id -->
					<!-- BEGIN search.filter -->
					<input type="hidden" id="filter" name="filter"
						value="{$params.filter}" />
					<!-- END search.filter -->

					<input type="text" id="search_text" class="search-field"
						name="search"
						onFocus="if (this.value=='{_Search} ...') this.value='';"
						onblur="if (this.value=='') this.value='{_Search} ...';"
						value="{_Search} ..." />
					<input type="submit" class="search-submit" value="" />
				</form>
			</div>
			<!-- END search.box -->

			<div id="header-menu" onclick="javascript:showNavigationMenu()">
				<img src="{$planet.url}/themes/{$planet.theme}/images/menu_arrow.png" title="{_Main menu}" alt="{_Main menu}">
			</div>
		</div>
		<div id="navigation-bg" style="display:none">
			<ul class="content-menu" id="navigation">
			<!-- BEGIN menu.contact -->
				<li><a href="{$planet.url}/contact.php">{_Contact us}</a></li>
			<!-- END menu.contact -->
				<li><a href="{$planet.url}/charter.php">{_Charter}</a></li>
				<li><a href="{$planet.url}/users.php">{_Users}</a></li>
				<li><a href="{$planet.url}/tags.php">{_Tribes and tags}</a></li>
			<!-- BEGIN menu.votes -->
				<li><a href="{$planet.url}/stats.php">{_Stats}</a></li>
			<!-- END menu.votes -->

		<hr>
		<!-- BEGIN page.loginbox -->
			<!-- BEGIN menu.shaarli -->
				<li><a href="{$shaarli_instance}">{_My Shaarli}</a></li>
			<!-- END menu.shaarli -->
				<li><a href="{$planet.url}/user/">{_Dashboard} - {$login.username}</a></li>
			<!-- BEGIN page.loginadmin -->
				<li><a href="{$planet.url}/admin/">{_Administration}</a></li>
			<!-- END page.loginadmin -->
				<li><a href="?logout={$planet.url}">{_Logout}</a></li>
		<!-- ELSE page.loginbox -->
				<li><a href="javascript:display_login()">
					{_Login}</a></li>
		<!-- BEGIN menu.subscription -->
				<li><a href="{$planet.url}/signup.php">{_Register}</a></li>
		<!-- END menu.subscription -->
		<!-- END page.loginbox -->

			</ul>
		</div>
	</div>


	<div class="clear" id="body-bg">
		<a name="top"></a>
		<!-- ADD TRIBES HERE
			on the left : -->
		<div id="tribes-bg">
			<!-- BEGIN user.menu -->
				{!include:'user/menu.tpl'}
			<!-- END user.menu -->
			<!-- BEGIN main.menu -->
				{!include:'menu.tpl'}
			<!-- END main.menu -->
		</div>

		<!-- On the right : -->
		<div class="content" id="body">

			<div id="top-area">
				<!-- BEGIN content.topbar -->
				{!include:'topbar.tpl'}
				<!-- END content.topbar -->
			</div>

			<!-- ADD CONTENT HERE -->
			<div id="main-body">
				<!-- BEGIN content.posts -->
					{!include:'posts.tpl'}
				<!-- END content.posts -->

				<!-- BEGIN content.single -->
					{!include:'single.tpl'}
				<!-- END content.single -->

				<!-- BEGIN content.portal -->
					{!include:'portal.tpl'}
				<!-- END content.portal -->

				<!-- BEGIN content.users -->
					{!include:'users.tpl'}
				<!-- END content.users -->

				<!-- BEGIN content.tags -->
					{!include:'tags.tpl'}
				<!-- END content.tags -->

				<!-- BEGIN content.html -->
					{$html}
				<!-- END content.html -->

				<!-- BEGIN content.404 -->
					{!include:'404.tpl'}
				<!-- END content.404 -->

				<!-- BEGIN content.signup -->
					{!include:'signup.tpl'}
				<!-- END content.signup -->

				<!-- BEGIN content.charter -->
					{!include:'charter.tpl'}
				<!-- END content.charter -->

				<!-- BEGIN content.contact -->
					{!include:'contact.tpl'}
				<!-- END content.contact -->

				<!-- BEGIN content.stats -->
					{!include:'stats.tpl'}
				<!-- END content.stats -->

				<!-- BEGIN content.archives -->
					{!include:'archives.tpl'}
				<!-- END content.archives -->
			</div>

		</div><!-- end content -->

	</div><!-- end main -->

	{!include:'popup.tpl'}

	<!-- ADD FOOTER HERE -->
	{!include:'footer.tpl'}

</div><!-- end wrap -->



</body>
</html>
