<!-- BEGIN menu.nav -->
<div class="menu list-group" id="tribes">
	<a id="portal" href="{$planet.url}/?portal=1" class="entry {$nav.portal} list-group-item">

		<!-- <img src="{$planet.url}/themes/{$planet.theme}/images/portal.png" height="24px"> -->
		<i class="fa fa-home fa-lg"></i>
			<span class="menu-text">{_Portal}</span>
	</a>
	<a id="list" href="{$planet.url}/?list=1" class="entry {$nav.list} list-group-item">

			<!-- <img src="{$planet.url}/themes/{$planet.theme}/images/all.png" height="24px"> -->
			<i class="fa fa-files-o fa-lg"></i>
			<span class="menu-text">{_All posts}</span>
		</a>
<!-- BEGIN menu.tribes -->
	<a id="{$tribe.id}" href="{$planet.url}/?list=1&tribe_id={$tribe.id}" class="entry {$tribe.selected} entry-tribe list-group-item">
		&nbsp;&nbsp;&nbsp;&nbsp;
			<img src="{$tribe.icon}" height="24px"/>
			<span class="menu-text">{$tribe.name}</span>
	</a>
<!-- END menu.tribes -->
</div>
<!-- END menu.nav -->
