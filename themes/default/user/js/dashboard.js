function page_ready() {}
function rm_tag(post_id, tag) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tagging', 'action' : 'rm_tag', 'post_id' : post_id, 'tag' : tag},
        success: function(msg){
            updatePage('dashboard', msg);
        }
    });
}
function add_tags(post_id, post_title) {
    var content = $('#tag-post-form form').clone();
    Boxy.askform(content, function(val) {
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tagging', 'action' : 'add_tags', 'post_id' : post_id, 'tags' : data[1]},
            success: function(msg){
                updatePage('dashboard', msg);
            }
        });
    }, {
        title: "Tagging : " + post_title,
    });
}
function rm_post(post_id) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'post', 'action' : 'rm_post', 'post_id' : post_id},
        success: function(msg){
            updatePage('dashboard', msg);
        }
    });
}
function add_post(post_id) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'post', 'action' : 'add_post', 'post_id' : post_id},
        success: function(msg){
            updatePage('dashboard', msg);
        }
    });
}
function toggle_post_comments(post_id, comment_status) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'post',
			'action' : 'comment',
			'post_id' : post_id,
			'status': comment_status},
        success: function(msg){
            updatePage('dashboard', msg);
        }
    });
}
