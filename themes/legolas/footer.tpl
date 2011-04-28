<!-- footer-->
<div id="footer">
	<div id="left">
		<span class="brand">{$planet.title}</span>
		<a href="{$footer1.url}">{$footer1.text}</a>
	</div>
	<div id="right">
		<div class="span-4 append-1 last">
			<h2>{_Contribute}</h2>
			<ul>
				<li><a href="{$planet.url}/inscription.php" title="{_Subscribe your blog to the planet}" rel="nofollow">
				{_Add your blog}</a></li>
				<li><a href="{$planet.url}/admin" title="{_Admin interface}" rel="nofollow">
				{_Administration}</a></li>
			</ul>
		</div>
		<div class="span-4 last">
			<h2>{_About Bilboplanet}</h2>
			<ul>
				<li><a href="{$footer2.url}">{$footer2.text}</a></li>
				<li><a href="{$footer3.url}">{$footer3.text}</a></li>
			</ul>
		</div>

		<!-- BEGIN footer.widget -->
		<div class="footer-widget" id="widget{$footer-widget.id}">
			<h2 class="footer-widget" id="widget{$footer-widget.id}">{$footer-widget.title}</h2>
			{$footer-widget.html}
		</div>
		<!-- END footer.widget -->
	</div>
</div>
