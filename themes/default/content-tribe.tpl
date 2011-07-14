
<!-- BEGIN summary.block -->
		<!-- BEGIN summary.line -->
		<!-- END summary.line -->
<!-- END summary.block -->

	<div id="tribeDetails">
		<details id="filter">
			<summary>Filtres</summary>
			<ul class="filter">
				<li>Users: <user>Jean</user><user>Jacques</user></li>
				<li>Tags: <tag>foo</tag><tag>bar</tag><tag>baz</tag></li>
<!-- BEGIN search.line -->
				<li>{_Search} : <search>{$search_value}</search></li>
<!-- END search.line -->
			</ul>
		</details>
	</div>

<!-- BEGIN post.block -->
	<article>
		<header class="article">
			<a name="post{$post.id}">&nbsp;</a>
		<!-- BEGIN post.block.gravatar -->
			<img src="{$gravatar_url}&size=32" />
		<!-- END post.block.gravatar -->
			<h2><a href="index.php?page=post&action=view&id={$post.id}">{$post.title}</a></h2>
		</header>
		<description>
			<details id="post1">
				<summary>
					<ul>
						<li>Publié le {$post.date} par {$post.author_fullname} à {$post.hour}</li>
						<li><a href="#">5 commentaires</a></li>
						<!-- BEGIN post.block.votes -->
						<li itemprop="rating" itemscope
							itemtype="http://data-vocabulary.org/Rating">
							<div id="rating{$post.id}" class="stars">
								<input type="radio" name="rating1" value="0.5" title="Very poor">
								<input type="radio" name="rating1" value="1" title="Very poor">
								<input type="radio" name="rating1" value="1.5" title="Not that bad">
								<input type="radio" name="rating1" value="2" title="Not that bad">
								<input type="radio" name="rating1" value="2.5" title="Average">
								<input type="radio" name="rating1" value="3" title="Average">
								<input checked="true" type="radio" name="rating1" value="3.5" title="Good">
								<input type="radio" name="rating1" value="4" title="Good">
								<input type="radio" name="rating1" value="4.5" title="Perfect">
								<input type="radio" name="rating1" value="5" title="Perfect">
							</div>
						</li>
						<!-- END post.block.votes -->
					</ul>
				</summary>
				<ul>
					<li>{_Author} : <user>{$post.author_id}</user></li>
					<li>{_Tags} :
					<!-- BEGIN post.tags -->
						<tag>{$post_tag}</tag>
					<!-- END post.tags -->
					<!-- BEGIN post.action.tags -->
						<a href="javascript:tag_post('{$post.id}','{$post.title}')"><img title="{_Add tag}" src="user/tpl/images/add_tag.png"></a>
					<!-- END post.action.tags -->
					</li>
				<!-- BEGIN post.action.comment -->
				<!-- END post.action.comment -->
				<!-- BEGIN post.action.uncomment -->
				<!-- END post.action.uncomment -->
				</ul>
			</details>
			<content>
			{$post.content}
			</content>
		</description>
	</article>
	<!-- BEGIN post.comment.block -->
	<!-- END post.comment.block -->
	<!-- BEGIN post.backsummary -->
			<span class="backtop"><a href="#top">{_Back to summary}&nbsp;&uarr;</a></span>
	<!-- END post.backsummary -->
<!-- ELSE post.block -->
<article>
	<header class="article">
		<h2>{_No posts found}</h2>
	</header>
	<description>
		<content>
			<p>{_You can search to find other articles.}</p>
		</content>
	</description>
<article>
<!-- END post.block -->

