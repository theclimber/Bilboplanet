<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="description" content="{$planet.desc_meta}" />
	<meta name="keywords" content="{$planet.keywords}" />
	<title>{$params.title}</title>

	<link href="{$planet.url}/themes/{$planet.theme}/css/core.css" rel="stylesheet" type="text/css" />
	<link href="{$planet.url}/themes/{$planet.theme}/css/jquery.fancybox-1.3.2.css" rel="stylesheet" type="text/css" />
	<link rel="alternate" type="application/rss+xml"  title="RSS"  href="{$planet.url}/feed.php?type=rss" />
	<link rel="alternate" type="application/atom+xml" title="ATOM" href="{$planet.url}/feed.php?type=atom" />
	<link rel="icon" type="image/ico" href="{$planet.url}/themes/{$planet.theme}/favicon.png" />

	<script type="text/javascript" src="{$planet.url}/javascript/jquery.js"></script>
	<script type="text/javascript" src="{$planet.url}/javascript/jquery.easing-1.3.pack.js" ></script>
	<script type="text/javascript" src="{$planet.url}/javascript/jquery.fancybox-1.3.2.pack.js" ></script>

	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/votes.js" ></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/mobile.js" ></script>

</head>
<body>
<div id="tour">
<div id="arriere_plan">
<div id="global">
<div id="header">

	<div id="title">
		<a href="{$planet.url}">{$planet.title}</a>
		<div id="description_title">{$planet.desc}</div>
	</div>

	<!-- ADD MENU HERE -->
	{!include:'menu.tpl'}

	<div id="centre">

		<div class="contenu">
			<div id="colonne">

				<!-- ADD SIDEBAR HERE -->
				{!include:'sidebar.tpl'}
			</div>

			<div id="centre_centre">

				<!-- BEGIN menu.filter -->
				<div class="tri">
					<b>Filtrer les articles :&nbsp;&nbsp;&nbsp;&nbsp;</b>
					<span>
						<a href="index.php?{$filter_url}filter=day">Articles du jour</a>
					</span>&nbsp;&nbsp;-&nbsp;&nbsp;  
					<span>
						<a href="index.php?{$filter_url}filter=week">Articles de la semaine</a>
					</span>&nbsp;&nbsp;-&nbsp;&nbsp;
					<span>
						<a href="index.php?{$filter_url}filter=month">Articles du mois</a>
					</span>&nbsp;&nbsp;-&nbsp;&nbsp;
					<span>
						<a href="index.php?{$filter_url}">Tous les articles</a>
					</span>
				</div><!-- end submenu -->
				<!-- END menu.filter -->

				<!-- ADD CONTENT HERE -->
				<!-- BEGIN content.posts -->
					{!include:'posts.tpl'}
				<!-- END content.posts -->

				<!-- BEGIN content.html -->
					{$html}
				<!-- END content.html -->

				<!-- BEGIN content.subscription -->
					{!include:'subscription.tpl'}
				<!-- END content.subscription -->

				<!-- BEGIN content.contact -->
					{!include:'contact.tpl'}
				<!-- END content.contact -->

				<!-- BEGIN content.stats -->
					{!include:'stats.tpl'}
				<!-- END content.stats -->

				<!-- BEGIN content.archives -->
					{!include:'archives.tpl'}
				<!-- END content.archives -->
			</div>

		</div><!-- end content -->

	</div><!-- end main -->

<!-- ADD FOOTER HERE -->
{!include:'footer.tpl'}

</div>
</div>
</div>
</div><!-- end wrap -->

<!-- ADD JAVASCRIPT IMPORT HERE -->
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->
</body>
</html>
