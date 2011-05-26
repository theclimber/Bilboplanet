<div class="dashboardContent">
	<h1>{_User dashboard}</h1>
	<span class="tooltip">{_Welcome on the new user interface of the Bilboplanet.
		Here you'll be able to tag your posts and to remove them if you want.}</span>
	<div class="user-post-list dashbox">
		<ul>
		<!-- BEGIN userpost.item -->
			<li class="user-post {$post.status}" id="{$post.id}">
				<span class="pubdate">{$post.date}</span> : 
				<span class="post-title"><a href="{$post.permalink}">{$post.title}</a> 

				<!-- BEGIN userpost.action -->
				<a id="action-post{$post.id}" href="javascript:add_post('{$post.id}')">
					<img title="{_Accept post}" src="tpl/images/post-accept.png">
				</a>
				<!-- ELSE userpost.action -->
				<a id="action-post{$post.id}" href="javascript:rm_post('{$post.id}')">
					<img title="{_Remove post}" src="tpl/images/post-refuse.png">
				</a>
				<!-- END userpost.action -->
				</span><br/>
				<span class="tag-line">
				<!-- BEGIN userpost.tags -->
					<span class="tag" onclick="javascript:rm_tag('{$post_id}', '{$tag}')">{$tag}</span>
				<!-- END userpost.tags -->
				<a href="javascript:add_tags('{$post.id}','{$post.title2}')"><img title="{_Add tag}" src="tpl/images/add_tag.png"></a>
				</span>
			</li>
		<!-- ELSE userpost.item -->
			<li>{_No post found}</li>
		<!-- END userpost.item -->
		</ul>
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

<script type="text/javascript" src="{$planet.url}/user/tpl/js/jquery.boxy.js" ></script>
<script type="text/javascript" src="{$planet.url}/user/tpl/js/dashboard.js" ></script>
