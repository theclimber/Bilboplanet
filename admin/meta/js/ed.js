/*****************************************/
// Name: Javascript Textarea HTML Editor
// Version: 1.3
// Author: Balakrishnan
// Last Modified Date: 25/Jan/2009
// License: Free
// URL: http://www.corpocrat.com
/******************************************/

function edToolbar(obj) {
	
	var img_dir = 'meta/icons';
	
	var toolbar = "<img class=\"buttoned tips\" rel=\"Bold\" src=\""+img_dir+"/bold.png\" name=\"btnBold\" title=\"Bold\" onClick=\"doAddTags('<strong>','</strong>','"+obj+"')\">";
	toolbar = toolbar+"<img class=\"buttoned tips\" rel=\"Italic\" src=\""+img_dir+"/italic.png\" name=\"btnItalic\" title=\"Italic\" onClick=\"doAddTags('<em>','</em>','" + obj + "')\">";
	toolbar = toolbar+"<img class=\"buttoned tips\" rel=\"Undeline\" src=\""+img_dir+"/underline.png\" name=\"btnUnderline\" title=\"Underline\" onClick=\"doAddTags('<u>','</u>','" + obj + "')\">";
	toolbar = toolbar+"<img class=\"buttoned tips\" rel=\"Insert Link\" src=\""+img_dir+"/link.png\" name=\"btnLink\" title=\"Insert Link\" onClick=\"doURL('" + obj + "')\">";
	toolbar = toolbar+"<img class=\"buttoned tips\" rel=\"Insert Picture\" src=\""+img_dir+"/image.png\" name=\"btnPicture\" title=\"Insert Picture\" onClick=\"doImage('" + obj + "')\">";
	toolbar = toolbar+"<img class=\"buttoned tips\" rel=\"Ordered List\" src=\""+img_dir+"/ordered.png\" name=\"btnList\" title=\"Ordered List\" onClick=\"doList('<ol>','</ol>','" + obj + "')\">";
	toolbar = toolbar+"<img class=\"buttoned tips\" rel=\"Unordered List\" src=\""+img_dir+"/list.png\" name=\"btnList\" title=\"Unordered List\" onClick=\"doList('<ul>','</ul>','" + obj + "')\">";
	toolbar = toolbar+"<img class=\"buttoned tips\" rel=\"Quote\" src=\""+img_dir+"/quote.png\" name=\"btnQuote\" title=\"Quote\" onClick=\"doAddTags('<blockquote>','</blockquote>','" + obj + "')\">";
	toolbar = toolbar+"<img class=\"buttoned tips\" rel=\"Code\" src=\""+img_dir+"/code.png\" name=\"btnCode\" title=\"Code\" onClick=\"doAddTags('<code>','</code>','" + obj + "')\">";
	toolbar = toolbar+"<br />";
	$('.wysiwyg').append(toolbar);
}

function doImage(obj) {
	var textarea = document.getElementById(obj);
	var url = prompt('Enter the Image URL:','http://');

	var scrollTop = textarea.scrollTop;
	var scrollLeft = textarea.scrollLeft;

if (url != '' && url != null) {

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				sel.text = '<img src="' + url + '">';
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = '<img src="' + url + '">';
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}
 }
}

function doURL(obj) {
var sel;
var textarea = document.getElementById(obj);
var url = prompt('Enter the URL:','http://');
var scrollTop = textarea.scrollTop;
var scrollLeft = textarea.scrollLeft;

if (url != '' && url != null) {

	if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				
				if(sel.text==""){
					sel.text = '<a href="' + url + '">' + url + '</a>';
					} else {
					sel.text = '<a href="' + url + '">' + sel.text + '</a>';
					}
				//alert(sel.text);
				
			}
   else 
    {
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
		var sel = textarea.value.substring(start, end);
		
		if(sel==""){
		sel=url; 
		} else
		{
        var sel = textarea.value.substring(start, end);
		}
	    //alert(sel);
		
		
		var rep = '<a href="' + url + '">' + sel + '</a>';;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}
 }
}

function doAddTags(tag1,tag2,obj) {
var textarea = document.getElementById(obj);
	// Code for IE
		if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				//alert(sel.text);
				sel.text = tag1 + sel.text + tag2;
			}
   else 
    {  // Code for Mozilla Firefox
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;
		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		var rep = tag1 + sel + tag2;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}
}

function doList(tag1,tag2,obj){
var textarea = document.getElementById(obj);

// Code for IE
		if (document.selection) 
			{
				textarea.focus();
				var sel = document.selection.createRange();
				var list = sel.text.split('\n');
		
				for(i=0;i<list.length;i++) 
				{
				list[i] = '<li>' + list[i] + '</li>';
				}
				//alert(list.join("\n"));
				sel.text = tag1 + '\n' + list.join("\n") + '\n' + tag2;
				
			} else
			// Code for Firefox
			{

		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var i;
		
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;

		
        var sel = textarea.value.substring(start, end);
	    //alert(sel);
		
		var list = sel.split('\n');
		
		for(i=0;i<list.length;i++) 
		{
		list[i] = '<li>' + list[i] + '</li>';
		}
		//alert(list.join("<br>"));
        
		
		var rep = tag1 + '\n' + list.join("\n") + '\n' +tag2;
		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
 }
}
