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

	<!-- BEGIN postlist.state -->
	<div id="filter-status">
		<h2 id="filter-title">{_Page status}</h2>
		<div id="filter-nb-items">{_Number of posts :} <span id="filter-nb-items-content">
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

		<h2 id="abonnements">{_Subscribe}</h2>
		<ul>
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

		<!-- BEGIN sidebar.widget -->
		<h2 class="sidebar-widget" id="widget{$sidebar-widget.id}">{$sidebar-widget.title}</h2>
		<div class="sidebar-widget" id="widget{$sidebar-widget.id}">{$sidebar-widget.html}</div>
		<!-- END sidebar.widget -->


	<!-- BEGIN memberlist.box -->
		<h2 id="membres">{_Members}</h2>
		<ul>
			<!-- BEGIN sidebar.users.list -->
			<li><a href="#" onclick="javascript:add_user('{$user.id}')" title="{_See members posts}">
			<img src="{$planet.url}/themes/{$planet.theme}/images/ico-external.gif" alt="feed" /></a>
			<a href="{$user.site_url}" title="{_User's website}" target="_blank">{$user.fullname}</a></li>
			<!-- END sidebar.users.list -->
		</ul>
	<!-- END memberlist.box -->

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

