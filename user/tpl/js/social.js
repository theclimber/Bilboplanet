$(document).ready(function() {
	$('form#social_form').submit(function(e){
		var id = $(this).attr('id');
		$.ajax({
			type: "POST",
			url: "api/",
			data: {
				'ajax' : 'main',
				'action' : 'page'
			},
			success: function(msg){
			}
		});
	});
});

