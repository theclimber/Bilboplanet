<!-- BEGIN search.line -->
<div class="search">
	<span class="searchText">{_You are searching for all the posts with :} <span class="search">{$search_value}</span></span>
</div>
<!-- END search.line -->
<!-- BEGIN summary.block -->
<div id="summary">
	<h3><a name="top">{_Fast access to the last posts of the page}</a></h3>
	<ul>
		<!-- BEGIN summary.line -->
		<li><a href="{$summary.url}" title="{$summary.title}">{$summary.date} : {$summary.short_title}</a></li>
		<!-- ELSE summary.line -->
		<div class="no_posts">
		<p>{_No posts found}</p>
		<!-- END summary.line -->
	</ul>
</div>
<!-- END summary.block -->
<div class="navigation">
	<!-- BEGIN pagination.up.prev -->
	<div class="page_previous"><a href="javascript:prev_page()" class="page_prc"> &laquo; {_Previous page}</a></div>
	<!-- END pagination.up.prev -->
	<!-- BEGIN pagination.up.next -->
	<div class="paging_next"><a href="javascript:next_page()" class="page_svt">{_Next page} &raquo;</a></div>
	<!-- END pagination.up.next -->
</div>

<div id="posts-list">
	<!-- BEGIN post.block -->
	<div class="post">
		<div class="post_title">
			<a name="post{$post.id}">&nbsp;</a>
			<!-- BEGIN post.block.gravatar -->
			<div class="gravatar">
				<a href="javascript:add_user('{$post.author_id}')" title="{_Show user's posts}">
				<img src="{$gravatar_url}&size=40" class="gravatar" /></a>
			</div>
			<!-- END post.block.gravatar -->
			<div class="title">
				<h2><a href="{$post.permalink}" title="{_Visit source}">{$post.title}</a></h2>
			</div>
			<!-- BEGIN post.block.votes -->
			<div class="votes">{$votes.html}</div>
			<!-- END post.block.votes -->
			<div class="post_description">
				<h3>{$post.description}</h3>
				<div class="tag-line">
				<!-- BEGIN post.tags -->
					<span class="tag"><a href="javascript:add_tag('{$post_tag}')">{$post_tag}</a></span>
				<!-- END post.tags -->
				<!-- BEGIN post.action.tags -->
				<a href="javascript:tag_post('{$post.id}','{$post.title}')"><img title="{_Add tag}" src="user/tpl/images/add_tag.png"></a>
				<!-- END post.action.tags -->
				</div>
			</div>
		</div>

		<div class="post_content">{$post.content}</div>
		<div class="post_footer">
			<span class="twitter"><a href="http://twitter.com/planetlibre" data-url="{$post.permalink}" class="twitter-share-button" data-count="horizontal" data-lang="fr">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></span>

			<!-- BEGIN post.backsummary -->
			<span class="backtop"><a href="#top">{_Back to summary}&nbsp;&uarr;</a></span>
			<!-- END post.backsummary -->
		</div>
	</div>
	<!-- ELSE post.block -->
	<div class="post">
		<div class="post_title">
			<p class="small_caps">{_No posts found}</p>
			<p>{_You can search to find other articles.}</p>
		</div>
	</div>
	<!-- END post.block -->
</div>

<div class="navigation">
	<!-- BEGIN pagination.low.prev -->
	<div class="page_previous"><a href="javascript:prev_page()" class="page_prc"> &laquo; {_Previous page}</a></div>
	<!-- END pagination.low.prev -->
	<!-- BEGIN pagination.low.next -->
	<div class="paging_next"><a href="javascript:next_page()" class="page_svt">{_Next page} &raquo;</a></div>
	<!-- END pagination.low.next -->
</div>
