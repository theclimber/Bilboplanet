$(document).ready(function() {
	page_ready();
});

function page_ready() {
	selectChange();
	if (!$('input#shaarli').is(':checked')) {
		$('div#shaarli-details').css('display','none');
	}
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
				'shaarli-instance' : $('input#shaarli-instance').val(),
				'shaarli-type' : $('select#shaarli-type').val(),
				'reddit' : $('input#reddit').is(':checked')
			},
			success: function(msg){
				updatePage('social', msg);
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
			$('p#statusnet-input').css("display", "");
			$('input#statusnet-account').removeAttr("disabled");
		} else {
			$('input#statusnet-account').attr("disabled", "disabled");
			$('p#statusnet-input').css("display", "none");
		}
	});
	if ($('input#statusnet').is(':checked')) {
		$('p#statusnet-input').css("display", "");
	}
}

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
