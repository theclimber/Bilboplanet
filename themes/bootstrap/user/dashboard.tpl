<div class="row">
	<div class="col-xs-12">
		<div id="flash-log" style="display:none;">
			<div id="flash-msg"><!-- spanner --></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<h1>{_User dashboard}</h1>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<p class="well well-sm">{_Welcome on the new user interface of the Bilboplanet. Here you'll be able to tag your posts and to remove them if you want.}</p>
	</div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">{_Your latest posts}</div>
  <div class="panel-body">
		<!-- BEGIN userpost.item -->
		<div  class="{$post.status}" id="{$post.id}">
			<div class="row">
				<div class="col-xs-12">
					<span class="pubdate">{$post.date}</span> :
					<span class="post-title"><a href="{$post.permalink}">{$post.title}</a></span>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-md-11">
					<span class="tag-line">
					<!-- BEGIN userpost.tags -->
						<span class="tag btn btn-default btn-xs" onclick="javascript:rm_tag('{$post_id}', '{$tag}')">{$tag}</span>
					<!-- END userpost.tags -->
					</span>
				</div>
				<div class="col-xs-12 col-md-1">
					<!-- BEGIN userpost.action.activate -->
					<span class="action">
						<!-- BEGIN userpost.action -->
						<a id="action-post{$post.id}" href="javascript:add_post('{$post.id}')" title="{_Accept post}">
							<i class="fa fa-check-circle text-success"></i>
						</a>
						<!-- ELSE userpost.action -->
						<a id="action-post{$post.id}" href="javascript:rm_post('{$post.id}')" title="{_Remove post}">
							<i class="fa fa-exclamation-circle text-warning"></i>
						</a>
						<!-- END userpost.action -->
					</span>
					<br />
					<span class="action">
						<a href="#" data-toggle="modal" data-target="#tag-post-form" title="{_Add tag}">
							<span class="fa-stack">
								<i class="fa fa-tag fa-stack-lg"></i>
								<i class="fa fa-plus fa-stack-1x text-success"></i>
							</span>
						</a>
					</span>
					<br />
					<!-- BEGIN userpost.action.nocomment -->
					<span class="action">
						<a href="javascript:toggle_post_comments('{$post.id}',0)" title="{_Disable comments on post}">
							<span class="fa-stack">
								<i class="fa fa-comment-o fa-flip-horizontal fa-stack-1x"></i>
								<i class="fa fa-ban fa-stack-2x text-danger"></i>
							</span>
						</a>
					</span>
					<!-- END userpost.action.nocomment -->
					<!-- BEGIN userpost.action.comment -->
					<span class="action">
						<a href="javascript:toggle_post_comments('{$post.id}',1)" title="{_Allow comments on post}">
							<i class="fa fa-comment-o fa-flip-horizontal"></i>
						</a>
					</span>
					<!-- END userpost.action.comment -->
					<!-- END userpost.action.activate -->
				</div>
			</div>
			<div class="row"><hr /></div>
		</div>
		<!-- ELSE userpost.item -->
			<div class="row">
				<div class="col-xs-12">
					{_No post found}
				</div>
			</div>
		<!-- END userpost.item -->

	</div>

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
