<div id="menus"><!--menu-->
	<div id="mainmenu">
		<ul>
			<li class="first active"><a href="{$planet.url}">{_Home}</a></li>
			<!-- BEGIN menu.votes -->
			<!-- END menu.votes -->
			<!-- BEGIN menu.subscription -->
			<!-- END menu.subscription -->
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
