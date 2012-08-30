$(document).ready(function() {
	selectChange();
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
			$('div#shaarli-details').css('display','block');
			selectChange();
		} else {
			$('div#shaarli-details').css('display','none');
		}
	});
	$('select#shaarli-type').change(function() {
		selectChange();
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

function selectChange() {
	var value = $('select#shaarli-type').attr('value');
	if (value == 'remote') {
		$('p#shaarli-remote-instance').css('display','block');
		$('p#shaarli-local-instance').css('display','none');
	} else {
		$('p#shaarli-local-instance').css('display','block');
		$('p#shaarli-remote-instance').css('display','none');
	}
}
