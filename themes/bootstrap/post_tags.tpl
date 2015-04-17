				<div class="tag-line">
					{_tags :}
				<!-- BEGIN post.tags -->
					<span class="tag"><a href="#" onclick="javascript:add_tag('{$post_tag}')" class="btn btn-default btn-xs">{$post_tag}</a> <!--<a href="#" onclick="javascript:rm_tag_post('{$post.id}','{$post_tag}')">x</a>--></span>
				<!-- END post.tags -->
				<!-- BEGIN post.action.tags -->
				<a href="#" title="{_Add tag}" data-toggle="modal" data-target="#tag-post-form">
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
				</a>
				<!-- END post.action.uncomment -->
				</div>


<!-- Modal -->
<div class="modal fade" id="tag-post-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <h4 class="modal-title" id="myModalLabel">{_Add tag}</h4>
      </div>
			<form id="tagform" method="POST">
				<input type="hidden" name="ajax" value="tagging" />
				<input type="hidden" name="post_id" value="{$post.id}" />
				<input type="hidden" name="action" value="add_tags" />
	      <div class="modal-body">
					<div class="form-group">
						<label class="required" for="tags">{_Tags}</label>
						<input type="text" id="tags" name="tags" value="" class="form-control" placeholder="{_Comma separated tags (ex: linux,web,event)}" />
					</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" name="apply" class="btn btn-primary" onclick="jSubmitAndCloseDialog('tag-post-form', '{$planet.url}/user/api/', 'tagform', true)">{_Apply}</button>
	      </div>
			</form>
    </div>
  </div>
</div>
