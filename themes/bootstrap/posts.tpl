<div id="content" class="page-posts">

	<div id="posts-list">
		<!-- BEGIN post.block -->
		<div class="post post-content row" id="post{$post.id}" name="{$post.id}" hide="1">
			<div class="col-md-2">
				<div class="post-date">
					<h5>{$post.day}/{$post.month}/{$post.year} {$post.hour}</h5>
				</div>
				<!-- BEGIN post.block.gravatar -->
				<div class="post-avatar">
					<a href="#" onclick="javascript:add_user('{$post.author_id}')" title="{_Display posts of this user}">
					<img src="{$avatar_url}&size=64" class="gravatar" /></a>
				</div>
				<!-- END post.block.gravatar -->
				{_Published by } <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a> : <b>{$post.user_posts}</b><br />
			</div>
			<div class="col-md-10">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="post-title panel-title"><a name="post{$post.id}" href="{$planet.url}/?post_id={$post.id}" title="{_Visit the source}">{$post.title}</a> <span class="pull-right">{_Viewed :} {$post.nbview}</span></h3>
					</div>
					<div class="panel-body">
						<!-- BEGIN post.multi -->
						<!-- BEGIN post.image -->
						<div id="image-{$post.id}" class="post-image"><img src="{$planet.url}/{$post.image}" /></div>
						<!-- END post.image -->
						<div id="text-{$post.id}" class="post-text" post_id="{$post.id}"><p>{$post.short_content}</p></div>
						<!-- ELSE post.multi -->
						<div id="text-{$post.id}" class="post-text" post_id="{$post.id}"><p>{$post.content}</p></div>
						<!-- END post.multi -->
						<div class="post-tags">
							{!include:'post_tags.tpl'}
						</div>


						<div class="socialbar">
							{!include:'social.tpl'}
						</div>
					</div>


					<div class="panel-footer">
						<p class="post-author">

							<span class="pull-left">
								{_Written by}
		                        <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a> - <a href="{$post.permalink}" alt="{_Permalink}" title="{_Permalink}">
		                        {_See original post}</a>
							</span>

							<!-- BEGIN post.block.votes -->
							<span class="post-vote pull-right">{$votes.html}</span>
							<!-- END post.block.votes -->
						</p>
					</div>
				</div>
			</div>
		</div>


			<!-- BEGIN post.backsummary -->
			<!-- END post.backsummary -->

		<!-- END post.block -->
	</div>

	<!-- BEGIN post.morebutton -->
	<div id="more-button" style="display:none;" more="yes">
		<i class="fa fa-spinner fa-spin"></i> {_Loading}
	</div>
	<!-- END post.morebutton -->

</div>
