function updatePage(id) {
	$('div#main-body').fadeTo('fast', 0.1, function(){});
	$.ajax({
		type: "POST",
		url: "api/",
		data: {
			'ajax' : 'main',
			'action' : 'page',
			'page' : id
		},
		success: function(msg){
			$('div#main-body').html(msg);
			$('div#main-body').fadeTo('fast', 1, function(){});
		}
	});
}
