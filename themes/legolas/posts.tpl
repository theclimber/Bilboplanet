<!-- BEGIN menu.filter -->
<div id="submenu">
	<ul>
		<li class="{$filter.day}">
			<a href="#" onclick="javascript:set_period('day')">{_Posts of the day}</a>
		</li>
		<li class="{$filter.week}">
			<a href="#" onclick="javascript:set_period('week')">{_Posts of the week}</a>
		</li>
		<li class="{$filter.month}">
			<a href="#" onclick="javascript:set_period('month')">{_Posts of the month}</a>
		</li>
		<li>
			<a href="#" onclick="javascript:rm_period()">{_All posts}</a>
		</li>
	</ul>
</div><!-- end submenu -->
<!-- END menu.filter -->

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
		{_No posts found}
		<!-- END summary.line -->
	</ul>
</div>
<!-- END summary.block -->
<div id="upper_navigation">
	<!-- BEGIN pagination.up.prev -->
	<div class="paging_previous button"><a href="#" onclick="javascript:prev_page()" class="page_prc"> &laquo; {_Previous page}</a></div>
	<!-- END pagination.up.prev -->
	<!-- BEGIN pagination.up.next -->
	<div class="paging_next button"><a href="#" onclick="javascript:next_page()" class="page_svt">{_Next page} &raquo;</a></div>
	<!-- END pagination.up.next -->
</div>

<div id="posts-list">
	<!-- BEGIN post.block -->
	<div class="post">
		<div class="separ_article_top"></div>
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
		</div>
		<!-- BEGIN post.block.votes -->
		<div class="votes">
			{$votes.html}
		</div>
		<!-- END post.block.votes -->
		<div class="post_description">{$post.description}
			<div class="tag-line">
			<!-- BEGIN post.tags -->
				<span class="tag">{$post_tag}</span>
			<!-- END post.tags -->
			<!-- BEGIN post.action.tags -->
			<a href="javascript:tag_post('{$post.id}')"><img title="{_Add tag}" src="user/tpl/images/add_tag.png"></a>
			<!-- END post.action.tags -->
			<!-- BEGIN post.action.comment -->
			<a href="javascript:toggle_post_comments('{$post.id}', 1)"><img title="{_Allow comments}" src="user/tpl/images/comment.png"></a>
			<!-- END post.action.comment -->
			<!-- BEGIN post.action.uncomment -->
			<a href="javascript:toggle_post_comments('{$post.id}', 0)"><img title="{_Disable comments}" src="user/tpl/images/nocomment.png"></a>
			<!-- END post.action.uncomment -->
			</div>
		</div>
		<div class="post_content">{$post.content}</div>
		<!-- BEGIN post.comment.block -->
		<div class="comment-block post{$comments.post_id}">
			<div class="comment-list">
			<ul>
				<!-- BEGIN post.comment.element -->
				<li class="comment" id="comments-{$comment.id}">
					<span class="comment-author">{$comment.user_fullname_link}</span>
					<span class="comment-pubdate">{$comment.pubdate}</span>
					<span class="comment-content">{$comment.content}</span>
				</li>
				<!-- ELSE post.comment.element -->
				<li class="comment">{_No comments yet}</li>
				<!-- END post.comment.element -->
			</ul>
			</div>
			<hr />
			<div class="comment-form">
				<form class="comment-form" post="{$post.id}">
					<p>
						<label for="user_fullname">{_Fullname}</label>
						<input type="text" id="user_fullname" name="user_fullname" value="">
					</p>
					<p>
						<label for="user_email">{_E-mail}</label>
						<input type="e-mail" id="user_email" name="user_email" value="">
					</p>
					<p>
						<label for="user_site">{_Website}</label>
						<input type="text" id="user_site" name="user_site" value="">
					</p>
					<p>
						<textarea id="post_content"></textarea><br/>
						<span class="description">{_Here you'll find some help concerning the syntax :}
							<a href="#">Wiki syntax</a></span>
					</p>
					<p>
						<input type="submit" id="apply" class="button" value="{_Publish}">
					</p>
				</form>
			</div>
		</div>
		<!-- END post.comment.block -->
		<div class="separ_article_bottom"></div>

		<!-- BEGIN post.backsummary -->
		<div class="backtop button"><a href="#top">{_Back to summary}</a></div>
		<!-- END post.backsummary -->
	</div>
	<!-- ELSE post.block -->
	{_No posts found}
	<!-- END post.block -->
</div>

<div id="lower_navigation">
	<!-- BEGIN pagination.low.prev -->
	<div class="paging_previous button"><a href="#" onclick="javascript:prev_page()" class="page_prc"> &laquo; {_Previous page}</a></div>
	<!-- END pagination.low.prev -->
	<!-- BEGIN pagination.low.next -->
	<div class="paging_next button"><a href="#" onclick="javascript=next_page()" class="page_svt">{_Next page} &raquo;</a></div>
	<!-- END pagination.low.next -->
</div>
