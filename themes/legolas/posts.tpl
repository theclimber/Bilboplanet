<!-- BEGIN menu.filter -->
<div id="submenu">
	<ul>
		<li class="{$filter.day}">
			<a href="index.php?{$filter_url}filter=day">{_Posts of the day}</a>
		</li>
		<li class="{$filter.week}">
			<a href="index.php?{$filter_url}filter=week">{_Posts of the week}</a>
		</li>
		<li class="{$filter.month}">
			<a href="index.php?{$filter_url}filter=month">{_Posts of the month}</a>
		</li>
		<li>
			<a href="index.php?{$filter_url}">{_All posts}</a>
		</li>
	</ul>
</div><!-- end submenu -->
<!-- END menu.filter -->

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
	<div class="paging_previous button"><a href="?{$page.params}page={$page.prev}" class="page_prc"> &laquo; {_Previous page}</a></div>
	<!-- END pagination.up.prev -->
	<!-- BEGIN pagination.up.next -->
	<div class="paging_next button"><a href="?{$page.params}page={$page.next}" class="page_svt">{_Next page} &raquo;</a></div>
	<!-- END pagination.up.next -->
</div>

<div class="posts-list">
	<!-- BEGIN post.block -->
	<div class="post">
		<div class="separ_article_top"></div>
		<div class="post_title">
			<a name="post{$post.id}">&nbsp;</a>
			<!-- BEGIN post.block.gravatar -->
			<div class="gravatar">
				<a href="{$planet.url}/index.php?user_id={$post.author_id}" title="{_Show user's posts}">
				<img src="{$gravatar_url}" class="gravatar" /></a>
			</div>
			<!-- END post.block.gravatar -->
			<div class="title">
				<h2><a href="{$post.permalink}" title="{_Visit source}">{$post.title}</a></h2>
			</div>
		</div>
		<!-- BEGIN post.block.votes -->
		<div class="votes">
			{$votes.html}
		</div>
		<!-- END post.block.votes -->
		<div class="post_description">{$post.description}</div>
		<div class="post_content">{$post.content}</div>
		<div class="separ_article_bottom"></div>
		<div class="backtop button"><a href="#top">{_Back to summary}</a></div>
	</div>
	<!-- ELSE post.block -->
	{_No posts found}
	<!-- END post.block -->
</div>

<div id="lower_navigation">
	<!-- BEGIN pagination.low.prev -->
	<div class="paging_previous button"><a href="?{$page.params}page={$page.prev}" class="page_prc"> &laquo; {_Previous page}</a></div>
	<!-- END pagination.low.prev -->
	<!-- BEGIN pagination.low.next -->
	<div class="paging_next button"><a href="?{$page.params}page={$page.next}" class="page_svt">{_Next page} &raquo;</a></div>
	<!-- END pagination.low.next -->
</div>
