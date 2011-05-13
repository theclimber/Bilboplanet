function rm_tag(post_id, tag) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tagging', 'action' : 'rm_tag', 'post_id' : post_id, 'tag' : tag},
        success: function(msg){
            $("#action"+post_id)[0].removeAttribute('class','ajax-loading');
            $('#flash-msg').removeClass('ajax-loading');
            $('#flash-log').css('display', 'none');
            $(msg).flashmsg();
            updateFilterList();
        }
    });
}

function add_tags(post_id, post_title) {
    var content = $('#tag-post-form form').clone();

    Boxy.askform(content, function(val) {
        $('#flash-log').css('display','');
        $('#flash-msg').addClass('ajax-loading');
        $("#flash-msg").html('Sending');
        $("#action"+post_id)[0].setAttribute('class','ajax-loading');
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tagging', 'action' : 'add_tags', 'post_id' : post_id, 'tags' : data[1]},
            success: function(msg){
                $("#action"+post_id)[0].removeAttribute('class','ajax-loading');
                $('#flash-msg').removeClass('ajax-loading');
                $('#flash-log').css('display', 'none');
                $(msg).flashmsg();
                updateFilterList();
            }
        });
    }, {
        title: "Tagging : " + post_title,
    });
}

function updateFilterList(values) {
    if (values == null) {
        var user_id = $('select.userscombo').val();
        var nb_items = $('input#nb_items').val();
        values = {
            'ajax' : 'tagging',
            'action' : 'filter',
            'nb_items' : nb_items
        };
        if (user_id != '') {
            values['user_id'] = user_id;
        }
    }
	$.ajax({
        type: "POST",
        url: "api/",
        data: values,
        success: function(msg){
            $("#post-list").html(msg);
        }
    });
}

$(document).ready(function() {
	$("#post-list").ready( function() {
        values = {'ajax' : 'tagging', 'action' : 'filter'};
        updateFilterList(values);
	});
	$("#filter-form").submit(function() {
		var $inputs = $('#filter-form :input');
		var values = {};
		$inputs.each(function() {
			values[this.name] = $(this).val();
		});
        updateFilterList(values);
		return false;
	});
});
