<div id="content" class="pages">

	<!-- BEGIN search.line -->
	<div class="post box">
		<p>Vous avez effectué une recherche dans tous les articles avec le mot: <b>&laquo;&nbsp;{$search_value}&nbsp;&raquo;</b></p>
	</div>
	<!-- END search.line -->

	<!-- BEGIN summary.block -->
	<div class="post box firstbox">
		<h1 class="post-title">Acc&egrave;s rapide aux articles de la page :</h1>
		<ul class="post-summary">
			<!-- BEGIN summary.line -->
			<li><a href="{$summary.url}" title="[{$summary.user}] {$summary.title}">{$summary.date} : {$summary.short_title}</a> <a href="{$planet.url}/?post_id={$summary.id}" target="_blank"><img src="{$planet.url}/themes/{$planet.theme}/images/view.png" /></a></li>
			<!-- ELSE summary.line -->
			<div class="no_posts">
			<p>Aucun article trouvé</p>
			<p>Vous pouvez effectuer une nouvelle recherche :</p>
				<div id="search_no_posts">
					<form id="search_form" action="index.php" method="get">
						<!-- BEGIN search.popular -->
						<input type="hidden" id="popular" name="popular" value="{$params.popular}" />
						<!-- END search.popular -->
						<!-- BEGIN search.user_id -->
						<input type="hidden" id="user_id" name="user_id" value="{$params.user_id}" />
						<!-- END search.user_id -->
						<!-- BEGIN search.filter -->
						<input type="hidden" id="filter" name="filter" value="{$params.filter}" />
						<!-- END search.filter -->
						<input type="text" id="search_text" name="search" value="{$search_value}" />
						<input type="submit" id="search_no_posts_button" value="OK" />
					</form>
				</div>
			</div>
			<!-- END summary.line -->
		</ul>
	</div>
	<!-- END summary.block -->

	<div class="post-nav">
		<!-- BEGIN pagination.up.prev -->
		<a href="#" onclick="javascript:prev_page()" class="post-nav-prev">&laquo; Page précédente</a>
		<!-- END pagination.up.prev -->
		<!-- BEGIN pagination.up.next -->
		<a href="#" onclick="javascript:next_page()" class="post-nav-next">Page suivante &raquo;</a>
		<!-- END pagination.up.next -->
	</div>

	<div id="posts-list">
	<!-- BEGIN post.block -->
	<div class="post box post-content" id="post{$post.id}" name="{$post.id}" hide="1">
			<div class="post-date">
				<span class="post-date-min">{$post.day}/{$post.month}</span><br>
				<span class="post-date-year">{$post.year}</span>
			</div>
			<!--{_Par} <a href="{$planet.url}/index.php?user_id={$post.author_id}">{$post.author_fullname}</a>, {_le} {$post.date} {_à} {$post.hour}.
			<a href="{$planet.url}/index.php?post_id={$post.id}" title="{$post.title}">{_Voir l'article}</a>-->

		<!-- [title] -->
		<h1 class="post-title"><a name="post{$post.id}" href="{$planet.url}/?post_id={$post.id}" title="Visitez la source">{$post.title}</a></h1>
		<div class="post-author">
		<!-- BEGIN post.block.gravatar -->
			<div class="post-avatar">
				<a href="#" onclick="javascript:add_user('{$post.author_id}')" title="Afficher les articles de l'utilisateur">
				<img src="{$avatar_url}&size=32" class="gravatar" /></a>
			</div>
		<!-- END post.block.gravatar -->
		{_Par} <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a>, {_à} {$post.hour}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$post.permalink}" alt="Permalien">Permalien</a>
		<!-- BEGIN post.block.votes -->
		<div class="post-vote">{$votes.html}</div>
		<!-- END post.block.votes -->
		</div>
		<div class="post_description">
			<div class="tag-line">
				{_tags :}
			<!-- BEGIN post.tags -->
				<span class="tag"><a href="#" onclick="javascript:add_tag('{$post_tag}')">{$post_tag}</a> <!--<a href="#" onclick="javascript:rm_tag_post('{$post.id}','{$post_tag}')">x</a>--></span>
			<!-- END post.tags -->
			<!-- BEGIN post.action.tags -->
			<a href="javascript:tag_post('{$post.id}')"><img title="{_Add tag}" src="user/tpl/images/add_tag.png"></a>
			<!-- END post.action.tags -->
			<!-- BEGIN post.action.comment -->
			<a href="javascript:toggle_post_comments('{$post.id}', 1)"><img title="{_Allow comments}" src="user/tpl/images/comment.png"></a>
			<!-- END post.action.comment -->
			<!-- BEGIN post.action.uncomment -->
			<a href="javascript:toggle_post_comments('{$post.id}', 0)"><img title="{_Disable comments}" src="user/tpl/images/nocomment.png"></a>
			<!-- END post.action.uncomment -->
			</div>
		</div>

		<!-- [meta] -->

		<!-- [post content] -->
		<div id="text-{$post.id}" class="post-text" post_id="{$post.id}">{$post.content}</div>
		<!-- [post footer] -->
		<div class="postbox">
		{!include:'social.tpl'}

			<div id="expand-button-{$post.id}" class="collapse-button" onclick="javascript:expand_block({$post.id})">&nbsp;</div>
			Cet article a été vu {$post.nbview} fois. <a href="#" onclick="javascript:add_user('{$post.author_id}')">{$post.author_fullname}</a> a déjà publié <b>{$post.user_posts}</b> articles sur ce planet.<br />
			Aller voir <a href="{$post.permalink}">l'article original</a><br />
		</div>

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



		<!-- BEGIN post.backsummary -->
		<a href="#top" class="post-summary-return" title="Retour au sommaire">&nbsp;</a>
		<!-- END post.backsummary -->



		</div>


		<!-- ELSE post.block -->
	<div class="post box">
		<h1 class="post-title">Aucun article trouvé</h1>
		<p>Vous pouvez effectuer une nouvelle recherche :</p>
	</div>
		<!-- END post.block -->
		</div>

	<div class="post-nav">
		<!-- BEGIN pagination.low.prev -->
		<a href="#" onclick="javascript:prev_page()" class="post-nav-prev">&laquo; Page précédente</a>
		<!-- END pagination.low.prev -->
		<!-- BEGIN pagination.low.next -->
		<a href="#" onclick="javascript:next_page()" class="post-nav-next">Page suivante &raquo;</a>
		<!-- END pagination.low.next -->
	</div>

</div>

