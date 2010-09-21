<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Cache-Control" content="public"/>
	<meta name="description" content="{$planet.desc_meta}" />
	<meta name="keywords" content="{$planet.keywords}" />
	<title>{$planet.title} - {_Mobile version}</title>

	<link href="{$planet.url}/themes/{$planet.theme}/css/mobile.css" rel="stylesheet" type="text/css" />
	<link rel="alternate" type="application/rss+xml"  title="RSS"  href="feed.php?type=rss" />
	<link rel="alternate" type="application/atom+xml" title="ATOM" href="feed.php?type=atom" />
</head>
<body>

<div id="wrap">
	<div id="header">
		{$planet.title} <small>- {_Mobile Version}</small>
	</div>

	<div id="menu">
		<ul>
			<li><a href="?layout=summary" title="{_Go to summary}">{_Summary}</a></li>
			<li><a href="?layout=detail" title="{_Show posts content}">{_Post detail}</a></li>
			<li><a href="?nb_posts={$more}" title="{_Show more posts}">{_See more posts}</a></li>
			<li><a href="{$planet.url}" title="{_Back to site}">{_Back to site}</a></li>
		</ul>
	</div>

	<div id="sp_results">
		<p id='nbr_articles'>
			<b>{$mobile.title}</b> 
			(<a href='mobile.php?nb_posts=10'>10</a>, 
			<a href='mobile.php?nb_posts=20'>20</a>, 
			<a href='mobile.php?nb_posts=30'>30</a>)
		</p>

		<!-- BEGIN line -->
		<div id="post-{$post.id}" class="chunk {!cycle:'',' diff'}">
			<p class="topic">
				<a href="mobile.php?layout=detail{$mobile.params}#post-{$post.id}" rel="nofollow">{$post.title}</a>
			</p>
			<p class="datestamp">{$post.pubdate} | {$post.votes}</p>
		</div>
		<!-- END line -->

		<!-- BEGIN detail -->
		<div id="post-{$post.id}" class="{$post_class}">
			<p class="topic">
				<a href="{$post.permalink}" title="{_Visit source}" rel="nofollow"><b>{$post.author}</b> : {$post.title}</a>
			</p>
			<br/>{$post.content}<br/>
			<p class="datestamp">{$post.pubdate} | {$post.votes}</p>
		</div>
		<!-- END detail -->
	</div>

	{!include:'footer.tpl'}

</div><!-- end wrap -->

</body>
</html>
