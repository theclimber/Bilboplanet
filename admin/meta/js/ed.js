/*****************************************/
// Name: Javascript Textarea HTML Editor
// Version: 1.3
// Author: Balakrishnan
// Last Modified Date: 25/Jan/2009
// License: Free
// URL: http://www.corpocrat.com
/******************************************/

var textarea;
var content;
var img_dir = 'meta/icons/';

function edSimpleToolbar(obj) {
	document.write("<img class=\"buttoned\" src=\""+img_dir+"/bold.png\" name=\"btnBold\" onClick=\"doAddTags('<strong>','</strong>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\""+img_dir+"/italic.png\" name=\"btnItalic\" onClick=\"doAddTags('<em>','</em>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\""+img_dir+"/underline.png\" name=\"btnUnderline\" onClick=\"doAddTags('<u>','</u>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\""+img_dir+"/ordered.png\" name=\"btnList\" onClick=\"doList('<ol>','</ol>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\""+img_dir+"/list.png\" name=\"btnList\" onClick=\"doList('<ul>','</ul>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\""+img_dir+"/quote.png\" name=\"btnQuote\" onClick=\"doAddTags('<blockquote>','</blockquote>','" + obj + "')\">"); 
  	document.write("<img class=\"buttoned\" src=\""+img_dir+"/code.png\" name=\"btnCode\" onClick=\"doAddTags('<code>','</code>','" + obj + "')\">");
	document.write("<br>");
}

function edToolbar(obj) {
	document.write("<img class=\"buttoned\" src=\"icons/bold.png\" name=\"btnBold\" onClick=\"doAddTags('<strong>','</strong>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\"icons/italic.png\" name=\"btnItalic\" onClick=\"doAddTags('<em>','</em>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\"icons/underline.png\" name=\"btnUnderline\" onClick=\"doAddTags('<u>','</u>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\"icons/link.png\" name=\"btnLink\" onClick=\"doURL('" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\"icons/image.png\" name=\"btnPicture\" onClick=\"doImage('" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\"icons/ordered.png\" name=\"btnList\" onClick=\"doList('<ol>','</ol>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\"icons/list.png\" name=\"btnList\" onClick=\"doList('<ul>','</ul>','" + obj + "')\">");
	document.write("<img class=\"buttoned\" src=\"icons/quote.png\" name=\"btnQuote\" onClick=\"doAddTags('<blockquote>','</blockquote>','" + obj + "')\">"); 
  	document.write("<img class=\"buttoned\" src=\"icons/code.png\" name=\"btnCode\" onClick=\"doAddTags('<code>','</code>','" + obj + "')\">");
    document.write("<br>");
}

function doImage(obj)
{
textarea = document.getElementById(obj);
var url = prompt('Enter the Image URL:','http://');

var scrollTop = textarea.scrollTop;
var scrollLeft = textarea.scrollLeft;

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

function doURL(obj)
{
var sel;
textarea = document.getElementById(obj);
var url = prompt('Enter the URL:','http://');
var scrollTop = textarea.scrollTop;
var scrollLeft = textarea.scrollLeft;

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

function doAddTags(tag1,tag2,obj)
{
textarea = document.getElementById(obj);
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
textarea = document.getElementById(obj);

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
