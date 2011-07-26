<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="fr"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="fr"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="fr"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
	<head>

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
			Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<!-- Mobile viewport optimized: j.mp/bplateviewport -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="{$planet.url}/themes/{$planet.theme}/favicon.ico">
		<link rel="apple-touch-icon" href="{$planet.url}/themes/{$planet.theme}/apple-touch-icon.png">

		<title>{$planet.title}</title>
		<meta name="description" content="{$planet.desc_meta}" />
		<meta name="keywords" content="{$planet.keywords}" />
		<meta charset="utf-8" />
		<link rel="stylesheet" href="{$planet.url}/themes/{$planet.theme}/style-{$page}.css" />
	</head>
<!-- hack for ie browser (assuming that ie is a browser) -->
<!--[if lt IE 9]>
<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/javascript/ext-core-debug.js"></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/javascript/{$page}.js"></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/javascript/rating.js"></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/javascript/details.js"></script>
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->
<body>
<a name="top"></a>
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
