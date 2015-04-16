<!DOCTYPE html>
<html lang="en">
<head>
<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="{$planet.desc}" />
<title>{$params.title}</title>

<link rel="canonical" href="{$planet.url}" />
<!-- CSS
<link rel="stylesheet" type="text/css" title="default"	href="{$planet.url}/themes/{$planet.theme}/css/style.css" />
-->
<link rel="stylesheet" type="text/css" href="{$planet.url}/themes/{$planet.theme}/js/fancy/jquery.fancybox-1.3.1.css" />

<link rel="stylesheet" type="text/css"	href="{$planet.url}/themes/{$planet.theme}/css/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css"	href="{$planet.url}/themes/{$planet.theme}/css/jquery-ui.structure.min.css" />
<link rel="stylesheet" type="text/css"	href="{$planet.url}/themes/{$planet.theme}/css/jquery-ui.theme.min.css" />
<link rel="stylesheet" type="text/css"	href="{$planet.url}/themes/{$planet.theme}/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css"	href="{$planet.url}/themes/{$planet.theme}/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css"	href="{$planet.url}/themes/{$planet.theme}/css/offcanvas.css" />
<link rel="stylesheet" type="text/css"	href="{$planet.url}/themes/{$planet.theme}/css/sticky-footer.css" />
<!-- BEGIN css.import -->
	<link rel="stylesheet" type="text/css" href="{$planet.url}/{$css_file}"/>


<!-- END css.import -->

<!-- OTHERS -->
<link rel="alternate" type="application/rss+xml"  title="RSS"  href="{$planet.url}/feed.php?type=rss" />
<link rel="alternate" type="application/atom+xml" title="ATOM" href="{$planet.url}/feed.php?type=atom" />
<link rel="alternate" type="application/rss+xml"  title="Popular RSS"  href="{$planet.url}/feed.php?type=rss&popular=true&filter={$params.filter}" />
<!-- BEGIN feed.tags -->
<link rel="alternate" type="application/rss+xml"  title="RSS with filter"  href="{$planet.url}/feed.php?type=rss&tags={$params.tags}&users={$params.users}" />
<link rel="alternate" type="application/atom+xml" title="ATOM with filter" href="{$planet.url}/feed.php?type=atomi&tags={$params.tags}&users={$params.users}" />
<!-- END feed.tags -->
<link rel="alternate" type="application/atom+xml" title="Uncensored feed" href="{$planet.url}/feed.php?type=atom&uncensored=true" />

<link rel="icon" type="image/png" href="{$planet.url}/themes/{$planet.theme}/favicon.png" />

<!-- JS -->
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery-1.8.2.min.js"></script>
<!-- begin old -->
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery.boxy.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery.ba-outside-events.min.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/planet_fct.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/votes.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/fancy/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/fancy/jquery.easing-1.3.pack.js"></script>
<!-- end old -->
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="{$planet.url}/themes/{$planet.theme}/js/bootstrap.min.js"></script>
<!-- ADD JAVASCRIPT IMPORT HERE -->
<!-- BEGIN js.import -->
<script type="text/javascript" src="{$planet.url}/{$js_file}"></script>
<!-- END js.import -->

</head>
<body>
	<nav class="navbar navbar-default navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
	      <div class="container-fluid">
	        <div class="navbar-header">
	          <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
	            <span class="sr-only">Toggle navigation</span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a href="{$planet.url}" class="navbar-brand" title="{$planet.title}">
								<img alt="{$planet.title}" src="{$planet.url}/themes/{$planet.theme}/images/logo.png" id="logo" height="20"/>
						</a>
	        </div>
	        <div class="navbar-collapse collapse" id="navbar">
	          <ul class="nav navbar-nav">
							<!-- BEGIN menu.contact -->
								<li><a href="{$planet.url}/contact.php">{_Contact us}</a></li>
							<!-- END menu.contact -->
								<li><a href="{$planet.url}/charter.php">{_Charter}</a></li>
								<li><a href="{$planet.url}/users.php">{_Users}</a></li>
								<li><a href="{$planet.url}/tags.php">{_Tribes and tags}</a></li>
							<!-- BEGIN menu.votes -->
								<li><a href="{$planet.url}/stats.php">{_Stats}</a></li>
							<!-- END menu.votes -->
	          </ul>
						<!-- BEGIN search.box -->
						<form id="search_form" class="navbar-form navbar-right" role="search">
			        <div class="form-group">
								<!-- BEGIN search.popular -->
								<input type="hidden" id="popular" name="popular" class="form-control" placeholder="{_Search} ..."
									value="{$params.popular}" />
								<!-- END search.popular -->
								<!-- BEGIN search.user_id -->
								<input type="hidden" id="user_id" name="user_id" class="form-control" placeholder="{_Search} ..."
									value="{$params.user_id}" />
								<!-- END search.user_id -->
								<!-- BEGIN search.filter -->
								<input type="hidden" id="filter" name="filter" class="form-control" placeholder="{_Search} ..."
									value="{$params.filter}" />
								<!-- END search.filter -->

								<input type="text" id="search_text" class="form-control search-field" placeholder="{_Search} ..."
									name="search"
									 />
			        </div>
			        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
			      </form>
						<!-- END search.box -->
	          <ul class="nav navbar-nav navbar-right">
			        <li class="dropdown">
			          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-user fa-lg"></i> {$login.username} <span class="caret"></span></a>
			          <ul class="dropdown-menu" role="menu">
									<!-- BEGIN page.loginbox -->
										<!-- BEGIN menu.shaarli -->
											<li><a href="{$shaarli_instance}">{_My Shaarli}</a></li>
										<!-- END menu.shaarli -->
											<li><a href="{$planet.url}/user/">{_Dashboard}</a></li>
										<!-- BEGIN page.loginadmin -->
											<li><a href="{$planet.url}/admin/">{_Administration}</a></li>
										<!-- END page.loginadmin -->
											<li><a href="?logout={$planet.url}">{_Logout}</a></li>
									<!-- ELSE page.loginbox -->
											<li><a href="javascript:display_login()">{_Login}</a></li>
									<!-- BEGIN menu.subscription -->
											<li><a href="{$planet.url}/signup.php">{_Register}</a></li>
									<!-- END menu.subscription -->
									<!-- END page.loginbox -->
			          </ul>
			        </li>

	          </ul>
	        </div><!--/.nav-collapse -->
	      </div>
			</div>
	    </nav>
			<!-- BEGIN sidebar.action -->

			<!-- END sidebar.action -->

<div class="container-fluid">
	<div class="row row-offcanvas row-offcanvas-right">
		<div id="sidebar" class="col-xs-6 col-sm-3 sidebar-offcanvas">

				<!-- BEGIN user.menu -->
				{!include:'user/menu.tpl'}
			<!-- END user.menu -->
			<!-- BEGIN main.menu -->
				{!include:'menu.tpl'}
			<!-- END main.menu -->

		</div><!--/.sidebar-offcanvas-->
        <div class="col-xs-12 col-sm-9">
          <p class="pull-right visible-xs">
            <button data-toggle="offcanvas" class="btn btn-primary btn-xs" type="button">Toggle nav</button>
          </p>
          <div class="row">
						<div id="top-area" class="col-xs-12">
							<!-- BEGIN content.topbar -->
							{!include:'topbar.tpl'}
							<!-- END content.topbar -->
						</diV>

						<div id="main-body" class="col-xs-12">
							<!-- BEGIN content.posts -->
								{!include:'posts.tpl'}
							<!-- END content.posts -->

							<!-- BEGIN content.single -->
								{!include:'single.tpl'}
							<!-- END content.single -->

							<!-- BEGIN content.portal -->
								{!include:'portal.tpl'}
							<!-- END content.portal -->

							<!-- BEGIN content.users -->
								{!include:'users.tpl'}
							<!-- END content.users -->

							<!-- BEGIN content.tags -->
								{!include:'tags.tpl'}
							<!-- END content.tags -->

							<!-- BEGIN content.html -->
								{$html}
							<!-- END content.html -->

							<!-- BEGIN content.404 -->
								{!include:'404.tpl'}
							<!-- END content.404 -->

							<!-- BEGIN content.signup -->
								{!include:'signup.tpl'}
							<!-- END content.signup -->

							<!-- BEGIN content.charter -->
								{!include:'charter.tpl'}
							<!-- END content.charter -->

							<!-- BEGIN content.contact -->
								{!include:'contact.tpl'}
							<!-- END content.contact -->

							<!-- BEGIN content.stats -->
								{!include:'stats.tpl'}
							<!-- END content.stats -->

							<!-- BEGIN content.archives -->
								{!include:'archives.tpl'}
							<!-- END content.archives -->
						</diV>


          </div><!--/row-->
        </div><!--/.col-xs-12.col-sm-9-->

			</div>
  </div>

	{!include:'popup.tpl'}

	<!-- ADD FOOTER HERE -->
	{!include:'footer.tpl'}


</body>
</html>
