<!-- Sidebar -->
<div class="column1">

	<!-- BEGIN sidebar.alert -->
	<div id="nouvelles">
		<div id="nouvelles_head">
			<h2 id="informations_title">{_Quick news}</h2>
		</div>
		<div id="nouvelles_center">{$planet.msg_info}</div>
		<div id="nouvelles_footer">&nbsp;</div>
	</div>
	<!-- END sidebar.alert -->

		<h2 id="abonnement">{_Subscribe}</h2>
		<ul>
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-feed.gif" alt="feed" />&nbsp;
			<a href="{$planet.url}/feed.php?type=rss" rel="nofollow">{_Feed with all the posts}</a></li>

			<!-- BEGIN sidebar.popular -->
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-feed.gif" alt="feed" />&nbsp;
			<a href="{$planet.url}/feed.php?type=rss&popular=true" rel="nofollow">{_Popular posts feed}</a></li>
			<!-- END sidebar.popular -->
		</ul>

		<h2 id="membres">{_Members}</h2>
		<ul>
			<!-- BEGIN sidebar.users.list -->
			<li><a href="{$planet.url}/index.php?user_id={$user.id}" title="{_See members posts}">
			<img src="{$planet.url}/themes/{$planet.theme}/images/ico-external.gif" alt="feed" /></a>
			<a href="{$user.site_url}" title="{_User's website}" target="_blank">{$user.fullname}</a></li>
			<!-- END sidebar.users.list -->
		</ul>

		<h2 id="participer">{_Contribute}</h2>
		<ul>
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-meta.gif" alt="meta" />&nbsp;
			<a href="{$planet.url}/inscription.php" title="{_Subscribe your blog to the planet}" rel="nofollow">
			{_Add your blog}</a></li>
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-meta.gif" alt="meta" />&nbsp;
			<a href="{$planet.url}/admin" title="{_Admin interface}" rel="nofollow">
			{_Administration}</a></li>
		</ul>



	<p>&nbsp;</p>
</div>
<div class="clear"></div>

