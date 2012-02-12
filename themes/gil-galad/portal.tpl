<div id="content" class="pages">

	<div id="portal">
	<!-- BEGIN portal.block -->
		<div class="portalbox {$tribe.align}box">
			<div class="title">{$tribe.title}</div>
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
			<div class="feedlink"><a href="{$planet.url}/feed.php?tribe={$tribe.id}">{_Follow this tribe}</a></div>
		</div>
	<!-- ELSE portal.block -->
		<div class="portalbox">
			No tribes found
		</div>
	<!-- END portal.block -->
	</div>

</div>

