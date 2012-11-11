				<div class="tag-line">
					{_tags :}
				<!-- BEGIN post.tags -->
					<span class="tag"><a href="#" onclick="javascript:add_tag('{$post_tag}')">{$post_tag}</a> <!--<a href="#" onclick="javascript:rm_tag_post('{$post.id}','{$post_tag}')">x</a>--></span>
				<!-- END post.tags -->
				<!-- BEGIN post.action.tags -->
				<a href="javascript:tag_post('{$post.id}')"><img title="{_Add tag}" src="themes/{$planet.theme}/user/images/add_tag.png"></a>
				<!-- END post.action.tags -->
				<!-- BEGIN post.action.comment -->
				<a href="javascript:toggle_post_comments('{$post.id}', 1)"><img title="{_Allow comments}" src="themes/{$planet.theme}/user/images/comment.png"></a>
				<!-- END post.action.comment -->
				<!-- BEGIN post.action.uncomment -->
				<a href="javascript:toggle_post_comments('{$post.id}', 0)"><img title="{_Disable comments}" src="themes/{$planet.theme}/user/images/nocomment.png"></a>
				<!-- END post.action.uncomment -->
				</div>

