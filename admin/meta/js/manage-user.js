$(document).ready(function() {
	$('#filteruser_form').submit(function() {
		var data = $('#filteruser_form').serialize();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=user&action=filter&'+data,
			success: function(msg){
				$('#flash-msg').removeClass('ajax-loading');
				$("#users-list").html(msg);
			}
		});
		return false;
	});
	$('#adduser_form').submit(function() {
		var data = $('#adduser_form').serialize();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$('#flash-msg').html('Loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=user&action=add&'+data,
			success: function(msg){
				updateUserList();
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-msg').html(msg);
			}
		});
		return false;
	});
	$("#users-list").ready( function() {
		updateUserList();
	});

});

function updateUserList(num_page, nb_items) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'user', 'action' : 'list', 'num_page': num_page, 'nb_items' : nb_items},
		success: function(msg){
			$("#users-list").html(msg);
		}
	});
}

function toggleUserStatus(user_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'user', 'action' : 'toggle', 'user_id' : user_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html(msg);
			updateUserList(num_page, nb_items);
		}
	});
}

function removeUser(user_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'user', 'action' : 'remove', 'user_id' : user_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html(msg);
			$('#removeConfirm_form').submit(function() {
				var data = $('#removeConfirm_form').serialize();
				$('#flash-log').css('display','');
				$('#flash-msg').addClass('ajax-loading');
				$('#flash-msg').html('Loading');
				$.ajax({
					type: "POST",
					url: "api/",
					data: 'ajax=user&action=removeConfirm&'+data,
					success: function(msg){
						updateUserList(num_page, nb_items);
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-msg').html(msg);
					}
				});
				return false;
			});
		}
	});
}

function removeSite(site_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'site', 'action' : 'remove', 'site_id' : site_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html(msg);
			$('#removeSiteConfirm_form').submit(function() {
				var data = $('#removeSiteConfirm_form').serialize();
				$('#flash-log').css('display','');
				$('#flash-msg').addClass('ajax-loading');
				$('#flash-msg').html('Loading');
				$.ajax({
					type: "POST",
					url: "api/",
					data: 'ajax=site&action=removeConfirm&'+data,
					success: function(msg){
						updateUserList(num_page, nb_items);
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-msg').html(msg);
					}
				});
				return false;
			});
		}
	});
	return false;
}

function profile(user_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data: {'ajax' : 'user', 'action' : 'profile', 'user_id' : user_id},
		dataType: 'json',
		success: function(user){
			$('#flash-msg').removeClass('ajax-loading');
			
			$('#user-edit-form #euser_id').val(user.user_id);
			$('#user-edit-form #euser_id').attr('disabled', 'true');
			$('#user-edit-form #efullname').val(unescape(user.user_fullname));
			$('#user-edit-form #eemail').val(user.user_email);
			var content = $('#user-edit-form form').clone();

			Boxy.askform(content, function(val) {
				val['ajax'] = "user";
				val['action'] = "update";
				val['user_id'] = $('#user-edit-form #euser_id').val();
				$.ajax({
					type: "POST",
					url: "api/",
					data : val,
					success: function(msg){
						$('#user-edit-form #euser_id').removeAttr('disabled');
						$('#user-edit-form #euser_id').val('');
						$('#user-edit-form #efullname').val('');
						$('#user-edit-form #eemail').val('');
						updateUserList(num_page, nb_items);
						$("#flash-msg").html(msg);
					}
				});
			}, {
				title: "Update "+user.user_id+" profile",
				closeable: true,
			});
		}
	});
	return false;
}


function addSite(user_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');

	$('#add-site-form #suser_id').val(user_id);
	$('#add-site-form #suser_id').attr('disabled', 'true');
	var content = $('#add-site-form form').clone();

	Boxy.askform(content, function(val) {
		val['ajax'] = "site";
		val['action'] = "add";
		val['user_id'] = $('#add-site-form #suser_id').val();
		$.ajax({
			type: "POST",
			url: "api/",
			data : val,
			success: function(msg){
				$('#add-site-form #suser_id').removeAttr('disabled');
				$('#flash-msg').removeClass('ajax-loading');
				$("#flash-msg").html(msg);
				$('#add-site-form #suser_id').removeAttr('disabled');
				$('#add-site-form #suser_id').val('');
				updateUserList(num_page, nb_items);
			}
		});
	}, {
		title: "Add site to "+user_id,
		closeable: true,
	});
	return false;
}

function editSite(site_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data: {'ajax' : 'site', 'action' : 'info', 'site_id' : site_id},
		dataType: 'json',
		success: function(site){
			$('#flash-msg').removeClass('ajax-loading');
			
			$('#site-edit-form #esite_name').val(unescape(site.site_name));
			$('#site-edit-form #esite_url').val(site.site_url);
			var content = $('#site-edit-form form').clone();

			Boxy.askform(content, function(val) {
				val['ajax'] = "site";
				val['action'] = "update";
				val['site_id'] = site_id;
				$.ajax({
					type: "POST",
					url: "api/",
					data : val,
					success: function(msg){
						$('#site-edit-form #esite_name').val('');
						$('#site-edit-form #esite_url').val('');
						updateUserList(num_page, nb_items);
						$("#flash-msg").html(msg);
					}
				});
			}, {
				title: "Update "+site.site_name+" site",
				closeable: true,
			});
		}
	});
	return false;
}

function openAdd() {
	jQuery('#adduser-field').css('display', '');
}

function closeAdd() {
	jQuery('#adduser-field').css('display', 'none');
}

function openFilter() {
	jQuery('#filteruser-field').css('display', '');
}

function closeFilter() {
	jQuery('#filteruser-field').css('display', 'none');
	updateUserList(0, 30);
}
