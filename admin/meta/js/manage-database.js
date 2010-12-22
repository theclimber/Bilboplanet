$(document).ready(function($) {
	if($('#post_flash').val() != null) {
		$('#post_flash').flashmsg();
		$('#post_flash').remove();
	}
});
