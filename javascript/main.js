this.page = 0;
this.nb_items = 10;
this.search = '';
this.popular = 0;
this.tags = new Array();
this.users = new Array();
this.period = '';
$(document).ready(function() {
	this.page = 0;
	this.nb_items = 10;
	this.search = '';
	this.popular = 0;
	this.tags = new Array();
	this.users = new Array();
	this.period = '';

/*	$("#posts-list").ready( function() {
		updatePostList();
	});*/
});

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
	$("div#posts-list").html('Loading...');
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
			$("div#posts-list").html(msg);
//			console.debug(msg.posts);
		}
	});
}

function next_page() {
	this.page += 1;
	updatePostList();
}
function prev_page() {
	if (this.page > 0) {
		this.page -= 1;
	}
	updatePostList();
}
function add_tag(tag) {
	this.page = 0;
	this.tags.push(tag);
	updatePostList();
}
function rm_tag(tag) {
	this.page = 0;
	this.tags.pop(tag);
	updatePostList();
}
function add_user(user) {
	this.page = 0;
	this.users.push(user);
	updatePostList();
}
function rm_user(user) {
	this.page = 0;
	this.users.pop(user);
	updatePostList();
}
