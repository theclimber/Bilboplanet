$(document).ready(function() {
	$('form#social_form').submit(function(){
		var id = $(this).attr('id');
		var sdata = $('form#social_form').serialize();
		sdata += '&ajax=social'
		sdata += '&action=update'
		$.ajax({
			type: "POST",
			url: "api/",
			data: sdata,
			success: function(msg){
				//updatePage('social');
			}
		});
		return false;
	});
});

