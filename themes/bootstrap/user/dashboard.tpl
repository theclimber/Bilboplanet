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
					<span class="tag btn btn-default btn-xs" onclick="javascript:rm_tag('{$post_id}', '{$tag}')">{$tag}</span>
				<!-- END userpost.tags -->
				</span>
			</td>
			<td class="action">
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
				<span class="action">
					<a href="#" data-toggle="modal" data-target="#tag-post-form" title="{_Add tag}">
						<span class="fa-stack">
		          <i class="fa fa-tag fa-stack-lg"></i>
		          <i class="fa fa-plus fa-stack-1x text-success"></i>
		        </span>
					</a>
				</span>
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
			</td>
			</tr>
		<!-- ELSE userpost.item -->
			<tr><td colspan=2>{_No post found}</td></tr>
		<!-- END userpost.item -->
		</table>
	</div>
</div>
<!--
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
-->

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
