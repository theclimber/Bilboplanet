$(document).ready(function() {
	$('#sideMenu li').click(function(e){
		var id = $(this).attr('id');
		updatePage(id);
	});
});

function updatePage(id) {
	$.ajax({
		type: "POST",
		url: "api/",
		data: {
			'ajax' : 'main',
			'action' : 'page',
			'page' : id
		},
		success: function(msg){
			$('div#mainContent').fadeTo('fast', 0.1, function(){});
			$('#sideMenu li').attr('class', '');
			$('#sideMenu li#'+id).attr('class', 'selected');
			$('div#mainContent').html(msg);
			$('div#mainContent').fadeTo('fast', 1, function(){});
		}
	});
}
