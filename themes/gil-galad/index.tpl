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

</head>
<body>
<div id="tour">
<div id="arriere_plan">
<div id="global">
<div id="header">

	<div id="title">
		<a href="{$planet.url}">{$planet.title}</a>
		<div id="description_title">{$planet.desc}</div>
	</div>

	<!-- ADD MENU HERE -->
	{!include:'menu.tpl'}

	<div id="centre">
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

		<div class="contenu">
			<div id="colonne">

				<!-- ADD SIDEBAR HERE -->
				{!include:'sidebar.tpl'}
			</div>

			<div id="body">

				<!-- ADD CONTENT HERE -->
				<!-- BEGIN content.posts -->
					<!-- BEGIN menu.filter -->
					<div class="tri">
						<b>{_Filter posts} :&nbsp;&nbsp;&nbsp;&nbsp;</b>
						<span>
							<a href="#" onclick="javascript:set_period('day')">{_Posts of the day}</a>
						</span>&nbsp;&nbsp;-&nbsp;&nbsp;
						<span>
							<a href="#" onclick="javascript:set_period('week')">{_Posts of the week}</a>
						</span>&nbsp;&nbsp;-&nbsp;&nbsp;
						<span>
							<a href="#" onclick="javascript:set_period('month')">{_Posts of the month}</a>
						</span>&nbsp;&nbsp;-&nbsp;&nbsp;
						<span>
							<a href="#" onclick="javascript:rm_period()">{_All the posts}</a>
						</span>
					</div><!-- end submenu -->
					<!-- END menu.filter -->

					{!include:'posts.tpl'}
				<!-- END content.posts -->

				<!-- BEGIN content.portal -->
					{!include:'portal.tpl'}
				<!-- END content.portal -->

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

		</div><!-- end content -->

	</div><!-- end main -->

<!-- ADD FOOTER HERE -->
{!include:'footer.tpl'}

</div>
</div>
</div>
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
