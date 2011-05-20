this.page = 0;
this.nb_items = 10;
this.search = getUrlParameter('search');
this.popular = getUrlParameter('popular');
this.tags = new Array();
this.users = new Array();
this.period = '';
$(document).ready(function() {
	this.page = getUrlParameter('num_page');
	this.nb_items = 10;
	this.search = getUrlParameter('search');
	this.popular = getUrlParameter('popular');
	if (this.popular == 'true') {
		$('#filter-popular').attr('style', '');
	}
	this.tags = new Array();
	this.users = new Array();
	this.period = '';

	$('#search_form').submit(function() {
		var data = $('#search_form').serializeArray();
		jQuery.each(data, function(i, field){
			if (field.name == 'search') {
				add_search(field.value);
			}
		});
		return false;
	});

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
		if (i <= array.length -1) {
			string += ',';
		}
	}
	return string;
}

function updatePostList() {
	$('#filter-status').attr('style', '');
	var main_div = "body";
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
			'tags' : arrayToString(this.tags),
			'users' : arrayToString(this.users),
			'period' : this.period
		},
		success: function(msg){
			$('div#'+main_div).html(msg);
			$('div#'+main_div).fadeTo('slow', 1, function(){});
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
function add_tag(tag) {
	this.page = 0;
	var already_exists = false;
	jQuery.each(this.tags, function(i, val) {
		if (val == tag) {
			already_exists = true;
		}
	});
	if (!already_exists) {
		this.tags.push(tag);
		updatePostList();
		var taglist = '';
		jQuery.each(this.tags, function(i, val) {
			taglist += '<span class="tag"><a href="#" onclick="javascript:rm_tag(\''+val+'\')">'+val+'</a></span>';
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
function add_user(user) {
	this.page = 0;
	var already_exists = false;
	jQuery.each(this.users, function(i, val) {
		if (val == user) {
			already_exists = true;
		}
	});
	if (!already_exists) {
		this.users.push(user);
		updatePostList();
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
function add_search(search) {
	this.page = 0;
	this.search = search;
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
	$('#filter-page').attr('style', 'display:none;');
	if (nb != 10) {
		html += '<a href="#" onclick="javascript:set_nb_items(10)">10</a>, '
	} else {
		html += '10, ';
	}
	if (nb != 15) {
		html += '<a href="#" onclick="javascript:set_nb_items(15)">15</a>, '
	} else {
		html += '15, ';
	}
	if (nb != 20) {
		html += '<a href="#" onclick="javascript:set_nb_items(20)">20</a>'
	} else {
		html += '20';
	}
	$('#filter-nb-items-content').html(html);
}
