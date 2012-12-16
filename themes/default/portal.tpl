<div id="content" class="pages">

	<div id="portal">
    <div class="welcome-box">
    {_Welcome on this Planet. A Planet is a feed aggregator website designed to collect posts from the weblogs of poeple of an Internet community and display them on a single page. It creates pages with entries from the original feeds. This planet is running with the Bilboplanet engine.}
    </div>
	<!-- BEGIN portal.block -->
		<div class="portalbox {$tribe.align}box" id="tribe-{$tribe.id}">
			<div class="title">
				<a href="{$planet.url}/?list=1&tribe_id={$tribe.id}">
				<img src="{$tribe.icon}" height="24px"/>
				<span class="box-title">{$tribe.title}</span></a>
                <div class="pagination">
                    <a title="{_Older posts}" href="javascript:tribe_prev('{$tribe.id}',{$tribe.page})">
                        <img src="{$planet.url}/themes/{$planet.theme}/images/prev.png"/></a>
                    <a title="{_More recent posts}" href="javascript:tribe_next('{$tribe.id}',{$tribe.page})">
                        <img src="{$planet.url}/themes/{$planet.theme}/images/next.png"/></a>
                </div>
			</div>
			<div class="list">
				<ul>
			<!-- BEGIN portal.entry -->
				<li>{$entry.date} : <a href="{$entry.permalink}" title="{$entry.author_fullname} : {$entry.title}">{$entry.title}</a></li>
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

