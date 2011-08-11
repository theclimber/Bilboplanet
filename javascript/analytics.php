<?php
#require_once(dirname(__FILE__).'../inc/prepend.php');

$analytics = $blog_settings->get('planet_analytics');

if ($analytics == "google-analytics") {
	$ga = $blog_settings->get('planet_ganalytics');
?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo $ga ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php
} elseif ($analytics == 'piwik') {
	$id = $blog_settings->get('piwik_id');
	$url = $blog_settings->get('piwik_url');
?>
<!-- Piwik -->
<script type="text/javascript">
	var pkBaseURL = (("https:" == document.location.protocol) ? "https://piwik.kiwais.com/" : "<?php echo $url; ?>");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", <?php echo $id;?>);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script>
<noscript><p><img src="<?php echo $url; ?>/piwik.php?idsite=<?php echo $id; ?>" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Tag -->
<?php
}
?>
