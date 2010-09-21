<!-- Sidebar -->
<div class="column1">

	<!-- BEGIN search.box -->
	<div id="search">
		<h2>{_Search}</h2>
		<form id="form_search" action="index.php" method="get">
			<!-- BEGIN search.popular -->
			<input type="hidden" id="popular" name="popular" value="{$params.popular}" />
			<!-- END search.popular -->
			<!-- BEGIN search.user_id -->
			<input type="hidden" id="user_id" name="user_id" value="{$params.user_id}" />
			<!-- END search.user_id -->
			<!-- BEGIN search.filter -->
			<input type="hidden" id="filter" name="filter" value="{$params.filter}" />
			<!-- END search.filter -->
			<input type="text" id="mainmenu_search" name="search" value="{$search_value}" />
			<input type="submit" id="mainmenu_search_btn" value="OK" />
		</form>
	</div>
	<!-- END search.box -->

	<!-- BEGIN sidebar.alert -->
	<div id="alert">
		<h2>{_Quick news}</h2>
		<p>{$planet.msg_info}</p>
	</div>
	<!-- END sidebar.alert -->


	<div id="feeds">
		<h2>{_Subscribe}</h2>
		<ul class="no_bullet">
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
		<ul class="no_bullet">
			<!-- BEGIN sidebar.users.list -->
			<li><a href="{$planet.url}/index.php?user_id={$user.id}" title="{_See members posts}">
			<img src="{$planet.url}/themes/{$planet.theme}/images/ico-external.gif" alt="feed" /></a>
			<a href="{$user.site_url}" title="{_User's website}" target="_blank">{$user.fullname}</a></li>
			<!-- END sidebar.users.list -->
		</ul>
	</div>


	<p>&nbsp;</p>
</div>
<div class="clear"></div>

