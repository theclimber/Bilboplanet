$(document).ready(function() {
	$('#filterfeed_form').submit(function() {
		var data = $('#filterfeed_form').serialize();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=feed&action=filter&'+data,
			success: function(msg){
				$('#flash-msg').removeClass('ajax-loading');
				$("#feed-list").html(msg);
			}
		});
		return false;
	});
	$('#addfeed_form').submit(function() {
		var data = $('#addfeed_form').serialize();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=feed&action=add&'+data,
			success: function(msg){
				updateFeedList();
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-msg').html(msg);
			}
		});
		return false;
	});
	$("#feed-list").ready( function() {
		updateFeedList();
	});
});

function updateFeedList(num_page, nb_items) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'feed', 'action' : 'list', 'num_page': num_page, 'nb_items' : nb_items},
		success: function(msg){
			$("#feed-list").html(msg);
		}
	});
}

function updateSiteCombo() {
	var user_id = $("select#user_id").val();
	if (user_id == null){
		alert('user_id can not be null')
	}
	else {
		$.ajax({
			type: "POST",
			url: "api/",
			data : {'ajax' : 'site', 'action' : 'get-user-site', 'user_id': user_id},
			dataType: 'json',
			success: function(sites){
				$("select#site_id").html('');
				$.each(sites, function(site_id, value) {
					$("select#site_id").append($('<option></option>').val(site_id).html(value.site_url));
				});
			}
		});
	}
}

function toggleFeedStatus(feed_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'feed', 'action' : 'toggle', 'feed_id' : feed_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html(msg);
			updateFeedList(num_page, nb_items);
		}
	});
}

function toggleFeedTrust(feed_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'feed', 'action' : 'change-trust', 'feed_id' : feed_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html(msg);
			updateFeedList(num_page, nb_items);
		}
	});
}

function removeFeed(feed_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'feed', 'action' : 'remove', 'feed_id' : feed_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html(msg);
			$('#removeFeedConfirm_form').submit(function() {
				var data = $('#removeFeedConfirm_form').serialize();
				$('#flash-log').css('display','');
				$('#flash-msg').addClass('ajax-loading');
				$.ajax({
					type: "POST",
					url: "api/",
					data: 'ajax=feed&action=removeConfirm&'+data,
					success: function(msg){
						updateFeedList(num_page, nb_items);
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-msg').html(msg);
					}
				});
				return false;
			});
		}
	});
}

function edit(feed_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data: {'ajax' : 'feed', 'action' : 'get', 'feed_id' : feed_id},
		dataType: 'json',
		success: function(feed){
			$('#flash-msg').removeClass('ajax-loading');
			
			$('#feed-edit-form #ef_id').val(feed.feed_id);
			$('#feed-edit-form #ef_user_id').val(feed.user_id);
			$('#feed-edit-form #ef_user_id').attr('disabled', 'true');
			$('#feed-edit-form #ef_name').val(feed.feed_name);
			$('#feed-edit-form #ef_url').val(feed.feed_url);
			var content = $('#feed-edit-form form').clone();

			Boxy.askform(content, function(val) {
				val['ajax'] = "feed";
				val['action'] = "edit";
				$.ajax({
					type: "POST",
					url: "api/",
					data : val,
					success: function(msg){
						$('#feed-edit-form #ef_user_id').removeAttr('disabled');
						$('#feed-edit-form #ef_user_id').val('');
						$('#feed-edit-form #ef_id').val('');
						$('#feed-edit-form #ef_name').val('');
						$('#feed-edit-form #ef_url').val('');
						updateFeedList(num_page, nb_items);
						$("#flash-msg").html(msg);
					}
				});
			}, {
				title: "Update "+feed.user_id+" feeds",
				closeable: true,
			});
		}
	});
	return false;
}
