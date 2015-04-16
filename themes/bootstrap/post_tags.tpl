				<div class="tag-line">
					{_tags :}
				<!-- BEGIN post.tags -->
					<span class="tag"><a href="#" onclick="javascript:add_tag('{$post_tag}')">{$post_tag}</a> <!--<a href="#" onclick="javascript:rm_tag_post('{$post.id}','{$post_tag}')">x</a>--></span>
				<!-- END post.tags -->
				<!-- BEGIN post.action.tags -->
				<a href="javascript:tag_post('{$post.id}')" title="{_Add tag}">
					<span class="fa-stack">
	          <i class="fa fa-tag fa-stack-lg"></i>
	          <i class="fa fa-plus fa-stack-1x text-success"></i>
	        </span>
				</a>
				<!-- END post.action.tags -->
				<!-- BEGIN post.action.comment -->
				<a href="javascript:toggle_post_comments('{$post.id}', 1)" title="{_Allow comments}"><i class="fa fa-comment" ></i></a>
				<!-- END post.action.comment -->
				<!-- BEGIN post.action.uncomment -->
				<a href="javascript:toggle_post_comments('{$post.id}', 0)" title="{_Disable comments}">
					<span class="fa-stack" >
					  <i class="fa fa-comment fa-stack-1x"></i>
					  <i class="fa fa-ban fa-stack-2x text-danger"></i>
					</span>
				<!-- END post.action.uncomment -->
				</div>
