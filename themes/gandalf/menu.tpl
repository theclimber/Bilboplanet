<div id="menus"><!--menu-->
	<div id="mainmenu">
		<ul>
			<li class="first active"><a href="{$planet.url}">{_Home}</a></li>
			<!-- BEGIN menu.votes -->
			<li><a href="{$planet.url}/?popular=true">{_Top 10}</a></li>
			<!-- END menu.votes -->
			<li><a href="{$planet.url}/stats.php">{_Statistics}</a></li>
			<!-- BEGIN menu.subscription -->
			<li><a href="{$planet.url}/inscription.php">{_Registration}</a></li>
			<!-- END menu.subscription -->
			<li><a href="{$planet.url}/archives.php">{_Archives}</a></li>
			<!-- BEGIN menu.contact -->
			<li><a href="{$planet.url}/contact.php">{_Contact}</a></li>	
			<!-- END menu.contact -->
		</ul>
	</div><!-- end mainmenu -->

	<!-- BEGIN menu.filter -->
	<div id="submenu">
		<ul>
			<li class="{$filter.day}">
				<a class="first" href="index.php?{$filter_url}filter=day">{_Posts of the day}</a>
			</li>
			<li class="{$filter.week}">
				<a class="filter" href="index.php?{$filter_url}filter=week">{_Posts of the week}</a>
			</li>
			<li class="{$filter.month}">
				<a class="filter" href="index.php?{$filter_url}filter=month">{_Posts of the month}</a>
			</li>
			<li>
				<a class="filter" href="index.php?{$filter_url}">{_All posts}</a>
			</li>
		</ul>
	</div><!-- end submenu -->
	<!-- END menu.filter -->
</div><!-- end menu -->
