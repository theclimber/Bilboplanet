<div id="content" class="pages">

	<div id="posts-list">
		<!-- BEGIN post.block -->
		<div class="post box post-content" id="post{$post.id}" name="{$post.id}" hide="1">
				<div class="post-date">
					<span class="post-date-min">{$post.day}/{$post.month}</span><br>
					<span class="post-date-year">{$post.year}</span>
				</div>
				<!--{_Par} <a href="{$planet.url}/index.php?user_id={$post.author_id}">{$post.author_fullname}</a>, {_le} {$post.date} {_à} {$post.hour}.
				<a href="{$planet.url}/index.php?post_id={$post.id}" title="{$post.title}">{_Voir l'article}</a>-->

			<!-- [title] -->
			<h1 class="post-title"><a name="post{$post.id}" href="{$planet.url}/?post_id={$post.id}" title="{_Visit the source}">{$post.title}</a></h1>
			<div class="post-author">
			<!-- BEGIN post.block.gravatar -->
				<div class="post-avatar">
					<a href="#" onclick="javascript:add_user('{$post.author_id}')" title="{_Display posts of this user}">
					<img src="{$avatar_url}&size=32" class="gravatar" /></a>
				</div>
			<!-- END post.block.gravatar -->
			{_Par} <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a>, {_à} {$post.hour}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$post.permalink}" alt="{_Permalink}">{_Permalink}</a>
			<!-- BEGIN post.block.votes -->
			<div class="post-vote">{$votes.html}</div>
			<!-- END post.block.votes -->
			</div>
			<div class="post_description">
			{!include:'post_tags.tpl'}
			</div>

			<!-- [meta] -->

			<!-- [post content] -->
			<!-- BEGIN post.multi -->
			<!-- BEGIN post.image -->
			<div id="image-{$post.id}" class="post-image"><img src="{$planet.url}/{$post.image}" /></div>
			<!-- END post.image -->
			<div id="text-{$post.id}" class="post-text" post_id="{$post.id}">{$post.short_content}</div>
			<!-- ELSE post.multi -->
				<div id="text-{$post.id}" class="post-text" post_id="{$post.id}">{$post.content}</div>
			<!-- END post.multi -->

			<!-- [post footer] -->
			<div class="postbox">

				<!-- BEGIN social.statusnet -->
				<iframe height="61" width="61" scrolling="no" frameborder="0" src="{$planet.url}/api/identishare.php?post_id={$post.id}&title={$post.title}&noscript" border="0" marginheight="0" marginwidth="0" allowtransparency="true" class="identishare">
				<div id="identishare" style="vertical-align: bottom;"></div>
				<script type="text/javascript" src="{$planet.url}/api/identishare.php?post_id={$post.id}" defer="defer"></script>
				</iframe>
				<!-- END social.statusnet -->

				<div class="socialbar">
					{!include:'social.tpl'}
				</div>

<!--				<div id="expand-button-{$post.id}" class="collapse-button" onclick="javascript:expand_block({$post.id})">&nbsp;</div>-->
				{_Number of times this post was viewed :} {$post.nbview}<br/>
				{_Number of posts published by }<a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a> : <b>{$post.user_posts}</b><br />
				Have a look on the <a href="{$post.permalink}">{_original post}</a><br />
			</div>

			<!-- BEGIN post.backsummary -->
			<a href="#top" class="post-summary-return" title="{_Back to summary}">&nbsp;</a>
			<!-- END post.backsummary -->
		</div>
		<!-- END post.block -->
	</div>

	<!-- BEGIN post.morebutton -->
	<a><div id="more-button">
		{_More}
	</div></a>
	<!-- END post.morebutton -->

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
