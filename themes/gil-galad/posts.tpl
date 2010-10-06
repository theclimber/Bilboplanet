<!-- BEGIN search.line -->
<div class="search">
	<span class="searchText">{_You are searching for all the posts with :} <span class="search">{$search_value}</span></span>
</div>
<!-- END search.line -->
<div id="summary">
	<h3><a name="top">{_Fast access to the last posts of the page}</a></h3>
	<ul>
		<!-- BEGIN summary.line -->
		<li><a href="{$summary.url}" title="{$summary.title}">{$summary.date} : {$summary.short_title}</a></li>
		<!-- ELSE summary.line -->
		{_No posts found}
		<!-- END summary.line -->
	</ul>
</div>
<div id="upper_navigation">
	<!-- BEGIN pagination.up.prev -->
	<div class="page_previous"><a href="?{$page.params}page={$page.prev}" class="page_prc"> &laquo; {_Previous page}</a></div>
	<!-- END pagination.up.prev -->
	<!-- BEGIN pagination.up.next -->
	<div class="paging_next"><a href="?{$page.params}page={$page.next}" class="page_svt">{_Next page} &raquo;</a></div>
	<!-- END pagination.up.next -->
</div>

<div class="posts-list">
	<!-- BEGIN post.block -->
	<div class="post">
		<div class="separ_article_top"></div>
		<div class="post_title">
			<!-- BEGIN post.block.gravatar -->
			<div class="gravatar">
				<a href="{$planet.url}/index.php?user_id={$post.author_id}" title="{_Show user's posts}">
				<img src="{$gravatar_url}" class="gravatar" /></a>
			</div>
			<!-- END post.block.gravatar -->
			<a name="post{$post.id}">&nbsp;</a>
			<h2><a href="{$post.permalink}" title="{_Visit source}">{$post.title}</a></h2>
		</div>
		<!-- BEGIN post.block.votes -->
		<div class="votes">
			{$votes.html}
		</div>
		<!-- END post.block.votes -->
		<div class="post_description">{$post.description}</div>
		<div class="post_content">{$post.content}</div>
		<div class="separ_article_bottom"></div>
		<a href="#top" class="backtop">{_Back to summary}</a>
	</div>
	<!-- ELSE post.block -->
	{_No posts found}
	<!-- END post.block -->
</div>

<div id="lower_navigation">
	<!-- BEGIN pagination.low.prev -->
	<div class="page_previous"><a href="?{$page.params}page={$page.prev}" class="page_prc"> &laquo; {_Previous page}</a></div>
	<!-- END pagination.low.prev -->
	<!-- BEGIN pagination.low.next -->
	<div class="paging_next"><a href="?{$page.params}page={$page.next}" class="page_svt">{_Next page} &raquo;</a></div>
	<!-- END pagination.low.next -->
</div>
