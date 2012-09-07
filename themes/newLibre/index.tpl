<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta name="description" content="{$planet.desc}" />
<title>{$params.title}</title>

<link rel="canonical" href="{$planet.url}" />
<!-- CSS -->
<link rel="stylesheet" type="text/css" title="default"
	href="{$planet.url}/themes/{$planet.theme}/css/style.css" />
<link rel="stylesheet" type="text/css" title="big"
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
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/planet_fct.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/votes.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/mobile.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/fancy/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/fancy/jquery.easing-1.3.pack.js"></script>

<!-- ADD JAVASCRIPT IMPORT HERE -->
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->

</head>
<body>

	<div id="header-bg">
		<div id="header">
			<!-- Logo -->
			<div id="logo">
				<a href="{$planet.url}"><img alt="{$planet.title}" src="{$planet.url}/themes/{$planet.theme}/images/planet.png" id="logo" /></a>
				<a href="{$planet.url}" id="planet-title">{$planet.title}</a>
				<div class="description">{$planet.desc}</div>
			</div>
		</div>

		<!-- BEGIN search.box -->
		<div class="search box firstbox">
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
					onFocus="if (this.value=='Rechercher ...') this.value='';"
					onblur="if (this.value=='') this.value='Rechercher ...';"
					value="Rechercher ..." />
				<input type="submit" class="search-submit" value="" />
			</form>
		</div>
		<!-- END search.box -->

		<div id="navigation-bg"><a name="top"></a>
			<ul class="content" id="navigation">
			<!-- BEGIN menu.contact -->
				<li><a href="{$planet.url}/contact.php">{_Contact us}</a></li>
			<!-- END menu.contact -->
				<li><a href="{$planet.url}/charter.php">{_Charter}</a></li>
				<li><a href="{$planet.url}/users.php">{_Users}</a></li>
				<li><a href="{$planet.url}/tags.php">{_Tribes and tags}</a></li>
			<!-- BEGIN menu.votes -->
				<li><a href="{$planet.url}/stats.php">{_Stats}</a></li>
			<!-- END menu.votes -->

		<!-- BEGIN page.loginbox -->
			<!-- BEGIN menu.shaarli -->
				<li><a href="{$shaarli_instance}">{_Shaarli}</a></li>
			<!-- END menu.shaarli -->
				<li><a href="{$planet.url}/user/">{_Dashboard} - {$login.username}</a></li>
			<!-- BEGIN page.loginadmin -->
				<li><a href="{$planet.url}/admin/">{_Administration}</a></li>
			<!-- END page.loginadmin -->
				<li><a href="?logout={$planet.url}">{_Logout}</a></li>
		<!-- ELSE page.loginbox -->
				<li><a href="{$planet.url}/auth.php?came_from={$login.came_from}">
					{_Login}</a></li>
		<!-- END page.loginbox -->

		<!-- BEGIN menu.subscription -->
				<li><a href="{$planet.url}/signup.php">{_Register}</a></li>
		<!-- END menu.subscription -->
			</ul>
		</div>
	</div>


	<div class="clear" id="body-bg">

		<!-- ADD TRIBES HERE -->
		{!include:'menu.tpl'}

		<div class="content" id="body">
			<!-- BEGIN menu.filter -->
			<div id="submenu">
				<ul>
					<li class="{$filter.day}">
						<a class="first" href="index.php?{$filter_url}filter=day">
							{_Posts of the day}</a>
					</li>
					<li class="{$filter.week}">
						<a class="filter" href="index.php?{$filter_url}filter=week">
							{_Posts of the week}</a>
					</li>
					<li class="{$filter.month}">
						<a class="filter" href="index.php?{$filter_url}filter=month">
							{_Posts of the month}</a>
					</li>
					<li>
						<a class="filter" href="index.php?{$filter_url}">
							{_All posts}</a>
					</li>
				</ul>
			</div>

			<!-- BEGIN main.alert -->
			<div class="box">
				<h2>Info Flash</h2>
				<p>{$planet.msg_info}</p>
			</div>
			<!-- END main.alert -->

			<!-- END menu.filter -->

				<!-- ADD CONTENT HERE -->
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

			<div class="widget-area" id="secondary">
				<!-- BEGIN content.sidebar -->
				{!include:'sidebar.tpl'}
				<!-- END content.sidebar -->
			</div>

		</div><!-- end content -->

	</div><!-- end main -->


	<!-- ADD FOOTER HERE -->
	{!include:'footer.tpl'}

</div><!-- end wrap -->



</body>
</html>
