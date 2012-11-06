this.isfilter = false;
$(document).ready(function() {
	this.isfilter=false;
	$('#addtribe_form').submit(function() {
		var data = $('#addtribe_form').serialize();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$('#flash-msg').html('Loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=tribe&action=add&'+data,
			success: function(msg){
				$('#addtribe-field').css('display', 'none');
				$('#addtribe_form').find('input[type=text]').val('');
				$('#user_id').val('');
				$('#tribe_name').val('');
				$('#ordering').val('');
				updateTribeList();
				$('#flash-msg').html('');
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-log').css('display', 'none');
				$(msg).flashmsg();
			}
		});
		return false;
	});
	$("#tribe-list").ready( function() {
		updateTribeList();
	});
});

function updateTribeList(num_page, nb_items) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'tribe', 'action' : 'list', 'num_page': num_page, 'nb_items' : nb_items},
		success: function(msg){
			$("#tribe-list").html(msg);
		}
	});
}

function toggleTribeVisibility(tribe_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'tribe', 'action' : 'toggle', 'tribe_id' : tribe_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-log').css('display', 'none');
			updateTribeList(num_page, nb_items);
			$(msg).flashmsg();
		}
	});
}


function removeTribe(tribe_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'tribe', 'action' : 'remove', 'tribe_id' : tribe_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html('');
			$(msg).flashmsg();
			$('#removeTribeConfirm_form').submit(function() {
				var data = $('#removeTribeConfirm_form').serialize().split('=');
				$('#flash-msg').addClass('ajax-loading');
				$('#flash-msg').html('Loading');
				$.ajax({
					type: "POST",
					url: "api/",
					data : {'ajax' : 'tribe', 'action' : 'removeConfirm', 'tribe_id' : data[1]},
					success: function(msg){
						updateTribeList(num_page, nb_items);
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-msg').html('');
						$('#flash-log').css('display', 'none');
						updateTribeList(num_page, nb_items);
						$(msg).flashmsg();
					}
				});
				return false;
			});
		}
	});
}

function edit(tribe_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data: {'ajax' : 'tribe', 'action' : 'get', 'tribe_id' : tribe_id},
		dataType: 'json',
		success: function(tribe){
			$('#tribe-edit-form #tribe_id').val(tribe.tribe_id);
			$('#tribe-edit-form #tribe_name').val(tribe.tribe_name);
			$('#tribe-edit-form #tribe_order').val(tribe.ordering);
			var content = $('#tribe-edit-form form').clone();

			Boxy.askform(content, function(val) {
				val['ajax'] = "tribe";
				val['action'] = "edit";
				$.ajax({
					type: "POST",
					url: "api/",
					data : val,
					success: function(msg){
						$('#tribe-edit-form #tribe_id').val('');
						$('#tribe-edit-form #tribe_name').val('');
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-log').css('display', 'none');
						$(msg).flashmsg();
						updateTribeList(num_page, nb_items);
					}
				});
			}, {
				title: "Update tribe",
				closeable: true
			});
			return false;
		}
	});
}

function openAdd() {
	jQuery('#addtribe-field').css('display', '');
}

function closeAdd() {
	jQuery('#addtribe-field').css('display', 'none');
}

function rm_tag(num_page, nb_items, tribe_id, tag) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribe', 'action' : 'rm_tag', 'tribe_id' : tribe_id, 'tag' : tag},
        success: function(msg){
//            $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
            $('#flash-msg').removeClass('ajax-loading');
            $('#flash-msg').html('');
            $('#flash-log').css('display', 'none');
			updateTribeList(num_page, nb_items);
            $(msg).flashmsg();
        }
    });
}

function add_tags(num_page, nb_items, tribe_id, tribe_name) {
    var content = $('#tag-tribe-form form').clone();

    Boxy.askform(content, function(val) {
        $('#flash-log').css('display','');
        $('#flash-msg').addClass('ajax-loading');
        $("#flash-msg").html('Sending');
//        $("#tag_action"+tribe_id)[0].setAttribute('class','ajax-loading');
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribe', 'action' : 'add_tags', 'tribe_id' : tribe_id, 'tags' : data[1]},
            success: function(msg){
//                $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
                $('#flash-msg').removeClass('ajax-loading');
                $('#flash-log').css('display', 'none');
                $(msg).flashmsg();
				updateTribeList(num_page, nb_items);
            }
        });
    }, {
        title: "Tagging : " + tribe_name,
    });
}

function rm_notag(num_page, nb_items, tribe_id, notag) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribe', 'action' : 'rm_notag', 'tribe_id' : tribe_id, 'tag' : notag},
        success: function(msg){
//            $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
            $('#flash-msg').removeClass('ajax-loading');
            $('#flash-msg').html('');
            $('#flash-log').css('display', 'none');
			updateTribeList(num_page, nb_items);
            $(msg).flashmsg();
        }
    });
}

function add_notags(num_page, nb_items, tribe_id, tribe_name) {
    var content = $('#tag-tribe-form form').clone();

    Boxy.askform(content, function(val) {
        $('#flash-log').css('display','');
        $('#flash-msg').addClass('ajax-loading');
        $("#flash-msg").html('Sending');
//        $("#tag_action"+tribe_id)[0].setAttribute('class','ajax-loading');
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribe', 'action' : 'add_notags', 'tribe_id' : tribe_id, 'tags' : data[1]},
            success: function(msg){
//                $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
                $('#flash-msg').removeClass('ajax-loading');
                $('#flash-log').css('display', 'none');
                $(msg).flashmsg();
				updateTribeList(num_page, nb_items);
            }
        });
    }, {
        title: "Tagging unwanted : " + tribe_name,
    });
}

function rm_user(num_page, nb_items, tribe_id, user) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribe', 'action' : 'rm_user', 'tribe_id' : tribe_id, 'user' : user},
        success: function(msg){
//            $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
            $('#flash-msg').removeClass('ajax-loading');
            $('#flash-msg').html('');
            $('#flash-log').css('display', 'none');
			updateTribeList(num_page, nb_items);
            $(msg).flashmsg();
        }
    });
}

function add_users(num_page, nb_items, tribe_id, tribe_name) {
    var content = $('span#user-tribe-form').clone();
	content.find('select#user_combo').change(function() {
		var user_id = content.find('select#user_combo').val();
		var current_text = content.find('input#users_selected').val();
		if (current_text=='') {
			content.find('input#users_selected').val(user_id)
		} else {
			content.find('input#users_selected').val(current_text + ',' + user_id)
		}
	});

    Boxy.askform(content, function(val) {
        $('#flash-log').css('display','');
        $('#flash-msg').addClass('ajax-loading');
        $("#flash-msg").html('Sending');
		var data = content.find('form').serialize().split('=');
		console.debug(data);
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribe', 'action' : 'add_users', 'tribe_id' : tribe_id, 'users' : data[1]},
            success: function(msg){
                $('#flash-msg').removeClass('ajax-loading');
                $('#flash-log').css('display', 'none');
                $(msg).flashmsg();
				updateTribeList(num_page, nb_items);
            }
        });
    }, {
        title: "Add users : " + tribe_name,
    });
}

function rm_search(num_page, nb_items, tribe_id) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribe', 'action' : 'rm_search', 'tribe_id' : tribe_id},
        success: function(msg){
//            $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
            $('#flash-msg').removeClass('ajax-loading');
            $('#flash-msg').html('');
            $('#flash-log').css('display', 'none');
			updateTribeList(num_page, nb_items);
            $(msg).flashmsg();
        }
    });
}

function add_search(num_page, nb_items, tribe_id, tribe_name) {
    var content = $('#search-tribe-form form').clone();

    Boxy.askform(content, function(val) {
        $('#flash-log').css('display','');
        $('#flash-msg').addClass('ajax-loading');
        $("#flash-msg").html('Sending');
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribe', 'action' : 'add_search', 'tribe_id' : tribe_id, 'search' : data[1]},
            success: function(msg){
//                $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
                $('#flash-msg').removeClass('ajax-loading');
                $('#flash-log').css('display', 'none');
                $(msg).flashmsg();
				updateTribeList(num_page, nb_items);
            }
        });
    }, {
        title: "Add search : " + tribe_name,
    });
}


function add_icon(num_page, nb_items, tribe_id, tribe_name) {
    var content = $('#icon-tribe-form form').clone();
	content.find('input#tribe-id').attr('value',tribe_id);

	var options =
	{
		target: '#flash-msg',
		url: "api/",
		type: 'post',
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-log').css('display', 'none');
			$(msg).flashmsg();
			updateTribeList(num_page, nb_items);
		},
	};

    Boxy.askform(content, function(val) {
        $('#flash-log').css('display','');
        $('#flash-msg').addClass('ajax-loading');
        $("#flash-msg").html('Sending');

		$('form#icon-tribe.boxform').ajaxSubmit(options);
    }, {
        title: "Add tribe icon : " + tribe_name,
    });
}

function rm_icon(num_page, nb_items, tribe_id) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribe', 'action' : 'rm_icon', 'tribe_id' : tribe_id},
        success: function(msg){
//            $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
            $('#flash-msg').removeClass('ajax-loading');
            $('#flash-msg').html('');
            $('#flash-log').css('display', 'none');
			updateTribeList(num_page, nb_items);
            $(msg).flashmsg();
        }
    });
}
