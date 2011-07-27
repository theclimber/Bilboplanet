	<div id="mainNav">
		<div class="topNavMenu">
			<ul>
				<li {$topNavSelected.0}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-search.png"></a></li>
				<li {$topNavSelected.1}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-rss.png"></a></li>
				<li {$topNavSelected.2}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-twitter.png"></a></li>
				<li {$topNavSelected.3}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-mail.png"></a></li>
				<li {$topNavSelected.4}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-info.png"></a></li>
			</ul>
			<form class="search">
				<div id="triangle"></div>
				<input class="box" type="search" name="search" placeholder="Search" />
				<input type="submit" value="OK" />
			</form>
		</div>
	</div>
	<div id="widgetNav">
		<div class="widget postDetail">
			<h1>{_Post details}</h1>
			<ul>
				<li itemprop="rating" itemscope
					itemtype="http://data-vocabulary.org/Rating">
				★★★★☆
				(<span itemprop="value">{$post.score}</span> on a scale of
				<span itemprop="worst">0</span> to
				<span itemprop="best">5</span>)
				</li>
				<li>{_Publication} : {$post.date} - {$post.hour}</li>
				<li>{_Times this post was viewed} : {$post.nbview}</li>
				<li><a href="#">{_Add this post to your favorites}</a></li>
				<li><a href="{$post.permalink}">{_Go to blog}</a></li>
			</ul>
			<!-- BEGIN side.votes -->
			<p>{$votes.html}</p>
			<!-- END side.votes -->
		</div>

		<div class="widget postDetail">
			<h1>{_Tags}</h1>
			<ul>
			<!-- BEGIN post.tags -->
				<li><a href="#"><tag>{$post_tag}</tag></a></li>
			<!-- ELSE post.tags -->
				<li>{_No tags for this post}</li>
			<!-- END post.tags -->
			</ul>
		</div>
		<div class="widget postDetail authorDetail">
			<h1>{_The Author}</h1>
			<img src="{$post.author_avatar}&size=64" />
			<ul>
				<li>{_Name} : {$post.author_id}</li>
				<li>{_Number of published posts} : {$post.author_posts}</li>
				<li>{_Number of votes} : {$post.author_votes}</li>
				<li>{_Author's blog} : 
					<ul>
					<!-- BEGIN author.sites -->
						<li><a href="{$author_site}">{$author_site}</a></li>
					<!-- END author.sites -->
					</ul>
				</li>
			</ul>
			<p>{$post.author_desc}</p>
		</div>
		<div class="widget postDetail">
			<h1>{_From the same author}</h1>
			<ul>
				<!-- BEGIN author.same -->
				<li><a href="{$same_post.permalink}">{$same_post.title}</a></li>
				<!-- ELSE author.same -->
				<li>{_No post found}</li>
				<!-- END author.same -->
			</ul>
		</div>
	</div>

