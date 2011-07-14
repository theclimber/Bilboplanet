<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>{$planet.title}</title>
		<meta name="description" content="{$planet.desc_meta}" />
		<meta name="keywords" content="{$planet.keywords}" />
		<meta charset="utf-8" />
		<link rel="stylesheet" href="{$planet.url}/themes/{$planet.theme}/style-{$page}.css" />
	</head>
<!--[if lt IE 9]>
<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/javascript/ext-core-debug.js"></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/javascript/{$page}.js"></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/javascript/rating.js"></script>
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->
<body>

<div id="wrap">
<header class="top">
	<div id="slide-out">
	{!include:'slideout.tpl'}
	</div>
	<div id="siteTitle">
		<h1><a href="index.php">{$planet.title}</a></h1>
		<h2>{$planet.desc}</h2>
	</div>
</header>
<header class="menu">
<!-- BEGIN menu.tribe -->
	{!include:'menu-tribe.tpl'}
<!-- END menu.tribe -->

<!-- BEGIN menu.single -->
	{!include:'menu-single.tpl'}
<!-- END menu.single -->

<!-- BEGIN menu.user -->
	{!include:'menu-user.tpl'}
<!-- END menu.user -->

</header>

<section>
<!-- BEGIN content.tribe -->
	{!include:'content-tribe.tpl'}
<!-- END content.tribe -->

<!-- BEGIN content.user -->
	{!include:'content-user.tpl'}
<!-- END content.user -->

<!-- BEGIN content.single -->
	{!include:'content-single.tpl'}
<!-- END content.single -->

<!-- BEGIN content.404 -->
	<div id="content" class="pages">
		<center>
		<h3>{$error.title}</h3>
		<img src="themes/{$planet.theme}/images/404.png">
		<p>{$error.text}</p>
		</center>
	</div>
<!-- END content.404 -->
</section>

<nav>
<!-- BEGIN nav.tribe -->
	{!include:'nav-tribe.tpl'}
<!-- END nav.tribe -->

<!-- BEGIN nav.user -->
	{!include:'nav-user.tpl'}
<!-- END nav.user -->

<!-- BEGIN nav.single -->
	{!include:'nav-single.tpl'}
<!-- END nav.single -->
</nav>

<footer>
	&copy; Copyright 2011
</footer>
</div>

</body>
</html>
