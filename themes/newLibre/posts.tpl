<div id="content" class="pages">

	<!-- BEGIN search.line -->
	<div class="post box">
		<p>Vous avez effectué une recherche dans tous les articles avec le mot:
		<b>&laquo;&nbsp;{$search_value}&nbsp;&raquo;</b></p>
	</div>
	<!-- END search.line -->

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
			{!include:'post_tags.tpl'}
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

</div>


<div id="popup" style="display:none">
<div class="window-bar">
	<a href="#" onclick="javascript:close_popup();" id="close_popup">{_Close} x</a>
</div>
<div class="popup-content">
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
