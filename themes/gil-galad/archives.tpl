<div id="archives_list">
	<!-- BEGIN archives.line -->
	{$post.head}
	<li><a href="{$planet.url}/index.php?post_id={$post.post_id}" title="{_Read the article}">{$post.fullname} : {$post.title}</a></li>
	<!-- ELSE archives.line -->
	<li>{_No posts found}</li>
	<!-- END archives.line -->

	<!-- BEGIN archives.closure -->
	</ul>
	<!-- END archives.closure -->
</div>
