<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="description" content="{$planet.desc_meta}" />
	<meta name="keywords" content="{$planet.keywords}" />
	<title>{$params.title}</title>

	<link href="{$planet.url}/themes/{$planet.theme}/css/core.css" rel="stylesheet" type="text/css" />
	<link href="{$planet.url}/themes/{$planet.theme}/css/jquery.fancybox-1.3.2.css" rel="stylesheet" type="text/css" />
<!-- BEGIN css.import -->
	<link href="{$planet.url}/{$css_file}" rel="stylesheet" type="text/css" />
<!-- END css.import -->
	<link rel="alternate" type="application/rss+xml"  title="RSS"  href="{$planet.url}/feed.php?type=rss" />
	<link rel="alternate" type="application/atom+xml" title="ATOM" href="{$planet.url}/feed.php?type=atom" />
	<link rel="icon" type="image/ico" href="{$planet.url}/themes/{$planet.theme}/favicon.png" />

	<script type="text/javascript" src="{$planet.url}/javascript/jquery.js"></script>
	<script type="text/javascript" src="{$planet.url}/javascript/jquery.easing-1.3.pack.js" ></script>
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->

	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/votes.js" ></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/mobile.js" ></script>


</head>
<body>
	<div id="userMenu">
	<!-- BEGIN page.loginbox -->
		<div id="loginBox">
			{_Welcome} {$login.username}
			| <a href="{$planet.url}/user/">Dashboard</a>
		<!-- BEGIN page.loginadmin -->
			| <a href="{$planet.url}/admin/">Administration</a>
		<!-- END page.loginadmin -->
			| <a href="?logout={$planet.url}">Logout</a>
		</div>
	<!-- ELSE page.loginbox -->
		<div id="loginBox"><a href="auth.php?came_from={$planet.url}">login</a>
			<div id="loginForm" style="display:none;">
				<form class="login">
				<label class="username">
					<span>{_Username}</span>
					<input type="text" autocomplete="on" name="session[username]" value="">
				</label>
				<label class="password">
					<span>{_Password}</span>
					<input type="password" name="session[password]" value="">
				</label>
				<label class="remember">
					<input type="checkbox" name="remember_me" value="1">
					<span>{_Remember me}</span>
				</label>
				<button class="submit button" type="submit">{_Connect}</button>
				<a href="resend_password" class="forgot">{_Password forgotten?}</a><br>
				</form>
			</div>
		</div>
	<!-- END page.loginbox -->
	</div>
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
	<div class="content"></div>
</div>

<!-- ADD JAVASCRIPT IMPORT HERE -->
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery.fancybox-1.3.2.pack.js" ></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/fancy.js"></script>
</body>
</html>
