	<!-- BEGIN menu.nav -->
	<ul class="menu" id="tribes">
		<li id="portal" class="entry {$nav.portal}">
			<a href="{$planet.url}/?portal=1">{_Portal}</a>
		</li>
		<li id="list" class="entry {$nav.list}">
			<a href="{$planet.url}/?list=1">{_All posts}</a>
		</li>
	<!-- BEGIN menu.tribes -->
		<li id="{$tribe.id}" class="entry {$tribe.selected}">
			<img src="{$tribe.icon}" width="16px" height="16px"/> <a href="{$planet.url}/?list=1&tribe_id={$tribe.id}">{$tribe.name}</a>
		</li>
	<!-- END menu.tribes -->
<!--		<li id="popular" class="entry {$nav.popular}">
			<a href="{$planet.url}/?list=1&popular=true&filter=week">{_Popular}</a>
		</li>-->
	</ul>
	<!-- END menu.nav -->
