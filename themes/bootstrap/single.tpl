<div id="content" class="page-posts">

	<div id="posts-list">
	<!-- BEGIN post.block -->
	<div class="post box post-content row" id="post{$post.id}" name="{$post.id}" hide="1">
	  <div class="col-md-3">
			<div class="post-date">
				<span class="post-date-min">{$post.day}/{$post.month}</span><br>
				<span class="post-date-year">{$post.year}</span>
			</div>
			<!--{_Par} <a href="{$planet.url}/index.php?user_id={$post.author_id}">{$post.author_fullname}</a>, {_le} {$post.date} {_at} {$post.hour}.
			<a href="{$planet.url}/index.php?post_id={$post.id}" title="{$post.title}">{_See post}</a>-->
			<!-- BEGIN post.block.gravatar -->
				<div class="post-avatar">
					<a href="#" onclick="javascript:add_user('{$post.author_id}')" title="{_Show posts of the user}">
					<img src="{$avatar_url}&size=32" class="gravatar" /></a>
				</div>
			<!-- END post.block.gravatar -->
			<div class="postbox">
				<div id="expand-button-{$post.id}" class="collapse-button" onclick="javascript:expand_block({$post.id})">&nbsp;</div>
	            {_Viewed :} {$post.nbview}<br/>
	            {_Published by }<a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a> : <b>{$post.user_posts}</b><br />
			</div>
	  </div>
		<div class="col-md-9">
			<div class="panel panel-default">
			  <div class="panel-heading">
					<!-- [title] -->
			    <h3 class="post-title panel-title"><a name="post{$post.id}" href="{$planet.url}/?post_id={$post.id}" title="{_Visit source}">SINGLE : {$post.title}</a></h3>
			  </div>
			  <div class="panel-body">
			    Panel content
					<!-- [meta] -->

					<!-- [post content] -->
					<div id="text-{$post.id}" class="post-text" post_id="{$post.id}">{$post.content}</div>
					<!-- [post footer] -->
					{!include:'social.tpl'}

					<!-- BEGIN post.similar.block -->
					<div class="similar-block post{$post.id}">
						<h3>{_Similar posts}</h3>
						<ul>
							<!-- BEGIN post.similar.item -->
							<li>{$similar.author} : <a href="{$similar.permalink}" title="{$similar.author} - {$similar.title}">{$similar.title}</a> ({$similar.pubdate})</li>
							<!-- END post.similar.item -->
						</ul>
					</div>
					<!-- END post.similar.block -->

					<!-- BEGIN post.comment.block -->
					<div class="comment-block post{$post.id}">
						<div class="comment-list">
						<ul>
							<!-- BEGIN post.comment.element -->
							<li class="comment" id="comments-{$comment.id} co-post-{$post.id}">
								<span class="comment-author">{$comment.user_fullname_link}</span>
								<span class="comment-pubdate">{$comment.pubdate}</span>
								<span class="comment-content">{$comment.content}</span>
							</li>
							<!-- ELSE post.comment.element -->
							<li class="comment">{_No comments yet}</li>
							<!-- END post.comment.element -->
						</ul>
						</div>
						<hr />
						<div class="comment-form">
							<form class="comment-form" postid="{$post.id}">
								<p>
									<label for="user_fullname">{_Fullname : }</label>
									<input type="text" id="user_fullname" name="user_fullname" value="">
								</p>
								<p>
									<label for="user_email">{_E-mail : }</label>
									<input type="e-mail" id="user_email" name="user_email" value="">
								</p>
								<p>
									<label for="user_site">{_Website : }</label>
									<input type="text" id="user_site" name="user_site" value="">
								</p>
								<p>
									<textarea class="comment_content" id="comment_text_{$post.id}" name"content"></textarea><br/>
			<!--						<span class="description">{_Here you'll find some help concerning the syntax :}
										<a href="#">Wiki syntax</a></span>-->
								</p>
								<p>
									<input type="submit" id="apply" class="button" value="{_Publish}">
								</p>
							</form>
						</div>
					</div>
					<!-- END post.comment.block -->
			  </div>
				<div class="panel-footer">
					<div class="post-author">

					{_Par} <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a>, {_at} {$post.hour} <a href="{$post.permalink}" alt="Permalien">Permalien</a>

					<div class="post_description">
						{!include:'post_tags.tpl'}
					</div>

					</div>

					<!-- BEGIN post.block.votes -->
					<div class="post-vote">{$votes.html}</div>
					<!-- END post.block.votes -->
					</div>
				</div>
			</div>
	  </div>
	</div>

		<!-- ELSE post.block -->
		<div class="post box">
			<h1 class="post-title">{_No post found}</h1>
		</div>
		<!-- END post.block -->

</div>
