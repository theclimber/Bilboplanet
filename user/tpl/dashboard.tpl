<div class="dashboardContent">
	<h1>{_User dashboard}</h1>
	<span class="tooltip"></span>
	<div class="user-post-list dashbox">
		<ul>
		<!-- BEGIN userpost.item -->
			<li class="user-post" id="{$post.id}">
				<span class="pubdate">{$post.date}</span> : 
				<span class="post-title"><a href="{$post.permalink}">{$post.title}</a> 
				<a href="javascript:remove('{$post.id}')"><img title="{_Remove post}" src="tpl/images/action-small-remove.png"></a></span><br/>
				<span class="tag-line">
				<!-- BEGIN userpost.tags -->
					<span class="tag">{$tag}</span>
				<!-- END userpost.tags -->
				<a href="javascript:add_tag('{$post.id}')"><img title="{_Add tag}" src="tpl/images/add_tag.png"></a>
				</span>
			</li>
		<!-- ELSE userpost.item -->
			<li>{_No post found}</li>
		<!-- END userpost.item -->
		</ul>
		</div>
	</div>
