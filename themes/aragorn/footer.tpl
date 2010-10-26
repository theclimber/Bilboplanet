<!-- footer-->
<div id="footer">
	<div class="column1">
		<a href="{$footer1.url}">{$footer1.text}</a>
	</div>
	<div class="column2">
		<a href="{$footer2.url}">{$footer2.text}</a>
	</div>
	<div class="column3">
		<a href="{$footer3.url}">{$footer3.text}</a>
	</div>
		<!-- BEGIN footer.widget -->
		<div class="footer-widget" id="widget{$footer-widget.id}">
			<h2 class="footer-widget" id="widget{$footer-widget.id}">{$footer-widget.title}</h2>
			{$footer-widget.html}
		</div>
		<!-- END footer.widget -->
</div>
