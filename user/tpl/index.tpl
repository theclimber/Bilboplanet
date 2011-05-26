<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta name="description" content="{$planet.desc_meta}" />
	<meta name="keywords" content="{$planet.keywords}" />
	<title>{$planet.title} - {$title}</title>

	<link href="{$planet.url}/user/tpl/css/blueprint/screen.css" rel="stylesheet" type="text/css" />
	<link href="{$planet.url}/user/tpl/css/core.css" rel="stylesheet" type="text/css" />
	<link href="{$planet.url}/user/tpl/css/boxy.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="{$planet.url}/javascript/jquery.js"></script>
	<script type="text/javascript" src="{$planet.url}/user/tpl/js/main.js" ></script>
</head>
<body>

<div id="wrapper">
	<div id="sideMenu">
		<ul>
			<li id="dashboard" class="{$menu.dashboard}"><span class="menuItem" id="dashboard">{_Dashboard}</span></li>
			<li id="profile" class="{$menu.profile}"><span class="menuItem" id="profile">{_Profile}</span></li>
			<li id="social" class="{$menu.social}"><span class="menuItem" id="social">{_Social}</span></li>
			<li id="write" class="{$menu.write}"><span class="menuItem" id="write">{_Write or share}</span></li>
			<li id="tribes" class="{$menu.tribes}" style="display:none"><span class="menuItem" id="tribes">{_Tribes}</span></li>
		</ul>
	</div>
	<div id="mainContent">
	{$html}
	</div>
</div>
</body>
</html>
