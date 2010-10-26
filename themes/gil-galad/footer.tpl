<!-- footer-->
<div id="footer">
		<a href="{$footer1.url}">{$footer1.text}</a>
		<a href="{$footer2.url}">{$footer2.text}</a>
		<a href="{$footer3.url}">{$footer3.text}</a>
		<a id="retour_top" href="#top"><img alt="{_Back to top}" src="themes/gil-galad/images/top.png"></a>
		<!-- BEGIN footer.widget -->
		<div class="footer-widget" id="widget{$footer-widget.id}">
			<h2 class="footer-widget" id="widget{$footer-widget.id}">{$footer-widget.title}</h2>
			{$footer-widget.html}
		</div>
		<!-- END footer.widget -->
</div>
