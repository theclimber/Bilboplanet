<div id="content" class="page-posts">

	<div id="posts-list">
	<!-- BEGIN post.block -->
	<div class="post box post-content" id="post{$post.id}" name="{$post.id}" hide="1">
			<div class="post-date">
				<span class="post-date-min">{$post.day}/{$post.month}</span><br>
				<span class="post-date-year">{$post.year}</span>
			</div>
			<!--{_Par} <a href="{$planet.url}/index.php?user_id={$post.author_id}">{$post.author_fullname}</a>, {_le} {$post.date} {_at} {$post.hour}.
			<a href="{$planet.url}/index.php?post_id={$post.id}" title="{$post.title}">{_See post}</a>-->

		<!-- [title] -->
		<h1 class="post-title"><a name="post{$post.id}" href="{$planet.url}/?post_id={$post.id}" title="{_Visit source}">SINGLE : {$post.title}</a></h1>
		<div class="post-author">
		<!-- BEGIN post.block.gravatar -->
			<div class="post-avatar">
				<a href="#" onclick="javascript:add_user('{$post.author_id}')" title="{_Show posts of the user}">
				<img src="{$avatar_url}&size=32" class="gravatar" /></a>
			</div>
		<!-- END post.block.gravatar -->
		{_Par} <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a>, {_at} {$post.hour}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$post.permalink}" alt="Permalien">Permalien</a>
		<!-- BEGIN post.block.votes -->
		<div class="post-vote">{$votes.html}</div>
		<!-- END post.block.votes -->
		</div>
		<div class="post_description">
			{!include:'post_tags.tpl'}
		</div>

		<!-- [meta] -->

		<!-- [post content] -->
		<div id="text-{$post.id}" class="post-text" post_id="{$post.id}">{$post.content}</div>
		<!-- [post footer] -->
		<div class="postbox">
		{!include:'social.tpl'}

			<div id="expand-button-{$post.id}" class="collapse-button" onclick="javascript:expand_block({$post.id})">&nbsp;</div>
            {_Viewed :} {$post.nbview}<br/>
            {_Published by }<a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a> : <b>{$post.user_posts}</b><br />
		</div>

		<!-- BEGIN post.similar.block -->
		<div class="similar-block post{$post.id}">
			<h3>{_Similar posts}</h3>
			<ul>
				<!-- BEGIN post.similar.item -->
				<li>{$similar.author} : <a href="{$similar.permalink}" title="{$similar.author} - {$similar.title}">{$similar.title}</a> ({$similar.pubdate})</li>
				<!-- END post.similar.item -->
			</ul>
		</div>
		<!-- END post.similar.block -->


		<!-- BEGIN post.comment.block -->
		<div class="comment-block post{$post.id}">
			<div class="comment-list">
			<ul>
				<!-- BEGIN post.comment.element -->
				<li class="comment" id="comments-{$comment.id} co-post-{$post.id}">
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
				<form class="comment-form" postid="{$post.id}">
					<p>
						<label for="user_fullname">{_Fullname : }</label>
						<input type="text" id="user_fullname" name="user_fullname" value="">
					</p>
					<p>
						<label for="user_email">{_E-mail : }</label>
						<input type="e-mail" id="user_email" name="user_email" value="">
					</p>
					<p>
						<label for="user_site">{_Website : }</label>
						<input type="text" id="user_site" name="user_site" value="">
					</p>
					<p>
						<textarea class="comment_content" id="comment_text_{$post.id}" name"content"></textarea><br/>
<!--						<span class="description">{_Here you'll find some help concerning the syntax :}
							<a href="#">Wiki syntax</a></span>-->
					</p>
					<p>
						<input type="submit" id="apply" class="button" value="{_Publish}">
					</p>
				</form>
			</div>
		</div>
		<!-- END post.comment.block -->

		</div>


		<!-- ELSE post.block -->
		<div class="post box">
			<h1 class="post-title">{_No post found}</h1>
		</div>
		<!-- END post.block -->
	</div>


</div>


<div id="popup" style="display:none">
<div class="window-bar">
	<a href="#" onclick="javascript:close_popup();" id="close_popup">{_Close} x</a>
</div>
<div class="popup-content">
</div>
</div>
<div id="tag-post-form" style="display:none">
<form>
	<label class="required" for="tags">{_Tags}</label>
	<input type="text" id="tags" name="tags" value=""><br/>
	<span class="description">{_Comma separated tags (ex: linux,web,event)}</span>
	<div class="button">
		<input type="submit" name="apply" class="add_tags" value="{_Apply}" />
	</div>
</form>
</div>

