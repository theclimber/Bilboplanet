$(document).ready(function() {
	$('form#social_form').submit(function(){
		var id = $(this).attr('id');
		$.ajax({
			type: "POST",
			url: "api/",
			data: {
				'ajax' : 'social',
				'action' : 'update',
				'twitter' : $('input#twitter').is(':checked'),
				'google' : $('input#google').is(':checked'),
				'shaarli' : $('input#shaarli').is(':checked'),
				'statusnet' : $('input#statusnet').is(':checked'),
				'newsletter' : $('select#newsletter').val(),
				'statusnet-account' : $('input#statusnet-account').val(),
				'shaarli-instance' : $('input#shaarli-instance').val()
			},
			success: function(msg){
				updatePage('social');
			}
		});
		return false;
	});
	$('input#shaarli').change(function() {
		var checked = $('input#shaarli').is(':checked');
		if (checked) {
			$('input#shaarli-instance').removeAttr("disabled");
		} else {
			$('input#shaarli-instance').attr("disabled", "disabled");
		}
	});
	$('input#statusnet').change(function() {
		var checked = $('input#statusnet').is(':checked');
		if (checked) {
			$('input#statusnet-account').removeAttr("disabled");
		} else {
			$('input#statusnet-account').attr("disabled", "disabled");
		}
	});
});

