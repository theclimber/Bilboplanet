function updatePage(id, state) {
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
			showFlash(state);
			page_ready();
		}
	});
}

function showFlash(msg) {
	$('#flash-log').css('display','block');
	$('#flash-msg').html(msg);
}
