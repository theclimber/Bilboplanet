<!-- Sidebar -->
<div class="column1">

	<!-- BEGIN sidebar.alert -->
	<div id="alert">
		<h2>{_Quick news}</h2>
		<p>{$planet.msg_info}</p>
	</div>
	<!-- END sidebar.alert -->

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
	</div>

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

	<!-- BEGIN sidebar.widget -->
	<div class="sidebar-widget" id="widget{$widget.id}">
		<h2 class="sidebar-widget" id="widget{$widget.id}">{$widget.title}</h2>
		{$widget.html}
	</div>
	<!-- END sidebar.widget -->

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

