$(document).ready(function() {
	$("#users-list").ready( function() {
		updateUserList();
	});

});

function updateUserList(num_page, nb_items) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'permissions', 'action' : 'list', 'num_page': num_page, 'nb_items' : nb_items},
		success: function(msg){
			$("#users-list").html(msg);
			$('form.managerPerm').submit(function() {
				var data = $('form.managerPerm').serialize();
				$('#flash-log').css('display','');
				$('#flash-msg').addClass('ajax-loading');
				$('#flash-msg').html('Loading');
				$.ajax({
					type: "POST",
					url: "api/",
					data: 'ajax=permissions&action=togglePerms&'+data,
					success: function(msg){
						updateUserList();
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-msg').html(msg);
					}
				});
				return false;
			});
		}
	});
}

function toggleUserRole(user_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	user_role = $('#role'+user_id).val()
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'permissions', 'action' : 'toggleRole', 'user_id' : encodeURIComponent(user_id), 'user_role' : user_role},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html(msg);
			updateUserList(num_page, nb_items);
		}
	});
}
function toggleUserPermission(user_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');

	admin = 'unset';
	if($('input#admin'+user_id+':checked').val()) {
		admin = 'set';
	}
	config = 'unset';
	if($('input#config'+user_id+':checked').val()) {
		config = 'set';
	}
	moder = 'unset';
	if($('input#moder'+user_id+':checked').val()) {
		moder = 'set';
	}
	
	$.ajax({
		type: "POST",
		url: "api/",
		data : {
			'ajax' : 'permissions',
			'action' : 'togglePerms',
			'user_id' : user_id,
			'moder' : moder,
			'config' : config,
			'admin' : admin
			},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html(msg);
			updateUserList(num_page, nb_items);
		}
	});
}

