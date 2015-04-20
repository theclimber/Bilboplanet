<div id="content" class="pages">
	<div id="users row">
	<!-- BEGIN user.block -->
		<div class="userbox col-xs-12 col-md-4">
			<div class="panel panel-default">
  			<div class="panel-body">
					<div id="users row">
						<div class="col-xs-3 col-md-3">
							<a href="{$planet.url}/index.php?list=1&user_id={$user.id}" class="image" title="{_Show posts of} {$user.fullname}">
								<img class="avatar" width="50" src="{$avatar_url}" />
							</a>
						</div>
						<div class="col-xs-9 col-md-9">
							<p class="nickname">
								{$user.fullname}<br/>
								<a href="{$user.website}"><i class="fa fa-globe"></i> {_Web site}</a><br/>
								Last post : {$user.last}<br/>
								Post count : {$user.nb_post}
							</p>
							<!-- BEGIN user.shaarli -->
							<div class="shaarlilink"><a href="{$user_shaarli}">
								<img alt="Links" src="{$planet.url}/themes/{$planet.theme}/images/shaarli_24.png" /></a>
							</div>
							<!-- END user.shaarli -->
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="feedlink">
						<a href="{$planet.url}/feed.php?type=atom&users={$user.id}">
							<i class="fa fa-rss-square text-warning fa-lg"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	<!-- ELSE user.block -->
		<div class="userbox">
			{_No users found}
		</div>
	<!-- END user.block -->
	</div>

</div>
