//var BP_Admin;
window.addEvent('domready', function(){
	$('BP_Logout').addEvent('click', function(event){
		event.stop();
		window.location.replace('<?php echo $planet_url; ?>');
	});
	$('BP_About').addEvent('click', function(event){
		event.stop();
		var message = '<p>'+
			'<h3><?php echo T_(\'Bilboplanet 0.3 was developed by\');?></h3>'+
			'<ul>'+
			'<li>Gregoire de Hemptinne (<a href="http://www.theclimber.be" target="_blank">http://www.theclimber.be</a>)</li>'+
			'<li>Thomas Bourcey (<a href="http://www.sckyzo.com" target="_blank">http://www.sckyzo.com</a>)</li>'+
			'<li>Jonas Luthi (<a href="http://jonasluthi.com" target="_blank">http://jonasluthi.com</a>)</li>'+
			'</ul>'+
			'</p>'+
			'<br /><hr><br/>'+
			'<span><?php echo T_(\'BilboPlanet.com - Open Source Feed Agregator - 2009\');?></span>';
		$alert(message, '<?php echo T_(\'About\');?>');
	});
});

