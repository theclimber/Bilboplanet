$(document).ready(function() {
	$('form#profile_form').submit(function() {
		var data = $('form#profile_form').serialize();
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=account&action=update&'+data,
			success: function(msg){
				//updatePage('profile');
			}
		});
		return false;
	});
});

function rm_feed_tag(feed_id, tag) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tagging', 'action' : 'rm_feed_tag', 'feed_id' : feed_id, 'tag' : tag},
        success: function(msg){
            updatePage('profile');
        }
    });
}
function add_feed_tags(feed_id) {
    var content = $('#tag-feed-form form').clone();
    Boxy.askform(content, function(val) {
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tagging', 'action' : 'add_feed_tags', 'feed_id' : feed_id, 'tags' : data[1]},
            success: function(msg){
                updatePage('profile');
            }
        });
    }, {
        title: "Add feed tags",
    });
}

function add_feed() {
    var content = $('#new-feed-form form').clone();
    Boxy.askform(content, function(val) {
		var sdata = content.serialize();
		sdata += '&ajax=feed'
		sdata += '&action=add_feed'
//		console.debug(sdata);
        $.ajax({
            type: "POST",
            url: "api/",
			data: sdata,
            success: function(msg){
                updatePage('profile');
            }
        });
    }, {
        title: "Add a new feed",
    });
}
function rm_feed(feed_id) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'feed', 'action' : 'rm_feed', 'feed_id' : feed_id},
        success: function(msg){
            updatePage('profile');
        }
    });
}
function rm_pending_feed(feed_url) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'feed', 'action' : 'rm_pending_feed', 'feed_url' : feed_url},
        success: function(msg){
            updatePage('profile');
        }
    });
}

function disallow_comments(feed_id) {
	comment(feed_id, 0);
}
function allow_comments(feed_id) {
	comment(feed_id, 1);
}
function comment(feed_id, comment_status) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'feed',
			'action' : 'comment',
			'feed_id' : feed_id,
			'status': comment_status},
        success: function(msg){
            updatePage('profile');
        }
    });
}

