<!-- footer -->
<div id="bottombar-bg">
<!-- Bottom Bar -->
	<div id="bottombar">
	<!-- Powered By -->
	<span id="powered-by">Powered by <a href="{$footer1.url}">BilboPlanet</a></span>
	<!-- Copyright -->
	</div>
</div>
	<!-- BEGIN footer.widget -->
	<div class="footer-widget" id="widget{$footer-widget.id}">
		<h2 class="footer-widget" id="widget{$footer-widget.id}">{$footer-widget.title}</h2>
		{$footer-widget.html}
	</div>
	<!-- END footer.widget -->


{$analytics_html}

<!-- BEGIN social.google.script -->
<!-- Place this tag after the last share tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
<!-- END social.google.script -->

<!-- BEGIN social.twitter.script -->
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<!-- END social.twitter.script -->

<!-- BEGIN social.shaarli.script -->
<script type="text/javascript">
function shaare(id,title) {
	var url = '{$planet.url}/?post_id='+id;
//	var title = url;
	window.open(
		'{$shaarli_instance}/index.php?post=' +
		encodeURIComponent(url)+'&title=' +
		title+'&source=bookmarklet',
		'_blank',
		'menubar=no,height=390,width=600,toolbar=no,scrollbars=no,status=no'
		);
}
</script>
<!-- END social.shaarli.script -->
