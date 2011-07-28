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

function refusePendingUser(pUserId, siteUrl, feedUrl, userEmail, userFullname) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'pendinguser', 'action': 'emailText', 'userfullname': userFullname, 'feedurl': feedUrl},
		success: function(msg){
			$('#refuse-subscription-form #content').text(msg);
			var content = $('#refuse-subscription-form form').clone();
			Boxy.askform(content, function(val) {
				$.ajax({
					type: "POST",
					url: "api/",
					data : {'ajax' : 'pendinguser', 'action': 'refuse', 'puserid': pUserId, 'useremail': userEmail},
					success: function(msg){
						$('#flash-msg').html('');
						$('#flash-log').css('display','');
						$('#flash-msg').removeClass('ajax-loading');
						updatePendingUserList();
						$(msg).flashmsg();
					}
				});
			return false;
			});
		}
	});
	return false;
}

function acceptPendingUser(pUserId, siteUrl, feedUrl, userEmail, userFullname) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'pendinguser', 'action': 'emailText', 'userfullname': userFullname, 'feedurl': feedUrl, 'type': 'accept'},
			success: function(msg){
				$('#accept-subscription-form #content').text(msg);
				var content = $('#accept-subscription-form form').clone();
				Boxy.askform(content, function(val) {
					$.ajax({
						type: "POST",
						url: "api/",
						data : {'ajax' : 'pendinguser', 'action': 'accept', 'puserid': pUserId, 'useremail': userEmail, 'userfullname': userFullname, 'siteurl': siteUrl, 'feedurl': feedUrl},
						success: function(msg){
						$('#flash-msg').html('');
						$('#flash-log').css('display','');
						$('#flash-msg').removeClass('ajax-loading');
						updatePendingUserList();
						$(msg).flashmsg();
					}
				});
			return false;
			});
		}
	});
	return false;
}
