<div id="content" class="pages">

	<div class="post box">
		<h1>Archives</h1>
		<!-- BEGIN archives.line -->
		{$post.head}
		<li><a href="{$planet.url}/index.php?post_id={$post.post_id}" title="Lire l'article">{$post.fullname} : {$post.title}</a></li>
		<!-- ELSE archives.line -->
		<li>{_No posts found}</li>
		<!-- END archives.line -->

		<!-- BEGIN archives.closure -->
		</ul>
		<!-- END archives.closure -->
	</div>
</div>
