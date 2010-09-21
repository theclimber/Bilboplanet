<!-- Sidebar -->
<div class="column1">

	<!-- BEGIN sidebar.alert -->
	<div id="alert">
		<h2>{_Quick news}</h2>
		<p>{$planet.msg_info}</p>
	</div>
	<!-- END sidebar.alert -->

	<div id="feeds">
		<h2>{_Subscribe}</h2>
		<ul>
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-feed.gif" alt="feed" />&nbsp;
			<a href="{$planet.url}/feed.php?type=rss" rel="nofollow">{_Feed with all the posts}</a></li>

			<!-- BEGIN sidebar.popular -->
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-feed.gif" alt="feed" />&nbsp;
			<a href="{$planet.url}/feed.php?type=rss&popular=true" rel="nofollow">{_Popular posts feed}</a></li>
			<!-- END sidebar.popular -->
		</ul>
	</div>

	<div id="userslist">
		<h2>{_Users}</h2>
		<ul>
			<!-- BEGIN sidebar.users.list -->
			<li><a href="{$planet.url}/index.php?user_id={$user.id}" title="{_See members posts}">
			<img src="{$planet.url}/themes/{$planet.theme}/images/ico-external.gif" alt="feed" /></a>
			<a href="{$user.site_url}" title="{_User's website}" target="_blank">{$user.fullname}</a></li>
			<!-- END sidebar.users.list -->
		</ul>
	</div>

	<div id="contribute">
		<h2>{_Contribute}</h2>
		<ul>
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-meta.gif" alt="meta" />&nbsp;
			<a href="{$planet.url}/inscription.php" title="{_Subscribe your blog to the planet}" rel="nofollow">
			{_Add your blog}</a></li>
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-meta.gif" alt="meta" />&nbsp;
			<a href="{$planet.url}/admin" title="{_Admin interface}" rel="nofollow">
			{_Administration}</a></li>
		</ul>
	</div>



	<p>&nbsp;</p>
</div>
<div class="clear"></div>

