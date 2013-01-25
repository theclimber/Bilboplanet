	<!-- BEGIN menu.nav -->
	<ul class="menu" id="tribes">
		<a href="{$planet.url}/?portal=1">
			<li id="portal" class="entry {$nav.portal}">
				<img src="{$planet.url}/themes/{$planet.theme}/images/portal.png" height="24px">
				<span class="menu-text">{_Portal}</span>
		</li></a>
		<a href="{$planet.url}/?list=1">
			<li id="list" class="entry {$nav.list}">
				<img src="{$planet.url}/themes/{$planet.theme}/images/all.png" height="24px">
				<span class="menu-text">{_All posts}</span>
		</li></a>
	<!-- BEGIN menu.tribes -->
		<a href="{$planet.url}/?list=1&tribe_id={$tribe.id}">
			<li id="{$tribe.id}" class="entry {$tribe.selected} entry-tribe">
				<img src="{$tribe.icon}" height="24px"/>
				<span class="menu-text">{$tribe.name}</span>
		</li></a>
	<!-- END menu.tribes -->
	</ul>

	<!-- END menu.nav -->
