this.page = 0;
this.nb_items = 10;
this.search = getUrlParameter('search');
this.popular = getUrlParameter('popular');
this.order = getUrlParameter('order');
//this.tribe = getUrlParameter('tribe_id');
this.tribe = new Array();
this.tags = new Array();
this.users = new Array();
this.period = '';
this.post_status = 1;
this.trigger = function() {};
$(document).ready(function() {
	this.page = getUrlParameter('num_page');
	this.nb_items = 10;
	this.search = getUrlParameter('search');
	this.order = getUrlParameter('order');
//	this.tribe = getUrlParameter('tribe_id');
	if (this.order == 'popular') {
		$('#filter-order').attr('style', '');
	}
	this.tags = new Array();
	this.users = new Array();
	this.period = '';
	this.post_status = 1;

	var urlVars = getUrlVars();
	if (urlVars['tribe_id']) {
		var tribeList = urlVars['tribe_id'];
		if(tribeList.search('#')) {
			tribeList = tribeList.split('#')[0];
		}
		jQuery.each(tribeList.split(','), function (i, v) {
			add_tribe(v, true);
		});
	}
	if (urlVars['tags']) {
		var tagList = urlVars['tags'];
		if(tagList.search('#')) {
			tagList = tagList.split('#')[0];
		}
		jQuery.each(tagList.split(','), function (i, v) {
			add_tag(v, true);
		});
	}
	if (urlVars['user_id']) {
		var userList = urlVars['user_id'];
		if(userList.search('#')) {
			userList = userList.split('#')[0];
		}
		jQuery.each(userList.split(','), function (i, v) {
			add_user(v, true);
		});
	}
	if (urlVars['uncensored']) {
		var varF = urlVars['uncensored'];
		varF = varF.split('#')[0];
		if (varF != '') {
			this.post_status = 2;
		}
	}
	/*
	if (urlVars['filter']) {
		var varF = urlVars['filter'];
		varF = varF.split('#')[0];
		this.period = varF;
	}
	if (urlVars['page']) {
		var varL = urlVars['page'];
		varL = varL.split('#')[0];
		this.page = varL;
	}*/

	$('#search_form').submit(function() {
		var data = $('#search_form').serializeArray();
		$('#search_form #search_text').val('')
		jQuery.each(data, function(i, field){
			if (field.name == 'search') {
				add_search(field.value);
			}
		});
		return false;
	});

	// set posts refresh every X miliseconds
	setInterval(function() {
		updatePostList();
	}, 1000*60*40);

	$.ajax({
		type: "POST",
		url: "api/",
		data: {
			'ajax' : 'main',
			'action' : 'fetch'
		},
		success: function(msg){
		}
	});

	$("form.comment-form").submit(function() {
		var id = $(this).attr('postid');
		data = {
				'ajax' : 'comment',
				'action' : 'post_comment',
				'post_id' : id
			};
		$.each($(this).serializeArray(), function(i, field) {
			data[field['name']]=field['value'];
		});
		data['content'] = $('textarea#comment_text_'+id).attr('value');
		$.ajax({
			type: "POST",
			url: "api/",
			data: data,
			success: function(msg){
				updatePostList();
			}
		});
		return false;
	})
});


function getUrlParameter(name) {
	var searchString = location.search.substring(1).split('&');
	for (var i = 0; i < searchString.length; i++) {
		var parameter = searchString[i].split('=');
		if(name == parameter[0])
			return parameter[1];
	}
	return '';
}

function arrayToString(array) {
	var string = '';
	for (var i = 0; i < array.length; i++) {
		string += array[i];
		if (i < array.length -1) {
			string += ',';
		}
	}
	return string;
}

function updatePostList() {
	$('#filter-status').attr('style', '');
	var main_div = "main-body";
	$('div#'+main_div).fadeTo('slow', 0.5, function(){});
	$.ajax({
		type: "POST",
		url: "api/",
		data: {
			'ajax' : 'main',
			'action' : 'list',
			'page' : this.page,
			'nb_items' : this.nb_items,
			'search' : this.search,
			'popular' : this.popular,
			'order' : this.order,
			'tags' : arrayToString(this.tags),
			'tribe' : this.tribe[0],
			'users' : arrayToString(this.users),
			'period' : this.period,
			'post_status' : this.post_status
		},
		success: function(msg){
			$('div#'+main_div).html(msg);
			$('div#'+main_div).fadeTo('slow', 1, function(){});
			updateFeedList();
			$('div#'+main_div).trigger('ready');
		}
	});
}

function next_page() {
	this.page += 1;
	updatePostList();
	$('#filter-page-content').html(this.page);
	$('#filter-page').attr('style', '');
}
function prev_page() {
	if (this.page > 0) {
		this.page -= 1;
	}
	updatePostList();
	if (this.page == 0) {
		$('#filter-page').attr('style', 'display:none;');
	} else {
		$('#filter-page-content').html(this.page);
		$('#filter-page').attr('style', '');
	}
}
function add_tribe(tribe, disable_update) {
	this.page = 0;
	var already_exists = false;
	jQuery.each(this.tribe, function(i, val) {
		if (val == tribe) {
			already_exists = true;
		}
	});
	if (!already_exists) {
		this.tribe.push(tribe);
		if (!disable_update) {
			updatePostList();
		}
		var tribelist = '';
		jQuery.each(this.tribe, function(i, val) {
			tribelist += '<span class="tribe"><a href="#" onclick="javascript:rm_tribe(\''+val+'\')">'+val+' x</a></span>';
		});
		$('#filter-page').attr('style', 'display:none;');
		$('#filter-tribe-content').html(tribelist);
		$('#filter-tribe').attr('style', '');
	}
}
function rm_tribe(tribe) {
	this.page = 0;
	this.tribe.pop(tribe);
	updatePostList();
	$('#filter-page').attr('style', 'display:none;');
	if (this.tribe.length == 0) {
		$('#filter-tribe').attr('style', 'display:none;');
	} else {
		var tribelist = '';
		jQuery.each(this.tribe, function(i, tribe) {
			tribelist += '<span class="tribe"><a href="#" onclick="javascript:rm_tribe(\''+tribe+'\')">'+tribe+'</a></span>';
		});
		$('#filter-tribe-content').html(tribelist);
		$('#filter-tribe').attr('style', '');
	}
}

function add_tag(tag, disable_update) {
	this.page = 0;
	var already_exists = false;
	jQuery.each(this.tags, function(i, val) {
		if (val == tag) {
			already_exists = true;
		}
	});
	if (!already_exists) {
		this.tags.push(tag);
		if (!disable_update) {
			updatePostList();
		}
		var taglist = '';
		jQuery.each(this.tags, function(i, val) {
			taglist += '<span class="tag"><a href="#" onclick="javascript:rm_tag(\''+val+'\')">'+val+' x</a></span>';
		});
		$('#filter-page').attr('style', 'display:none;');
		$('#filter-tags-content').html(taglist);
		$('#filter-tags').attr('style', '');
	}
}
function rm_tag(tag) {
	this.page = 0;
	this.tags.pop(tag);
	updatePostList();
	$('#filter-page').attr('style', 'display:none;');
	if (this.tags.length == 0) {
		$('#filter-tags').attr('style', 'display:none;');
	} else {
		var taglist = '';
		jQuery.each(this.tags, function(i, tag) {
			taglist += '<span class="tag"><a href="#" onclick="javascript:rm_tag(\''+tag+'\')">'+tag+'</a></span>';
		});
		$('#filter-tags-content').html(taglist);
		$('#filter-tags').attr('style', '');
	}
}
function add_user(user, disable_update) {
	this.page = 0;
	var already_exists = false;
	jQuery.each(this.users, function(i, val) {
		if (val == user) {
			already_exists = true;
		}
	});
	if (!already_exists) {
		this.users.push(user);
		if (!disable_update) {
			updatePostList();
		}
		var userlist = '';
		jQuery.each(this.users, function(i, val) {
			userlist += '<span class="user"><a href="#" onclick="javascript:rm_user(\''+val+'\')">'+val+'</a></span>';
		});
		$('#filter-page').attr('style', 'display:none;');
		$('#filter-users-content').html(userlist);
		$('#filter-users').attr('style', '');
	}
}
function rm_user(user) {
	this.page = 0;
	this.users.pop(user);
	updatePostList();
	$('#filter-page').attr('style', 'display:none;');
	if (this.users.length == 0) {
		$('#filter-users').attr('style', 'display:none;');
	} else {
		var userlist = '';
		jQuery.each(this.users, function(i, user) {
			userlist += '<span class="user"><a href="#" onclick="javascript:rm_user(\''+user+'\')">'+user+'</a></span>';
		});
		$('#filter-users-content').html(userlist);
		$('#filter-users').attr('style', '');
	}
}
function add_search(search_val) {
	this.page = 0;
	this.search = search_val;
	updatePostList();
	$('#filter-page').attr('style', 'display:none;');
	$('#filter-search-content').html(search+' <a href="#" onclick="javascript:clear_search()">(x)</a>');
	$('#filter-search').attr('style', '');
}
function clear_search() {
	this.search = '';
	this.page = 0;
	updatePostList();
	$('#filter-page').attr('style', 'display:none;');
	$('#filter-search').attr('style', 'display:none');
}
function set_period(period) {
	this.page = 0;
	this.period = period;
	updatePostList();
	$('#filter-page').attr('style', 'display:none;');
	$('#filter-period-content').html(period+' <a href="#" onclick="javascript:rm_period()">(x)</a>');
	$('#filter-period').attr('style', '');
}
function order_by(type) {
	this.page = 0;
	if (type == "popular") {
		this.order = "popular";
		$('#filter-order').attr('style', 'display:block;');
	} else {
		this.order = "latest";
		$('#filter-order').attr('style', 'display:none;');
	}
	updatePostList();
	$('#filter-page').attr('style', 'display:none;');
}
function rm_period() {
	this.page = 0;
	this.period = '';
	updatePostList();
	$('#filter-page').attr('style', 'display:none;');
	$('#filter-period').attr('style', 'display:none');
}
function set_nb_items(nb) {
	this.page = 0;
	this.nb_items = nb;
	updatePostList();
	var html = '';
	var height = 205;
	$('#filter-page').attr('style', 'display:none;');
	if (nb != 10) {
		html += '<a href="#" onclick="javascript:set_nb_items(10)">10</a>, '
	} else {
		html += '10, ';
		height = 205;
	}
	if (nb != 15) {
		html += '<a href="#" onclick="javascript:set_nb_items(15)">15</a>, '
	} else {
		html += '15, ';
		height = 275;
	}
	if (nb != 20) {
		html += '<a href="#" onclick="javascript:set_nb_items(20)">20</a>'
	} else {
		html += '20';
		height = 330;
	}
	$('#filter-nb-items-content').html(html);
	setTimeout( function () {
		$('div#top_10').attr('style', 'height:'+height+'px');
	}, 1000);
}

function updateFeedList() {
	var feedlink = '';
	feedlink += getFeedURL();
	$('a#filter-feed').attr('href',feedlink);
	if (this.tags.length > 0 || this.users.length > 0) {
		$('div#filter-feed').attr('style', '');
	} else {
		$('div#filter-feed').attr('style', 'display:none;');
	}
}
function getFeedURL() {
	var feed_params = 'feed.php?type=atom';
	if (this.tags.length > 0) {
		var tag_list = 'tags='+arrayToString(this.tags);
		feed_params += '&'+tag_list;
	}
	if (this.users.length > 0) {
		var user_list = 'users='+arrayToString(this.users);
		feed_params += '&'+user_list;
	}
	if (this.popular) {
		feed_params += '&popular=true';
	}
	return feed_params;
}
/*
function popup(url) {
	var width = $(window).width() * 0.9;
	var height = $(window).height() * 0.9;
	$('div#popup').attr('style', 'z-index:1000;width:'+width+'px;height:'+height+'px;');
	var iframe_w = width;
	var iframe_h = height - 20;
	$('div#popup .popup-content').html('<iframe '
		+'src="'+url+'" '
		+'style="width:'+iframe_w+'px;height:'+iframe_h+'px">'
	);
}
function close_popup() {
	$('div#popup .popup-content').html('');
	$('div#popup').attr('style', 'display:none;');
}
function toggle_login_dropdown(position, url) {
	if (position == 'open') {
		$("div#loginForm").attr('style', 'z-index: 100;');
	} else {
		$("div#loginForm").attr('style', 'z-index: -100;display:none');
	}
}*/
function refresh_post(post_id){
	var search = this.search;
	$.ajax({
			type:"POST",
			url: "user/api/",
			data: {'ajax' : 'main', 'action':'post', 'post_id':post_id,'search_value':search},
			success: function(msg) {
				// find post on page and replace
				var post_div = 'div#post'+post_id;
				$(post_div).fadeTo('slow', 0, function(){});
				$(post_div).replaceWith(msg);
				$(post_div).fadeTo('slow', 1, function(){});
				$(post_div).trigger('ready');
//				console.debug(msg);
			}
	});
}
function tag_post(post_id, post_title) {
    var content = $('#tag-post-form form').clone();
    Boxy.askform(content, function(val) {
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "user/api/",
			data : {'ajax' : 'tagging', 'action' : 'add_tags', 'post_id' : post_id, 'tags' : data[1]},
            success: function(msg){
				refresh_post(post_id);
				//updatePostList();
            }
        });
    }, {
        title: "Add tags to this post",
    });
}
function rm_tag_post(post_id, tag) {
    var content = $('#tag-post-form form').clone();
	Boxy.confirm('Are you sure you want to remove <b>'+tag+'</b> from this post?', function(val) {
//    Boxy.askform(content, function(val) {
        $.ajax({
            type: "POST",
            url: "user/api/",
			data : {'ajax' : 'tagging', 'action' : 'rm_tag', 'post_id' : post_id, 'tag' : tag},
            success: function(msg){
                updatePostList();
            }
        });
    }, {
        title: "Remove tag to this post",
    });
}

function toggle_post_comments(post_id, comment_status) {
    $.ajax({
        type: "POST",
        url: "user/api/",
        data : {'ajax' : 'post',
			'action' : 'comment',
			'post_id' : post_id,
			'status': comment_status},
        success: function(msg){
            updatePostList();
        }
    });
}

// Read a page's GET URL variables and return them as an associative array.
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

$('div#more-button').ready(function() {
	if ($('div#more-button').length > 0) {
		$(window).scroll(function(){
			if  ($(window).scrollTop() == $(document).height() - $(window).height() - 1000 ||
			$(window).scrollTop() == $(document).height() - $(window).height()){
				showMore();
			}
		});
		$('div#more-button').click(function() {
			showMore();
		});
	}
});

function showMore() {
    $('div#more-button').css('display', '');
    if ($('div#more-button').attr('more') == 'no') {
		return;
	}
	this.page += 1;
	$('#filter-status').attr('style', '');
	var main_div = "main-body";
	$.ajax({
		type: "POST",
		url: "api/",
		data: {
			'ajax' : 'main',
			'action' : 'list',
			'page' : this.page,
			'nb_items' : this.nb_items,
			'search' : this.search,
			'popular' : this.popular,
			'order' : this.order,
			'tags' : arrayToString(this.tags),
			'tribe' : this.tribe[0],
			'users' : arrayToString(this.users),
			'period' : this.period,
			'post_status' : this.post_status
		},
		success: function(msg){
			var postlist = $(msg).find('#posts-list');
			$('div#'+main_div).find('#posts-list').append(postlist.html());
			if (postlist.html().length<10) {
				$('div#more-button').attr('more', 'no');
			}
			$('div#more-button').css('display', 'none');
			$('div#'+main_div).trigger('ready');
		}
    });
}

