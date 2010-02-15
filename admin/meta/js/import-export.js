window.addEvent('domready', function(){
	// Pour le formulaire d'export
	$('exportform').addEvent('submit', function(e) {
		e.stop();
		//Empty the log and show the spinning indicator.
		var box = $('export-log').setStyle('display','');
		var log = $('export_res').empty().addClass('ajax-loading');
		this.set('send', {onComplete: function(response) { 
			log.removeClass('ajax-loading');
			log.set('html', response);
		}});
		this.send();
	});
});

