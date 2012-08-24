<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta name="description" content="{$planet.desc}" />
<title>{$params.title}</title>

<link rel="canonical" href="{$planet.url}" />
<!-- CSS -->
<link rel="alternate stylesheet" type="text/css" title="normal"
	href="{$planet.url}/themes/{$planet.theme}/css/style-normal.css" />
<link rel="stylesheet" type="text/css" title="default"
	href="{$planet.url}/themes/{$planet.theme}/css/style-big.css" />
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

<!-- <link rel="icon" type="image/ico" href="{$planet.url}/themes/{$planet.theme}/favicon.ico" /> -->
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
	<div id="userMenu">
	<!-- BEGIN page.loginbox -->
		<div id="loginBox">
			{_Welcome} {$login.username}
		<!-- BEGIN menu.shaarli -->
			| <a href="javascript:popup('{$shaarli_instance}')">{_Shaarli}</a>
		<!-- END menu.shaarli -->
			| <a href="javascript:popup('{$planet.url}/user/')">{_Dashboard}</a>
		<!-- BEGIN page.loginadmin -->
			| <a href="{$planet.url}/admin/">{_Administration}</a>
		<!-- END page.loginadmin -->
			| <a href="?logout={$planet.url}">{_Logout}</a>
		</div>
	<!-- ELSE page.loginbox -->
		<div id="loginBox"><a href="{$planet.url}/signup.php">{_Register}</a> {_or} <a><span id="dropdown">Login <span id="login-dropdown">&nbsp;</span></span></a></div>
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
			<a href="{$planet.url}/auth.php?recover=1" class="forgot">{_Password forgotten?}</a> |
				<br>
			</p>
			</form>
		</div>
	<!-- END page.loginbox -->
	</div>

	<div id="navigation-bg"><a name="top"></a>
			<ul class="content" id="navigation">
				<li style="border-left: 1px solid #2F2F2F;"><a href="{$planet.url}/contact.php">{_Contact us}</a></li>
				<li style="border-left: 1px solid #2F2F2F;"><a href="{$planet.url}/charter.php">{_Charter}</a></li>
				<li style="border-left: 1px solid #2F2F2F;"><a href="{$planet.url}/users.php">{_Users}</a></li>
				<li style="border-left: 1px solid #2F2F2F;"><a href="{$planet.url}/tags.php">{_Tribes and tags}</a></li>
				<li style="border-left: 1px solid #2F2F2F;"><a href="{$planet.url}/stats.php">{_Stats}</a></li>
				</li>
			</ul>
	</div>

	<div id="header-bg">
		<div class="content" id="header">
			<!-- Logo -->
			<a href="{$planet.url}"><img alt="{$planet.title}" src="{$planet.url}/themes/{$planet.theme}/images/planet.png" id="logo" /></a>
			<a href="{$planet.url}" id="planet-title">{$planet.title}</a>
			<div class="description">{$planet.desc}</div>
			<!-- Followers -->
			<div id="followers">
				<!-- RSS -->
				<div id="rss" class="followers-col">
				<!-- BEGIN feed.main.button.filter -->
					<a href="{$planet.url}/feed.php?type=rss&tags={$params.tags}&users={$params.users}"><img class="followers-icon" alt="RSS" src="{$planet.url}/themes/{$planet.theme}/images/rss_24.png" /></a>
					<div class="followers-subcol"><a class="followers-num" href="{$planet.url}/feed.php?type=rss&tags={$params.tags}&users={$params.users}">RSS</a>
					<a class="followers-desc" href="{$planet.url}/feed.php?type=rss&tags={$params.tags}&users={$params.users}">Subscribers</a></div>
				<!-- ELSE feed.main.button.filter -->
					<a href="{$planet.url}/feed.php?type=rss"><img class="followers-icon" alt="RSS" src="{$planet.url}/themes/{$planet.theme}/images/rss_24.png" /></a>
					<div class="followers-subcol"><a class="followers-num" href="{$planet.url}/feed.php?type=rss">RSS</a>
					<a class="followers-desc" href="{$planet.url}/feed.php?type=rss">Subscribers</a></div>
				<!-- END feed.main.button.filter -->
				</div>
			</div>
		</div>
		<!-- // End of Followers -->
	</div>

		<!-- ADD MENU HERE -->
		{!include:'menu.tpl'}

	<div class="clear" id="body-bg">
		<div class="content" id="body">
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

				<!-- BEGIN content.submit_article -->
					{!include:'submit_article.tpl'}
				<!-- END content.submit_article -->

			</div>

			<div class="widget-area" id="secondary">
				<!-- BEGIN content.sidebar -->
				{!include:'sidebar.tpl'}
				<!-- END content.sidebar -->
			</div>
			<div class="clear"></div>

		</div><!-- end content -->

	</div><!-- end main -->

	<!-- ADD FOOTER HERE -->
	{!include:'footer.tpl'}

</div><!-- end wrap -->
<div id="popup" style="display:none">
<div class="window-bar">
	<a href="#" onclick="javascript:close_popup();" id="close_popup">{_Close} x</a>
</div>
<div class="popup-content">
</div>
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

{$analytics_html}

<!-- BEGIN social.google.script -->
<!-- Place this tag after the last share tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
<!-- END social.google.script -->

<!-- BEGIN social.twitter.script -->
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<!-- END social.twitter.script -->

<!-- BEGIN social.shaarli.script -->
<script type="text/javascript">
function shaare(id) {
	var url = '{$planet.url}/?post_id='+id;
	var title = url;
	window.open(
		'{$shaarli_instance}/index.php?post=' +
		encodeURIComponent(url)+'&title=' +
		encodeURIComponent(title)+'&source=bookmarklet',
		'_blank',
		'menubar=no,height=390,width=600,toolbar=no,scrollbars=no,status=no'
		);
}
</script>
<!-- END social.shaarli.script -->

</body>
</html>
