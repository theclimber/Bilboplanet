function rm_tag(post_id, tag) {
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tagging', 'action' : 'rm_tag', 'post_id' : post_id, 'tag' : tag},
        success: function(msg){
            updatePage('dashboard');
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
                updatePage('dashboard');
            }
        });
    }, {
        title: "Tagging : " + post_title,
    });
}
