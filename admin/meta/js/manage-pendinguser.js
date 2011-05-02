$(document).ready(function() {
	$("#pendinguser-list").ready( function() {
		updatePendingUserList();
	});

});

function updatePendingUserList(num_page, nb_items) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'pendinguser', 'action': 'list', 'num_page': num_page, 'nb_items': nb_items},
		success: function(msg){
			$("#pendinguser-list").html(msg);
		}
	});
}

function refusePendingUser(pUserId) {
	$('#flash-log').css('display','')
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'pendinguser', 'action': 'refuse', 'puserid': pUserId},
		success: function(msg){
			$('#flash-msg').html('');
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-log').css('display','none');
			$(msg).flashmsg();
		}
	});					
}
