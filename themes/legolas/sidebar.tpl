<!-- Sidebar -->
<div class="column1">

	<!-- BEGIN search.box -->
	<div id="search">
		<h2>{_Search}</h2>
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
			<input type="text" id="search_text" name="search" value="{$search_value}" />
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

	<!-- BEGIN postlist.state -->
	<div id="filter-status">
		<h2 id="filter-title">{_Page status}</h2>
		<div id="filter-nb-items">{_Number of items :} <span id="filter-nb-items-content">
			<a href="#" onclick="javascript:set_nb_items(10)">10</a>,
			<a href="#" onclick="javascript:set_nb_items(15)">15</a>,
			<a href="#" onclick="javascript:set_nb_items(20)">20</a>
			</span></div>
		<div id="filter-page" style="display:none">{_Page :}
			<span id="filter-page-content"></span></div>
		<div id="filter-search" style="display:none">{_Searching with :}
			<span id="filter-search-content"></span></div>
		<div id="filter-period" style="display:none">{_Period of posts :}
			<span id="filter-period-content"></span></div>
		<div id="filter-popular" style="display:none">{_In popular tags}</div>
		<div id="filter-tags" style="display:none">{_Tagged with :}
			<span id="filter-tags-content"></span></div>
		<div id="filter-users" style="display:none">{_Written by :}
			<span id="filter-users-content"></span></div>
		<div id="filter-feed" style="display:none">
			<a id="filter-feed" href="feed.php?type=atom">{_Parametrized feed}</a>
			</div>
	</div>
	<!-- END postlist.state -->

	<!-- BEGIN sidebar.widget -->
	<div class="sidebar-widget" id="widget{$sidebar-widget.id}">
		<h2 class="sidebar-widget" id="widget{$sidebar-widget.id}">{$sidebar-widget.title}</h2>
		{$sidebar-widget.html}
	</div>
	<!-- END sidebar.widget -->

	<div id="feeds">
		<h2>{_Subscribe}</h2>
		<ul class="no_bullet">
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-feed.gif" alt="feed" />&nbsp;
			<a href="{$planet.url}/feed.php?type=rss" rel="nofollow">{_Feed with all the posts}</a></li>
		<!-- BEGIN feed.main.button.filter -->
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-feed.gif" alt="feed" />&nbsp;
			<a href="{$planet.url}/feed.php?type=rss&tags={$params.tags}&users={$params.users}" rel="nofollow">{_Feed with the posts of this category : } {$params.tags}</a></li>
		<!-- END feed.main.button.filter -->

			<!-- BEGIN sidebar.popular -->
			<li><img src="{$planet.url}/themes/{$planet.theme}/images/ico-feed.gif" alt="feed" />&nbsp;
			<a href="{$planet.url}/feed.php?type=rss&popular=true" rel="nofollow">{_Popular posts feed}</a></li>
			<!-- END sidebar.popular -->
		</ul>
	</div>

	<!-- BEGIN memberlist.box -->
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
	<!-- END postlist.state -->

	<p>&nbsp;</p>
</div>
<div class="clear"></div>

