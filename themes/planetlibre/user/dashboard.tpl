<div id="flash-log" style="display:none;">
	<div id="flash-msg"><!-- spanner --></div>
</div>

<div class="dashboardContent">
	<h1>{_User dashboard}</h1>
	<span class="tooltip">{_Welcome on the new user interface of the Bilboplanet. Here you'll be able to tag your posts and to remove them if you want.}</span>
	<div class="user-post-list dashbox">
		<h2>{_Your latest posts}</h2>
		<table>
		<!-- BEGIN userpost.item -->
			<tr class="user-post {$post.status}" id="{$post.id}">
			<td class="element">
				<span class="pubdate">{$post.date}</span> :
				<span class="post-title"><a href="{$post.permalink}">{$post.title}</a>

				</span><br/>
				<span class="tag-line">
				<!-- BEGIN userpost.tags -->
					<span class="tag" onclick="javascript:rm_tag('{$post_id}', '{$tag}')">{$tag}</span>
				<!-- END userpost.tags -->
				</span>
			</td>
			<td class="action">
				<!-- BEGIN userpost.action.activate -->
				<span class="action">
					<!-- BEGIN userpost.action -->
					<a id="action-post{$post.id}" href="javascript:add_post('{$post.id}')">
						<img title="{_Accept post}" src="{$planet.url}/themes/{$planet.theme}/user/images/post-accept.png">
					</a>
					<!-- ELSE userpost.action -->
					<a id="action-post{$post.id}" href="javascript:rm_post('{$post.id}')">
						<img title="{_Remove post}" src="{$planet.url}/themes/{$planet.theme}/user/images/post-refuse.png">
					</a>
					<!-- END userpost.action -->
				</span>
				<span class="action">
					<a href="javascript:add_tags('{$post.id}','{$post.title2}')"><img title="{_Add tag}" src="{$planet.url}/themes/{$planet.theme}/user/images/add_tag.png"></a>
				</span>
				<!-- BEGIN userpost.action.nocomment -->
				<span class="action">
					<a href="javascript:toggle_post_comments('{$post.id}',0)"><img title="{_Disable comments on post}" src="{$planet.url}/themes/{$planet.theme}/user/images/nocomment.png"></a>
				</span>
				<!-- END userpost.action.nocomment -->
				<!-- BEGIN userpost.action.comment -->
				<span class="action">
					<a href="javascript:toggle_post_comments('{$post.id}',1)"><img title="{_Allow comments on post}" src="{$planet.url}/themes/{$planet.theme}/user/images/comment.png"></a>
				</span>
				<!-- END userpost.action.comment -->
				<!-- END userpost.action.activate -->
			</td>
			</tr>
		<!-- ELSE userpost.item -->
			<tr><td colspan=2>{_No post found}</td></tr>
		<!-- END userpost.item -->
		</table>
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

