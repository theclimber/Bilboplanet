<div id="content" class="pages">
	<div id="tribes">
		<div class="row">
			<div class="col-md-12">
				<h1>{_Tribes}</h1>
			</div>
		</div>
		<div class="row">
	<!-- BEGIN tribe.block -->
		<div class="tribesbox col-xs-12 col-md-6" id="tribe-{$tribe.id}">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title"><a href="{$planet.url}/index.php?list=1&tribe_id={$tribe.id}">{$tribe.name}</a></h3>
			  </div>
			  <div class="panel-body">
					<div class="row">
						<div class="col-xs-3 col-md-3">
							<img src="{$tribe.icon}">
						</div>
						<div class="col-xs-9 col-md-9">
							<p class="nickname">
								Tags : {$tribe.tags}<br/>
								Users : {$tribe.users}<br/>
								search : {$tribe.search}<br/>
								Last post : {$tribe.last}<br/>
								Post count : {$tribe.nb_post}
							</p>
						</div>
					</div>
			  </div>
				<div class="panel-footer"><div class="feedlink"><a href="{$planet.url}/feed.php?type=atom&tribe_id={$tribe.id}"><i class="fa fa-rss-square fa-lg text-warning"></i></a></div></div>
			</div>
		</div>
	<!-- ELSE tribe.block -->
		<div class="tribebox col-md-12">
			{_No tribes found}
		</div>
	<!-- END tribe.block -->
	</div>
	</div>
	<div id="tags">
		<div class="row">
			<div class="col-md-12">
				<h1>{_Tags}</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-body">
				<!-- BEGIN tag.block -->
					<span class="tag tagweigth-{$tag.weigth}">
						<a href="{$planet.url}/index.php?list=1&tags={$tag.id}" class="btn btn-default"><i class="fa fa-tag"></i> {$tag.id}</a>
					</span>
				<!-- ELSE tag.block -->
					<div class="tagbox">
						{_No tags found}
					</div>
				<!-- END tag.block -->
			</div></div>
			</div>
		</div>
	</div>

</div>
