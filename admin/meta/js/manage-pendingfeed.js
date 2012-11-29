$(document).ready(function() {
	$("#pendingfeed-list").ready( function() {
		updatePendingFeedList();
	});
});

function updatePendingFeedList(num_page, nb_items) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'pendingfeed', 'action': 'list', 'num_page': num_page, 'nb_items': nb_items},
		success: function(msg){
			$("#pendingfeed-list").html(msg);
		}
	});
}

function refusePendingFeed(pUserId, siteUrl, feedUrl, userEmail, userFullname) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'pendingfeed', 'action': 'emailText', 'userfullname': userFullname, 'feedurl': feedUrl},
		success: function(msg){
			$('#refuse-subscription-form #content').text(msg);
			var content = $('#refuse-subscription-form form').clone();
			Boxy.askform(content, function(val) {
				var datav = content.serialize().split('&');
				var fsubject = datav[1].split('=')[1];
				var fcontent = datav[2].split('=')[1];
				$.ajax({
					type: "POST",
					url: "api/",
					data : {
						'ajax' : 'pendingfeed',
						'action': 'refuse',
						'puserid': pUserId,
						'useremail': userEmail,
						'feed_url' : feedUrl,
						'subject': fsubject,
						'content': fcontent
					},
					success: function(msg){
						$('#flash-msg').html('');
						$('#flash-log').css('display','');
						$('#flash-msg').removeClass('ajax-loading');
						updatePendingFeedList();
						$(msg).flashmsg();
					}
				});
			return false;
			});
		}
	});
	return false;
}

function acceptPendingFeed(pUserId, siteUrl, feedUrl, userEmail, userFullname) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'pendingfeed', 'action': 'emailText', 'userfullname': userFullname, 'feedurl': feedUrl, 'type': 'accept'},
		success: function(msg){
			$('#accept-subscription-form #content').text(msg);
			var content = $('#accept-subscription-form form').clone();
			Boxy.askform(content, function(val) {
				var datav = content.serialize().split('&');
				var fsubject = datav[1].split('=')[1];
				var fcontent = datav[2].split('=')[1];
				$.ajax({
					type: "POST",
					url: "api/",
					data : {
						'ajax' : 'pendingfeed',
						'action': 'accept',
						'puserid': pUserId,
						'useremail': userEmail,
						'userfullname': userFullname,
						'siteurl': siteUrl,
						'feedurl': feedUrl,
						'subject': fsubject,
						'content': fcontent
					},
					success: function(msg){
						$('#flash-msg').html('');
						$('#flash-log').css('display','');
						$('#flash-msg').removeClass('ajax-loading');
						updatePendingFeedList();
						$(msg).flashmsg();
					}
				});
				return false;
			});
		}
	});
	return false;
}
