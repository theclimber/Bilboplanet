function accept(post_id, nom) {
	Boxy.confirm("Merci de confirmer?", function(val) {
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$('#flash-msg').html('Loading');
		$("#action"+post_id)[0].setAttribute('class','ajax-loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data : {'ajax' : 'moderation', 'action': 'accept', 'post_id' : post_id},
			success: function(msg){
				var href = $("#line"+post_id)[0].childNodes[2].childNodes[0].href
				$("#line"+post_id)[0].removeAttribute('class', 'inactive');
				$("#line"+post_id)[0].setAttribute('class', 'active');
				$("#action"+post_id)[0].removeAttribute('class','ajax-loading');
				$("#action"+post_id).html('<span id="action' + post_id + '"><img src="meta/icons/true-light.png" title="Accepter" /> <a href="javascript:refuse(' + post_id + ', \'' + nom + '\', \'' + href + '\')"><img src="meta/icons/warn.png" title="Refuser" /></a></span>');
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-log').css('display', 'none');
				$(msg).flashmsg();
			}
		});
	}, {
		title: "Etes vous sur de vouloir accepter cet article",
	});
}
function refuse(post_id, user_id, permalink) {
	var text = "brol"
	$.ajax({
		type: "POST",
		url: "api/",
		data : {
			'ajax' : 'moderation',
			'action': 'emailtext',
			'user_id' : user_id,
			'permalink' : permalink
		},
		success: function(msg){
			$('#refuse-post-form #content').text(msg);
			var content = $('#refuse-post-form form').clone();
			
			Boxy.askform(content, function(val) {
				val['post_id'] = post_id;
				val['ajax'] = 'moderation';
				val['action'] = 'refuse';
				$('#flash-log').css('display','');
				$('#flash-msg').addClass('ajax-loading');
				$("#flash-msg").html('Sending');
				$("#action"+post_id)[0].setAttribute('class','ajax-loading');
				$.ajax({
					type: "POST",
					url: "api/",
					data : val,
					success: function(msg){
						$("#line"+post_id)[0].removeAttribute('class', 'active');
						$("#line"+post_id)[0].setAttribute('class', 'inactive');
						$("#action"+post_id)[0].removeAttribute('class','ajax-loading');
						$("#action"+post_id).html('<span id="action'+post_id+'"><a href="javascript:accept('+post_id+', \'' + user_id + '\')"><img src="meta/icons/true.png" title="Accepter" /></a> <img src="meta/icons/warn-light.png" title="Refuser" /></span>');
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-log').css('display', 'none');
						$(msg).flashmsg();
					}
				});
			}, {
				title: "Send an email to the user",
			});
		}
	});
}

$(document).ready(function() {
	$("#post-list").ready( function() {
		$.ajax({
			type: "POST",
			url: "api/",
			data : {'ajax' : 'moderation', 'action' : 'filter'},
			success: function(msg){
				$("#post-list").html(msg);
			}
		});
	});
	$("#filter-form").submit(function() {
		var $inputs = $('#filter-form :input');
		var values = {};
		$inputs.each(function() {
			values[this.name] = $(this).val();
		});
		$.ajax({
			type: "POST",
			url: "api/",
			data: values,
			success: function(msg){
				$("#post-list").html(msg);
			}
		});
		return false;
	});
});
