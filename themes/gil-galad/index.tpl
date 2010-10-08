<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="description" content="{$planet.desc_meta}" />
	<meta name="keywords" content="{$planet.keywords}" />
	<title>{$planet.title}</title>

	<link href="{$planet.url}/themes/{$planet.theme}/css/core.css" rel="stylesheet" type="text/css" />
	<link rel="alternate" type="application/rss+xml"  title="RSS"  href="{$planet.url}/feed.php?type=rss" />
	<link rel="alternate" type="application/atom+xml" title="ATOM" href="{$planet.url}/feed.php?type=atom" />
	<link rel="icon" type="image/ico" href="{$planet.url}/themes/{$planet.theme}/favicon.png" />

	<script type="text/javascript" src="{$planet.url}/javascript/jquery.js"></script>

	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/votes.js" ></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/mobile.js" ></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery.easing.1.3.js" ></script>
	<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery.fancybox-1.2.1.pack.js" ></script>

</head>
<body>
<div id="tour">
	<div id="header" class="header"><!--header-->
		<div id="logo">
			<h1 id="title"><a href="{$planet.url}">{$planet.title}</a></h1>
			<h2 id="description_title">{$planet.desc}</h2>
		</div>
	</div><!-- end header -->

	<div id="centre">

		<div class="contenu">
		<!-- ADD MENU HERE -->
		{!include:'menu.tpl'}

	<!-- BEGIN menu.filter -->
	<div id="tri">
		<ul>
			<li class="{$filter.day}">
				<a href="index.php?{$filter_url}filter=day">{_Posts of the day}</a>
			</li>
			<li class="{$filter.week}">
				<a href="index.php?{$filter_url}filter=week">{_Posts of the week}</a>
			</li>
			<li class="{$filter.month}">
				<a href="index.php?{$filter_url}filter=month">{_Posts of the month}</a>
			</li>
			<li>
				<a href="index.php?{$filter_url}">{_All posts}</a>
			</li>
		</ul>
	</div><!-- end submenu -->

	<!-- END menu.filter -->
			<div id="colonne">
				<!-- BEGIN search.box -->
				<div id="recherche">
					<form id="form_search" action="index.php" method="get">
						<!-- BEGIN search.popular -->
						<input type="hidden" id="popular" name="popular" value="{$params.popular}" />
						<!-- END search.popular -->
						<!-- BEGIN search.user_id -->
						<input type="hidden" id="user_id" name="user_id" value="{$params.user_id}" />
						<!-- END search.user_id -->
						<!-- BEGIN search.filter -->
						<input type="hidden" id="filter" name="filter" value="{$params.filter}" />
						<!-- END search.filter -->
						<input type="submit" id="recherche_global_btn" value="OK" />
						<input type="text" id="mainmenu_search" name="search" value="{$search_value}" />
					</form>
				</div>
				<!-- END search.box -->

				<!-- ADD SIDEBAR HERE -->
				{!include:'sidebar.tpl'}
			</div>

			<div id="centre_centre">
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

</div><!-- end wrap -->

<!-- ADD JAVASCRIPT IMPORT HERE -->
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->
</body>
</html>
