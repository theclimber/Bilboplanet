<div id="content" class="pages">

	<div id="portal">
	<!-- BEGIN portal.block -->
		<div class="portalbox {$tribe.align}box">
			<div class="title">
				<a href="{$planet.url}/?list=1&tribe_id={$tribe.id}">
				<img src="{$tribe.icon}" height="24px"/>
				<span class="box-title">{$tribe.title}</span></a>
			</div>
			<div class="list">
				<ul>
			<!-- BEGIN portal.entry -->
				<li>{$entry.author_fullname} : <a href="{$entry.permalink}" title="{$entry.date} : {$entry.title}">{$entry.title}</a></li>
			<!-- END portal.entry -->
				</ul>
			</div>
			<div class="details">
			<!-- BEGIN portal.details -->
				<div class="tags">
					<ul></ul>
				</div>
			<!-- END portal.details -->
			</div>
			<div class="feedlink"><a href="{$planet.url}/feed.php?type=atom&tribe_id={$tribe.id}"><img class="followers-icon" alt="RSS" src="{$planet.url}/themes/{$planet.theme}/images/rss_24.png" /></a></div>
		</div>
	<!-- ELSE portal.block -->
		<div class="portalbox">
			No tribes found
		</div>
	<!-- END portal.block -->
	</div>

</div>

