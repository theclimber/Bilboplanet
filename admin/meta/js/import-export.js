$(document).ready(function() {
	// Pour le formulaire d'export
	$('#exportform').submit(function() {
		var data = $('#exportform').serialize();
		$('#export-log').css('display','');
		$('#export_res').addClass('ajax-loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=database&action=export&'+data,
			success: function(msg){
				$('#export_res').removeClass('ajax-loading');
				$('#export_res').html(msg);
			}
		});
		return false;
	});
});
