	<!-- BEGIN menu.nav -->
	<ul class="menu" id="tribes">
		<a href="{$planet.url}/?portal=1">
			<li id="portal" class="entry {$nav.portal}">
				{_Portal}
		</li></a>
		<a href="{$planet.url}/?list=1">
			<li id="list" class="entry {$nav.list}">
				{_All posts}
		</li></a>
	<!-- BEGIN menu.tribes -->
		<a href="{$planet.url}/?list=1&tribe_id={$tribe.id}">
			<li id="{$tribe.id}" class="entry {$tribe.selected}">
				<img src="{$tribe.icon}" width="16px" height="16px"/> {$tribe.name}
		</li></a>
	<!-- END menu.tribes -->
<!--		<li id="popular" class="entry {$nav.popular}">
			<a href="{$planet.url}/?list=1&popular=true&filter=week">{_Popular}</a>
		</li>-->
	</ul>
	<!-- END menu.nav -->
