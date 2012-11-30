this.isfilter = false;
$(document).ready(function() {
	this.isfilter=false;
	$('#filterfeed_form').submit(function() {
		updateFilterList();
		return false;
	});
	$('#addfeed_form').submit(function() {
		var data = $('#addfeed_form').serialize();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$('#flash-msg').html('Loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=feed&action=add&'+data,
			success: function(msg){
				$('#addfeed-field').css('display', 'none');
				$('#addfeed_form').find('input[type=text]').val('');
				$('#user_id').val('');
				$('#site_id').val('');
				updateFeedList();
				$('#flash-msg').html('');
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-log').css('display', 'none');
				$(msg).flashmsg();
			}
		});
		return false;
	});
	$('#adduser_form').submit(function() {
		var data = $('#adduser_form').serialize();
		var user_id = $('#adduser_form input#user_id').val();
		var user_fn = $('#adduser_form input#fullname').val();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$('#flash-msg').html('Loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=user&action=add&'+data,
			success: function(msg){
				$('#adduser-field').css('display', 'none');
				$('#adduser_form').find('input[type=text], input[type=password]').val('');
				updateUserList();
				addUserToCombo(user_id,user_fn);
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-log').css('display', 'none');
				$(msg).flashmsg();
			}
		});
		return false;
	});
	$("#feed-list").ready( function() {
		updateFeedList();
	});
});

function addUserToCombo(user_id, user_fullname) {
	var html = '<option value="'+user_id+'">'+user_fullname+'</option>';
	$('select#user_id').append(html);
//	console.debug(user_id+" "+user_fullname);
}


function updateFilterList() {
	var data = $('#filterfeed_form').serialize();
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data: 'ajax=feed&action=filter&'+data,
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-log').css('display', 'none');
			$("#feed-list").html(msg);
		}
	});
}

function updateFeedList(num_page, nb_items) {
	if (this.isfilter) {
		updateFilterList();
	} else {
		$.ajax({
			type: "POST",
			url: "api/",
			data : {'ajax' : 'feed', 'action' : 'list', 'num_page': num_page, 'nb_items' : nb_items},
			success: function(msg){
				$("#feed-list").html(msg);
			}
		});
	}
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
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'feed', 'action' : 'toggle', 'feed_id' : feed_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-log').css('display', 'none');
			updateFeedList(num_page, nb_items);
			$(msg).flashmsg();
		}
	});
}

function toggleFeedTrust(feed_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'feed', 'action' : 'change-trust', 'feed_id' : feed_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-log').css('display', 'none');
			updateFeedList(num_page, nb_items);
			$(msg).flashmsg();
		}
	});
}

function removeFeed(feed_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'feed', 'action' : 'remove', 'feed_id' : feed_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html('');
			$(msg).flashmsg();
			$('#removeFeedConfirm_form').submit(function() {
				var data = $('#removeFeedConfirm_form').serialize().split('=');
				$('#flash-msg').addClass('ajax-loading');
				$('#flash-msg').html('Loading');
				$.ajax({
					type: "POST",
					url: "api/",
					data : {'ajax' : 'feed', 'action' : 'removeConfirm', 'feed_id' : data[1]},
					success: function(msg){
						updateFeedList(num_page, nb_items);
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-msg').html('');
						$('#flash-log').css('display', 'none');
						updateFeedList(num_page, nb_items);
						$(msg).flashmsg();
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
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data: {'ajax' : 'feed', 'action' : 'get', 'feed_id' : feed_id},
		dataType: 'json',
		success: function(feed){
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
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-log').css('display', 'none');
						updateFeedList(num_page, nb_items);
						$(msg).flashmsg();
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

function openAdd() {
	jQuery('#addfeed-field').css('display', '');
}

function openUserAdd() {
	jQuery('#adduser-field').css('display', '');
}

function closeAdd() {
	jQuery('#addfeed-field').css('display', 'none');
}

function closeUserAdd() {
	jQuery('#adduser-field').css('display', 'none');
}

function openFilter() {
	this.isfilter = true;
	jQuery('#filterfeed-field').css('display', '');
}

function closeFilter() {
	this.isfilter = false;
	jQuery('#filterfeed-field').css('display', 'none');
	updateFeedList(0, 30);
}

function rm_tag(num_page, nb_items, feed_id, tag) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'feed', 'action' : 'rm_tag', 'feed_id' : feed_id, 'tag' : tag},
        success: function(msg){
            $("#tag_action"+feed_id)[0].removeAttribute('class','ajax-loading');
            $('#flash-msg').removeClass('ajax-loading');
            $('#flash-msg').html('');
            $('#flash-log').css('display', 'none');
			updateFeedList(num_page, nb_items);
            $(msg).flashmsg();
        }
    });
}

function add_tags(num_page, nb_items, feed_id, feed_name) {
    var content = $('#tag-feed-form form').clone();

    Boxy.askform(content, function(val) {
        $('#flash-log').css('display','');
        $('#flash-msg').addClass('ajax-loading');
        $("#flash-msg").html('Sending');
        $("#tag_action"+feed_id)[0].setAttribute('class','ajax-loading');
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'feed', 'action' : 'add_tags', 'feed_id' : feed_id, 'tags' : data[1]},
            success: function(msg){
                $("#tag_action"+feed_id)[0].removeAttribute('class','ajax-loading');
                $('#flash-msg').removeClass('ajax-loading');
                $('#flash-log').css('display', 'none');
                $(msg).flashmsg();
				updateFeedList(num_page, nb_items);
            }
        });
    }, {
        title: "Tagging : " + feed_name,
    });
}
function toggleFeedComment(feed_id, comment_status, num_page, nb_items) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'feed',
			'action' : 'comment',
			'feed_id' : feed_id,
			'status': comment_status},
        success: function(msg){
			updateFeedList(num_page, nb_items);
        }
    });
}
