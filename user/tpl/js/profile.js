$(document).ready(function() {
	$('form#profile_form').submit(function() {
		var data = $('form#profile_form').serialize();
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=account&action=update&'+data,
			success: function(msg){
				//updatePage('profile');
			}
		});
		return false;
	});
});

