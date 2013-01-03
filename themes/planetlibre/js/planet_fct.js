function finishWith(str, substr) {
	if (str.indexOf(substr)==str.length-substr.length){
		return true;
	}
	else{
		return false;
	}
}

function getExtension(str) {
	return str.substring(str.length-4,str.length)
}

this.sidebar_hidden = 0;
$(document).ready(function() {
	var allPageTags = new Array();
	var allPageTags=document.getElementsByClassName("post");
	for (i=0; i<allPageTags.length; i++) {
		var allImgTags = new Array();
		allImgTags = allPageTags[i].getElementsByTagName("img");
		for (j=0;j<allImgTags.length;-j++) {
			if (allImgTags[j].width > 640){
				allImgTags[j].setAttribute("width","640");
				allImgTags[j].setAttribute('height', '');
			}
			var imgExtension = getExtension(allImgTags[j].src)
			if(allImgTags[j].parentNode.tagName=='A'){
				if(finishWith(allImgTags[j].parentNode.href,imgExtension)){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.png')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.PNG')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.jpg')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.JPG')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.jpeg')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.JPEG')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.bmp')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.BMP')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.gif')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
				if(finishWith(allImgTags[j].parentNode.href,'.GIF')){
					allImgTags[j].parentNode.setAttribute("id", "bilbobox");
				}
			}
		}
	}

	$("a#bilbobox").fancybox({
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});

	detectSmartPhone()
	checkCookie()

	/*
	$('div#header-bg').click(function () {
		$('html, body').animate({scrollTop:0}, 'slow');
		return false;
	});*/

	// Expand and remove scroll when click on text
/*	$('div.post-text').click(function() {
		var post_id = $(this).attr('post_id');
		expand_block(post_id);
	});*/

/*
	if ($('div.post-text').length > 1) {
		jQuery.each($('div.post-text'), function () {
			var post_id = $(this).attr('post_id');
			expand_block(post_id);
//			$(this).attr('class', 'post-text-collapsed');
//			$(this).attr('status', 'collapsed');
		});
		jQuery.each($('div.comment-block'), function () {
			$(this).attr('style', 'display:none');
		});
	}
*/
	$("div.post").hover(
	function () {
		show_post_info(this.id);
	},
	function () {
		hide_post_info(this.id);
	}
	);

});

function expand_block (id) {
	var block = $('div#text-'+id);
	var stat = block.attr('status');
	if (stat == 'collapsed') {
		block.attr('class','post-text');
		block.attr('status', 'expanded');
		$('div#expand-button-'+id).attr('class', 'collapse-button');
	} else {
		block.attr('class','post-text-collapsed');
		block.attr('status', 'collapsed');
		$('div#expand-button-'+id).attr('class', 'expand-button');
	}
}


$('div#body').live('ready', function() {
	if ($('div.post-text').length > 1) {
		jQuery.each($('div.post-text'), function () {
			var post_id = $(this).attr('post_id');
			expand_block(post_id);
		});
		jQuery.each($('div.comment-block'), function () {
			$(this).attr('style', 'display:none');
		});
	}
});

/*
function getBrowserWidth(){
	if (window.innerWidth){
		return window.innerWidth;}
	else if (document.documentElement && document.documentElement.clientWidth != 0){
		return document.documentElement.clientWidth;	}
	else if (document.body){return document.body.clientWidth;}
		return 0;
}
function dynamicLayout(){
	var browserWidth = getBrowserWidth();

	//Load Thin CSS Rules
	if (browserWidth < 1200){
		changeLayout("normal");
	}
	//Load Wider CSS Rules
	if (browserWidth >= 1200){
		changeLayout("big");
	}
}
// changeLayout is based on setActiveStyleSheet function by Paul Sowdon
// http://www.alistapart.com/articles/alternate/
function changeLayout(description){
   var rows = document.getElementsByTagName('link');
   for(var i=0, row; row = rows[i]; i++){
	   if(row.getAttribute("type") == "text/css") {
		   if(row.getAttribute("title") == description){row.disabled = false;}
		   else if(row.getAttribute("title") != "default"){row.disabled = true;}
	   }
   }
}*/
//
//addEvent() by John Resig
function addEvent( obj, type, fn ){
	if (obj.addEventListener){
		obj.addEventListener( type, fn, false );
	}
	else if (obj.attachEvent){
		obj["e"+type+fn] = fn;
		obj[type+fn] = function(){ obj["e"+type+fn]( window.event ); }
		obj.attachEvent( "on"+type, obj[type+fn] );
	}
}

//Run dynamicLayout function when page loads and when it resizes
//addEvent(window, 'load', dynamicLayout);
//addEvent(window, 'resize', dynamicLayout);

function showNavigationMenu() {
	var display = $("div#navigation-bg").css('display');
	if (display == 'none') {
		$("div#navigation-bg").css('display','block');
	} else {
		$("div#navigation-bg").css('display','none');
	}
}
function display_login() {
	$('div.login-box').css('display','block');
	$('div.login-box').css('z-index','1000');
	$('div.login-box').fadeTo('slow', 1, function(){});
	$('div.login-box').bind('clickoutside', function(e) {
				hide_login();
            });
}
function hide_login() {
	$('div.login-box').fadeTo('slow', 0, function(){});
	$('div.login-box').css('display','none');
	$('div.login-box').css('z-index','-100');
}

function show_post_info(post_id) {
	var div = $('div#'+post_id).find('div.postbox');
//	$(div).css('display','inline-block');
	$(div).fadeTo('slow', 1, function(){});
}
function hide_post_info(post_id) {
	var div = $('div#'+post_id).find('div.postbox');
	$(div).fadeTo('slow', 0, function(){});
//	$(div).css('display','none');
}

function showSidebar() {
	if (this.sidebar_hidden == 1) {
		this.sidebar_hidden = 0;
		$('#tribes-bg').css('display','block');
		$('.content').css('margin-left','250px');
		$('img#show-sidebar-button').css('display','none');
		$('img#hide-sidebar-button').css('display','');
	} else {
		this.sidebar_hidden = 1;
		$('#tribes-bg').css('display','none');
		$('.content').css('margin-left','0px');
		$('img#show-sidebar-button').css('display','');
		$('img#hide-sidebar-button').css('display','none');
	}
	setCookie("sidebar_visible",this.sidebar_hidden,31);
}
function detectSmartPhone() {
	var uagent = navigator.userAgent.toLowerCase();
	if (uagent.indexOf("android") > -1) {
		showSidebar();
	} else if (uagent.indexOf("iphone") > -1) {
		showSidebar();
	} else if (uagent.indexOf("ipod") > -1) {
		showSidebar();
	} else if (uagent.indexOf("palm") > -1) {
		showSidebar();
	} else if (uagent.indexOf("blackberry") > -1) {
		showSidebar();
	}
}

function getCookie(c_name)
{
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++)
	{
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x=x.replace(/^\s+|\s+$/g,"");
		if (x==c_name)
		{
			return unescape(y);
		}
	}
}

function setCookie(c_name,value,exdays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}
function checkCookie()
{
	var sidebar=getCookie("sidebar_visible");
	if (sidebar!=null && sidebar!="")
	{
		if (sidebar != this.sidebar_hidden) {
			showSidebar();
		}
	}
	else
	{
		setCookie("sidebar_visible",this.sidebar_hidden,31);
	}
}
