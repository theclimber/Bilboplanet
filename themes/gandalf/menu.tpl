<div id="menus"><!--menu-->
	<div id="mainmenu">
		<ul>
			<li class="first active"><a href="{$planet.url}">{_Home}</a></li>
			<!-- BEGIN menu.votes -->
			<li><a href="{$planet.url}/?popular=true">{_Top 10}</a></li>
			<!-- END menu.votes -->
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
				<a class="first" href="#" onclick="javascript:set_period('day')">{_Posts of the day}</a>
			</li>
			<li class="{$filter.week}">
				<a class="filter" href="#" onclick="javascript:set_period('week')">{_Posts of the week}</a>
			</li>
			<li class="{$filter.month}">
				<a class="filter" href="#" onclick="javascript:set_period('month')">{_Posts of the month}</a>
			</li>
			<li>
				<a class="filter" href="#"  onclick="javascript:rm_period()">{_All posts}</a>
			</li>
		</ul>
	</div><!-- end submenu -->
	<!-- END menu.filter -->
</div><!-- end menu -->
