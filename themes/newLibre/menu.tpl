<div id="tribes-bg">
	<!-- BEGIN menu.nav -->
	<ul class="menu" id="tribes">
		<li id="portal" class="{$nav.portal}">
			<a href="{$planet.url}/?portal=1">{_Portal}</a>
		</li>
	<!-- BEGIN menu.tribes -->
		<li id="{$tribe.id}" class="{$tribe.selected}">
			<a href="{$planet.url}/?list=1&tribe_id={$tribe.id}">{$tribe.name}</a>
		</li>
	<!-- END menu.tribes -->
		<li id="popular" class="{$nav.popular}">
			<a href="{$planet.url}/?list=1&popular=true&filter=week">{_Popular}</a>
		</li>
		<li id="list" class="{$nav.list}">
			<a href="{$planet.url}/?list=1">{_All posts}</a>
		</li>
	</ul>
	<!-- END menu.nav -->
</div>
