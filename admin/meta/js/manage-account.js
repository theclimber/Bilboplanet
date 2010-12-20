$(document).ready(function() {
	$('#account-edit-form #euser_id').attr('disabled', 'true');
	$('#account-edit-form').submit(function() {
		var data = $('#account-edit-form').serialize();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=account&action=update&'+data,
			success: function(msg){
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-log').css('display', 'none');
				$(msg).flashmsg();
			}
		});
		return false;
	});
});
