$(document).ready(function() {
	page_ready();
});
function page_ready() {
	$('form#profile_form').submit(function() {
		var data = $('form#profile_form').serialize();
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=account&action=update&'+data,
			success: function(msg){
				updatePage('profile', msg);
			}
		});
		return false;
	});
    $('form#addfeed_form').submit(function() {
		var data = $('form#addfeed_form').serialize();
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=feed&action=add_feed&'+data,
			success: function(msg){
				updatePage('profile', msg);
			}
		});
		return false;
    });
    $('select#site_select').change(function(val) {
        var selected = $('select#site_select').val();
        if (selected == "new") {
            $('div#new_site').css('display','');
            var newSite = $('input#new_site').val();
            if (newSite != '') {
                $('div#new_site img.loading').css('display','');
                get_feed_from_site(newSite);
            }
        } else {
            $('div#site_combo img.loading').css('display','');
            $('div#new_site').css('display','none');
            get_feed_from_site(selected);
        }
    });
    $('input#new_site').change(function (val) {
        $('div#new_site img.loading').css('display','');
        get_feed_from_site($('input#new_site').val());
    });
}

function get_feed_from_site(site) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'feed', 'action' : 'feed_from_site', 'site' : site},
        success: function(msg){
            jQuery.each(msg, function(i,val) {
                var html = '<li>'
                    +'<input class="check" type="checkbox" name="feeds[]" value="'+val+'">'
                    +'<span class="feedurl">'+val+'</span></li>';
                $('div#new_feeds ul#feed_list').append(html)
            });
            $('div#site_combo img.loading').css('display','none');
            $('div#new_site img.loading').css('display','none');
        }
    });
}

function rm_feed_tag(feed_id, tag) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tagging', 'action' : 'rm_feed_tag', 'feed_id' : feed_id, 'tag' : tag},
        success: function(msg){
            updatePage('profile', msg);
        }
    });
}
function openAdd() {
	jQuery('#addfeed-field').css('display', '');
}

function closeAdd() {
	jQuery('#addfeed-field').css('display', 'none');
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
                updatePage('profile', msg);
            }
        });
    }, {
        title: "Add feed tags",
    });
}

function rm_feed(feed_id) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'feed', 'action' : 'rm_feed', 'feed_id' : feed_id},
        success: function(msg){
            updatePage('profile', msg);
        }
    });
}
function rm_pending_feed(feed_url) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'feed', 'action' : 'rm_pending_feed', 'feed_url' : feed_url},
        success: function(msg){
            updatePage('profile', msg);
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
            updatePage('profile', msg);
        }
    });
}

