	<div class="well well-sm">
	<div class="row">
	  	<div class="col-md-12">
	<!-- BEGIN menu.filter -->
	<div id="submenu">
		<ul>
			<li class="{$filter.day}">
				<a class="first" href="index.php?{$filter_url}filter=day">
					{_Posts of the day}</a>
			</li>
			<li class="{$filter.week}">
				<a class="filter" href="index.php?{$filter_url}filter=week">
					{_Posts of the week}</a>
			</li>
			<li class="{$filter.month}">
				<a class="filter" href="index.php?{$filter_url}filter=month">
					{_Posts of the month}</a>
			</li>
			<li>
				<a class="filter" href="index.php?{$filter_url}">
					{_All posts}</a>
			</li>
		</ul>
	</div>
	<!-- END menu.filter -->
	</div>
</div>
<div class="row">

	<!-- BEGIN postlist.state -->
	<div class="box page-status">

		<div id="filter-status" class="col-xs-12 col-md-7">
			<h4 id="filter-title">{_Page status}</h4>
			<div id="filter-tribe" style="display:none">{_Tribe :}
				<span id="filter-tribe-content"></span></div>
			<div id="filter-page" style="display:none">{_Page :}
				<span id="filter-page-content"></span></div>
			<div id="filter-search" style="display:none">{_Search with :}
				<span id="filter-search-content"></span></div>
			<div id="filter-period" style="display:none">{_Period of posts :}
				<span id="filter-period-content"></span></div>
			<div id="filter-order" style="display:none">{_Ordered by popularity}</div>
			<div id="filter-tags" style="display:none">{_Filter on tags :}
				<span id="filter-tags-content"></span></div>
			<div id="filter-users" style="display:none">{_Filter on authors :}
				<span id="filter-users-content"></span></div>
			<div id="filter-feed" style="display:none">
				<a id="filter-feed" href="feed.php?type=atom"><i class="fa fa-rss-square fa-lg"></i> {_Feed with this parameters}</a>
				</div>
		</div>
		<div id="filter-order-action" class="col-xs-12 col-md-5 small">
			{_Select only}
            <a href="javascript:order_by('latest')">{_latest posts}</a>
            - <a href="javascript:order_by('popular')">{_popular of the week}</a>
		</div>
	</div>
	<!-- END postlist.state -->
</div>

<div class="row">
	<div class="col-md-12">
	<!-- BEGIN main.alert -->
	<div class="box flash">
		<h2>{_Info Flash}</h2>
		<p>{$planet.msg_info}</p>
	</div>
	<!-- END main.alert -->
</div>
</div>
<div class="row">
	<div class="col-md-12">
	<!-- BEGIN sidebar.widget -->
	<div class="box sidebar-widget" id="widget{$sidebar-widget.id}">
		<h2 class="sidebar-widget" id="widget{$sidebar-widget.id}">{$sidebar-widget.title}</h2>
		{$sidebar-widget.html}
	</div>
	<!-- END sidebar.widget -->
</div>
</div>
</div>
