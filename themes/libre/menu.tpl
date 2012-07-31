<div id="categories-bg">
	<ul class="content" id="categories">
		<li><a href="{$planet.url}/?portal=1">{_Portal}</a></li>
	<!-- BEGIN menu.tribes -->
		<li><a href="{$planet.url}/?list=1&tribe_id={$tribe.id}">{$tribe.name}</a></li>
	<!-- END menu.tribes -->
		<li><a href="{$planet.url}/?list=1&popular=true&filter=week">{_Popular}</a></li>
		<li><a href="{$planet.url}/?list=1">{_All posts}</a></li>
	</ul>
</div>

	<!-- BEGIN menu.votes -->
	<!-- END menu.votes -->

	<!-- BEGIN menu.contact -->
	<!-- END menu.contact -->

	<!-- BEGIN menu.subscription -->
	<!-- END menu.subscription -->

	<!-- BEGIN menu.filter -->
	<!--<div id="submenu">
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
	</div> -->
	<!-- END menu.filter -->
<!-- </div>  end menu -->


