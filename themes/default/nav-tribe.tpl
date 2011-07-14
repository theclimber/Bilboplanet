
	<div id="mainNav">
		<div class="topNavMenu">
			<ul>
				<li {$topNavSelected.1}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-search.png"></a></li>
				<li {$topNavSelected.2}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-rss.png"></a></li>
				<li {$topNavSelected.3}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-twitter.png"></a></li>
				<li {$topNavSelected.4}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-mail.png"></a></li>
				<li {$topNavSelected.5}><a href="#"><img src="{$planet.url}/themes/{$planet.theme}/images/menu-info.png"></a></li>
			</ul>
			<!-- BEGIN search.box -->
			<form class="search">
				<div id="triangle"></div>
				<input class="box" type="search" name="search" placeholder="Search" value="{$search_value}" />
				<input type="submit" value="OK" />
			</form>
			<!-- END search.box -->
		</div>
		<ul class="tabMenu">
			<!-- BEGIN tribe.menuEntry -->
			<li {$cSelected}><a href="{$planet.url}/?page=tribe&id={$tribeEntry.id}">{$tribeEntry.name}</a></li>
			<!-- END tribe.menuEntry -->
		</ul>
	</div>
	<div id="widgetNav">
		<!-- BEGIN widget.cloud.tag -->
		<div class="widget">
			<h1>Tag cloud</h1>
			<ul class="cloud">
				<!-- BEGIN cloud.tag -->
				<li><a href="#" class="size{$tag.weight}"><tag>{$tag.id}</tag></a></li>
				<!-- END cloud.tag -->
			</ul>
		</div>
		<!-- END widget.cloud.tag -->

		<!-- BEGIN widget.cloud.user -->
		<div class="widget">
			<h1>User cloud</h1>
			<ul class="cloud">
				<!-- BEGIN cloud.user -->
				<li><a href="#" class="size{$user.weight}"><user>{$user.name}</user></a></li>
				<!-- END cloud.user -->
			</ul>
		</div>
		<!-- END widget.cloud.user -->
	</div>
