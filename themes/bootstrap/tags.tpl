<div id="content" class="pages">
	<div id="tribes">
	<h1>{_Tribes}</h1>
	<!-- BEGIN tribe.block -->
		<div class="tribesbox" id="tribe-{$tribe.id}">
			<a href="{$planet.url}/index.php?list=1&tribe_id={$tribe.id}">{$tribe.name}</a>
			<p><img src="{$tribe.icon}"></p>
			<p class="nickname">
				Tags : {$tribe.tags}<br/>
				Users : {$tribe.users}<br/>
				search : {$tribe.search}<br/>
				Last post : {$tribe.last}<br/>
				Post count : {$tribe.nb_post}
			</p>
			<div class="feedlink"><a href="{$planet.url}/feed.php?type=atom&tribe_id={$tribe.id}"><img alt="RSS" src="{$planet.url}/themes/{$planet.theme}/images/rss_24.png" /></a></div>
		</div>
	<!-- ELSE tribe.block -->
		<div class="tribebox">
			{_No tribes found}
		</div>
	<!-- END tribe.block -->
	</div>

	<div id="tags">
	<h1>{_Tags}</h1>
	<ul>
	<!-- BEGIN tag.block -->
		<li><span class="tag tagweigth-{$tag.weigth}">
			<a href="{$planet.url}/index.php?list=1&tags={$tag.id}">{$tag.id}</a>
		</span></li>
	<!-- ELSE tag.block -->
		<div class="tagbox">
			{_No tags found}
		</div>
	<!-- END tag.block -->
	</ul>
	</div>

</div>
