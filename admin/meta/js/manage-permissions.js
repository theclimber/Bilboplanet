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
						$('#flash-log').css('display', 'none');
						$(msg).flashmsg();
					}
				});
				return false;
			});
		}
	});
}

function toggleUserRole(nb, user_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');

	//var tagId = user_id;
	//tagId = replaceAll(tagId, "%", '___SPECIALCHAR___' );
	//tagId = replaceAll(tagId, '___SPECIALCHAR___', "\\\\%" );
	user_role = $('#role'+nb).val();
	//console.debug($('#role'+tagId));
	//console.debug(user_role);
	if (user_role != null ) {
		$.ajax({
			type: "POST",
			url: "api/",
			data : {'ajax' : 'permissions', 'action' : 'toggleRole', 'user_id' : user_id, 'user_role' : user_role},
			success: function(msg){
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-log').css('display', 'none');
				$(msg).flashmsg();
				updateUserList(num_page, nb_items);
			}
		});
	} else {
		alert('Error : user role is undefined. Please report the bug');
	}
}
function toggleUserPermission(nb, user_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');

	admin = 'unset';
	if($('input#admin'+nb+':checked').val()) {
		admin = 'set';
	}
	config = 'unset';
	if($('input#config'+nb+':checked').val()) {
		config = 'set';
	}
	moder = 'unset';
	if($('input#moder'+nb+':checked').val()) {
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
			$('#flash-log').css('display', 'none');
			$(msg).flashmsg();
			updateUserList(num_page, nb_items);
		}
	});
}

function replaceAll(strText, from, to) {
	var intIndexOfMatch = strText.indexOf( from );
	// Loop over the string value replacing out each matching
	// substring.
	while (intIndexOfMatch != -1){
		// Relace out the current instance.
		strText = strText.replace( from, to )
		// Get the index of any next matching substring.
		intIndexOfMatch = strText.indexOf( from );
	}
	 
	return strText;
}
