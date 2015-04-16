<div id="content" class="pages">

	<div id="portal">
    <div class="well">
    {_Welcome on this Planet. A Planet is a feed aggregator website designed to collect posts from the weblogs of poeple of an Internet community and display them on a single page. It creates pages with entries from the original feeds. This planet is running with the Bilboplanet engine.}
    </div>

	<!-- BEGIN portal.block -->
	<div class="col-xs-12 col-md-6">
		<div class="portalbox {$tribe.align}box panel panel-default" id="tribe-{$tribe.id}">
			<div class="panel-heading">
		    <h3 class="panel-title">
				<a href="{$planet.url}/?list=1&tribe_id={$tribe.id}">
				<img src="{$tribe.icon}" height="24px"/>
				<span class="box-title">{$tribe.title}</span></a>
				<span class="pull-right">
						<a title="{_Older posts}" href="javascript:tribe_prev('{$tribe.id}',{$tribe.page})">
							<i class="fa fa-chevron-circle-left"></i></a>
						<a title="{_More recent posts}" href="javascript:tribe_next('{$tribe.id}',{$tribe.page})">
							<i class="fa fa-chevron-circle-right"></i></a>
				</span>
				</h3>

			</div>
			<div class="list panel-body">
				<ul>
			<!-- BEGIN portal.entry -->
				<li>{$entry.date} : <a href="{$entry.permalink}" title="{$entry.author_fullname} : {$entry.title}">{$entry.title}</a></li>
			<!-- END portal.entry -->
				</ul>
				<div class="details">
				<!-- BEGIN portal.details -->
					<div class="tags">
						<ul></ul>
					</div>
				<!-- END portal.details -->
				</div>
			</div>

			<div class="feedlink panel-footer"><a href="{$planet.url}/feed.php?type=atom&tribe_id={$tribe.id}"><i class="fa fa-rss-square fa-lg"></i></a></div>
		</div>
	</div>	
	<!-- ELSE portal.block -->
		<div class="portalbox col-xs-12">
			{_No tribes found.}
		</div>
	<!-- END portal.block -->

	</div>

</div>
