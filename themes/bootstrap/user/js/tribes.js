this.isfilter = false;
$(document).ready(function() {
	page_ready();
});

function page_ready() {
	this.isfilter=false;
	$('#addtribe_form').submit(function() {
		var data = $('#addtribe_form').serialize();
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=tribes&action=add&'+data,
			success: function(msg){
				$('#addtribe-field').css('display', 'none');
				$('#addtribe_form').find('input[type=text]').val('');
				$('#user_id').val('');
				$('#tribe_name').val('');
				$('#ordering').val('');
				updatePage('tribes', msg);
			}
		});
		return false;
	});
/*	$("#tribe-list").ready( function() {
		updatePage('tribes', '');
	});*/
}

function toggleTribeVisibility(tribe_id) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'tribes', 'action' : 'toggle', 'tribe_id' : tribe_id},
		success: function(msg){
			updatePage('tribes', msg);
		}
	});
}

function removeTribe(tribe_id) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'tribes', 'action' : 'remove', 'tribe_id' : tribe_id},
		success: function(msg){
			showFlash(msg);
			$('#removeTribeConfirm_form').submit(function() {
				var data = $('#removeTribeConfirm_form').serialize().split('=');
				$.ajax({
					type: "POST",
					url: "api/",
					data : {'ajax' : 'tribes', 'action' : 'removeConfirm', 'tribe_id' : data[1]},
					success: function(msg){
						updatePage('tribes', msg);
					}
				});
				return false;
			});
		}
	});
}

function edit(tribe_id) {
	$.ajax({
		type: "POST",
		url: "api/",
		data: {'ajax' : 'tribes', 'action' : 'get', 'tribe_id' : tribe_id},
		dataType: 'json',
		success: function(tribe){
			$('#tribe-edit-form #tribe_id').val(tribe.tribe_id);
			$('#tribe-edit-form #tribe_name').val(tribe.tribe_name);
			$('#tribe-edit-form #tribe_order').val(tribe.ordering);
			var content = $('#tribe-edit-form form').clone();

			Boxy.askform(content, function(val) {
				val['ajax'] = "tribes";
				val['action'] = "edit";
				$.ajax({
					type: "POST",
					url: "api/",
					data : val,
					success: function(msg){
						$('#tribe-edit-form #tribe_id').val('');
						$('#tribe-edit-form #tribe_name').val('');
						updatePage('tribes', msg);
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

function rm_tag(tribe_id, tag) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribes', 'action' : 'rm_tag', 'tribe_id' : tribe_id, 'tag' : tag},
        success: function(msg){
			updatePage('tribes', msg);
        }
    });
}

function add_tags(tribe_id, tribe_name) {
    var content = $('#tag-tribe-form form').clone();

    Boxy.askform(content, function(val) {
//        $("#tag_action"+tribe_id)[0].setAttribute('class','ajax-loading');
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribes', 'action' : 'add_tags', 'tribe_id' : tribe_id, 'tags' : data[1]},
            success: function(msg){
				updatePage('tribes', msg);
            }
        });
    }, {
        title: "Tagging : " + tribe_name,
    });
}

function rm_notag(tribe_id, notag) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribes', 'action' : 'rm_notag', 'tribe_id' : tribe_id, 'tag' : notag},
        success: function(msg){
			updatePage('tribes', msg);
        }
    });
}

function add_notags(tribe_id, tribe_name) {
    var content = $('#tag-tribe-form form').clone();

    Boxy.askform(content, function(val) {
//        $("#tag_action"+tribe_id)[0].setAttribute('class','ajax-loading');
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribes', 'action' : 'add_notags', 'tribe_id' : tribe_id, 'tags' : data[1]},
            success: function(msg){
				updatePage('tribes', msg);
            }
        });
    }, {
        title: "Tagging unwanted : " + tribe_name,
    });
}

function rm_user(tribe_id, user) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribes', 'action' : 'rm_user', 'tribe_id' : tribe_id, 'user' : user},
        success: function(msg){
			updatePage('tribes', msg);
        }
    });
}

function add_users(tribe_id, tribe_name) {
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
		var data = content.find('form').serialize().split('=');
		console.debug(data);
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribes', 'action' : 'add_users', 'tribe_id' : tribe_id, 'users' : data[1]},
            success: function(msg){
				updatePage('tribes', msg);
            }
        });
    }, {
        title: "Add users : " + tribe_name,
    });
}

function rm_search(tribe_id) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribes', 'action' : 'rm_search', 'tribe_id' : tribe_id},
        success: function(msg){
			updatePage('tribes', msg);
        }
    });
}

function add_search(tribe_id, tribe_name) {
    var content = $('#search-tribe-form form').clone();

    Boxy.askform(content, function(val) {
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribes', 'action' : 'add_search', 'tribe_id' : tribe_id, 'search' : data[1]},
            success: function(msg){
				updatePage('tribes', msg);
            }
        });
    }, {
        title: "Add search : " + tribe_name,
    });
}


function add_icon(tribe_id, tribe_name) {
    var content = $('#icon-tribe-form form').clone();
	content.find('input#tribe-id').attr('value',tribe_id);

	var options =
	{
		target: '#flash-msg',
		url: "api/",
		type: 'post',
		success: function(msg){
			updatePage('tribes', msg);
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

function rm_icon(tribe_id) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribes', 'action' : 'rm_icon', 'tribe_id' : tribe_id},
        success: function(msg){
			updatePage('tribes', msg);
        }
    });
}
