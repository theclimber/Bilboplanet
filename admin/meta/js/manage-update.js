$(document).ready(function($) {
	$('#form_manage-update').submit(function() {
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$('#flash-msg').html('Loading');
	});
	
	if($('#post_flash').val() != null) {
		$('#flash-msg').html('');
		$('#flash-msg').removeClass('ajax-loading');
		$('#flash-log').css('display', 'none');
		$('#post_flash').flashmsg();
		$('#post_flash').remove();
	}
});
