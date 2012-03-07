<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="description" content="{$planet.desc_meta}" />
	<meta name="keywords" content="{$planet.keywords}" />
	<title>{$params.title}</title>

	<link href="{$planet.url}/themes/{$planet.theme}/css/core.css" rel="stylesheet" type="text/css" />
	<link href="{$planet.url}/themes/{$planet.theme}/css/boxy.css" rel="stylesheet" type="text/css" />
	<link href="{$planet.url}/themes/{$planet.theme}/css/jquery.fancybox-1.3.2.css" rel="stylesheet" type="text/css" />
<!-- BEGIN css.import -->
	<link href="{$planet.url}/{$css_file}" rel="stylesheet" type="text/css" />
<!-- END css.import -->
	<link rel="alternate" type="application/rss+xml"  title="RSS"  href="{$planet.url}/feed.php?type=rss" />
	<link rel="alternate" type="application/atom+xml" title="ATOM" href="{$planet.url}/feed.php?type=atom" />
<!-- BEGIN feed.tags -->
<link rel="alternate" type="application/rss+xml"  title="RSS with filter"  href="{$planet.url}/feed.php?type=rss&tags={$params.tags}&users={$params.users}" />
<link rel="alternate" type="application/atom+xml" title="ATOM with filter" href="{$planet.url}/feed.php?type=atomi&tags={$params.tags}&users={$params.users}" />
<!-- END feed.tags -->
	<link rel="icon" type="image/ico" href="{$planet.url}/themes/{$planet.theme}/favicon.png" />

	<script type="text/javascript" src="{$planet.url}/javascript/jquery.js"></script>
	<script type="text/javascript" src="{$planet.url}/javascript/jquery.boxy.js"></script>
	<script type="text/javascript" src="{$planet.url}/javascript/jquery.easing-1.3.pack.js" ></script>
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->

	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/votes.js" ></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/mobile.js" ></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/smothscroll.js" ></script>

</head>
<body>
	<div id="userMenu">
	<!-- BEGIN page.loginbox -->
		<div id="loginBox">
			{_Welcome} {$login.username}
			| <a href="javascript:popup('{$planet.url}/user/')">Dashboard</a>
		<!-- BEGIN page.loginadmin -->
			| <a href="{$planet.url}/admin/">Administration</a>
		<!-- END page.loginadmin -->
			| <a href="?logout={$planet.url}">Logout</a>
		</div>
	<!-- ELSE page.loginbox -->
		<div id="loginBox"><a><span id="dropdown">Login <span id="login-dropdown">&nbsp;</span></span></a></div>
		<div id="loginForm" style="display:none;">
			<form class="login" method="POST" action="{$planet.url}/auth.php">
			<input type="hidden" name="came_from" value="{$planet.url}">
			<p>
			<label class="username" for="user_id">
				<span>{_Username}</span>
				<input type="text" name="user_id" value="">
			</label>
			</p><p>
			<label class="password" for="user_pwd">
				<span>{_Password}</span>
				<input type="password" name="user_pwd" value="">
			</label>
			</p><p>
			<label class="remember">
				<input type="checkbox" name="user_remember" value="1" checked>
				<span>{_Remember me}</span>
			</label>
			<input class="submit button" type="submit" value="{_Connect}" />
			</p><p>
			<a href="{$planet.url}/auth.php?recover=1" class="forgot">{_Password forgotten?}</a><br>
			</p>
			</form>
		</div>
	<!-- END page.loginbox -->
	</div>
<div id="wrap">
	<div id="header"><!--header-->
		<!-- BEGIN search.box -->
		<div id="search">
			<form id="search_form" action="index.php" method="get">
				<!-- BEGIN search.popular -->
				<input type="hidden" id="popular" name="popular" value="{$params.popular}" />
				<!-- END search.popular -->
				<!-- BEGIN search.user_id -->
				<input type="hidden" id="user_id" name="user_id" value="{$params.user_id}" />
				<!-- END search.user_id -->
				<!-- BEGIN search.filter -->
				<input type="hidden" id="filter" name="filter" value="{$params.filter}" />
				<!-- END search.filter -->
				<input type="submit" id="mainmenu_search_btn" value="OK" />
				<input type="text" id="search_text" name="search"  value="{$search_value}" />
			</form>
		</div>
		<!-- END search.box -->
		<div id="logo">
			<h1 id="sitename"><a href="{$planet.url}">{$planet.title}</a></h1>
			<h2 class="description">{$planet.desc}</h2>
		</div>
	</div><!-- end header -->

	<div id="main">
		<!-- ADD MENU HERE -->
		{!include:'menu.tpl'}

		<div id="content">
			<div id="body">
				<!-- ADD CONTENT HERE -->
				<!-- BEGIN content.posts -->
					{!include:'posts.tpl'}
				<!-- END content.posts -->

				<!-- BEGIN content.portal -->
					{!include:'portal.tpl'}
				<!-- END content.portal -->

				<!-- BEGIN content.users -->
					{!include:'users.tpl'}
				<!-- END content.users -->


				<!-- BEGIN content.html -->
					{$html}
				<!-- END content.html -->

				<!-- BEGIN content.404 -->
				<div id="content" class="pages">
					<center>
					<h3>{$error.title}</h3>
					<img src="themes/{$planet.theme}/images/404.png">
					<p>{$error.text}</p>
					</center>
				</div>
				<!-- END content.404 -->

				<!-- BEGIN content.subscription -->
					{!include:'subscription.tpl'}
				<!-- END content.subscription -->

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

			<div id="homeright">
				<!-- ADD SIDEBAR HERE -->
				<!-- BEGIN content.sidebar -->
				{!include:'sidebar.tpl'}
				<!-- END content.sidebar -->
			</div>
		</div><!-- end content -->

	</div><!-- end main -->

<!-- ADD FOOTER HERE -->
{!include:'footer.tpl'}

</div><!-- end wrap -->


<div id="popup" style="display:none">
	<div class="window-bar">
		<a href="#" onclick="javascript:close_popup();" id="close_popup">{_Close} x</a>
	</div>
	<div class="popup-content"></div>
</div>
<div id="tag-post-form" style="display:none">
<form>
	<label class="required" for="tags">{_Tags}</label>
	<input type="text" id="tags" name="tags" value=""><br/>
	<span class="description">{_Comma separated tags (ex: linux,web,event)}</span>
	<div class="button">
		<input type="submit" name="apply" class="add_tags" value="{_Apply}" />
	</div>
</form>
</div>

<!-- ADD JAVASCRIPT IMPORT HERE -->
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery.fancybox-1.3.2.pack.js" ></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/fancy.js"></script>
{$analytics_html}

</body>
</html>
