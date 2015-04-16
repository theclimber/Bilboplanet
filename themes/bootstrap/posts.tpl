<div id="content" class="page-posts">

	<div id="posts-list">
		<!-- BEGIN post.block -->
		<div class="post post-content" id="post{$post.id}" name="{$post.id}" hide="1">
			<div class="box post-data">
				<div class="title-data">
					<h1 class="post-title"><a name="post{$post.id}" href="{$planet.url}/?post_id={$post.id}" title="{_Visit the source}">{$post.title}</a></h1>
					<!-- BEGIN post.multi -->
					<!-- BEGIN post.image -->
					<div id="image-{$post.id}" class="post-image"><img src="{$planet.url}/{$post.image}" /></div>
					<!-- END post.image -->
					<div id="text-{$post.id}" class="post-text" post_id="{$post.id}">{$post.short_content}</div>
					<!-- ELSE post.multi -->
					<div id="text-{$post.id}" class="post-text" post_id="{$post.id}">{$post.content}</div>
					<!-- END post.multi -->
					<div class="post-tags">
						{!include:'post_tags.tpl'}
					</div>
				</div>
				<div class="meta-data">
					<!-- BEGIN post.block.votes -->
					<div class="post-vote">{$votes.html}</div>
					<!-- END post.block.votes -->
					<div class="post-date">
						{$post.day}/{$post.month}/{$post.year} {$post.hour}
					</div>
					<div class="post-author">
						{_Written by}
                        <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a> - <a href="{$post.permalink}" alt="{_Permalink}" title="{_Permalink}">
                        {_See original post}</a>
					</div>
					<!-- BEGIN post.block.gravatar -->
					<div class="post-avatar">
						<a href="#" onclick="javascript:add_user('{$post.author_id}')" title="{_Display posts of this user}">
						<img src="{$avatar_url}&size=64" class="gravatar" /></a>
					</div>
					<!-- END post.block.gravatar -->
				</div>
			</div>

			<!-- [post footer] -->
			<div class="postbox">

				<div class="socialbar">
					{!include:'social.tpl'}
				</div>

<!--				<div id="expand-button-{$post.id}" class="collapse-button" onclick="javascript:expand_block({$post.id})">&nbsp;</div>-->
				{_Viewed :} {$post.nbview}<br/>
				{_Published by } <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a> : <b>{$post.user_posts}</b><br />
			</div>

			<!-- BEGIN post.backsummary -->
			<!-- END post.backsummary -->
		</div>
		<!-- END post.block -->
	</div>

	<!-- BEGIN post.morebutton -->
	<div id="more-button" style="display:none;" more="yes">
        <img src="{$planet.url}/themes/{$planet.theme}/images/spinner.gif"><br/>
        {_Loading}
	</div>
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
