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

$(document).ready(function() {
	var allPageTags = new Array();
	var allPageTags=document.getElementsByClassName("post");
	for (i=0; i<allPageTags.length; i++) {
		var allImgTags = new Array();
		allImgTags = allPageTags[i].getElementsByTagName("img");
		for (j=0;j<allImgTags.length;-j++) {
			if (allImgTags[j].width > 700){
				allImgTags[j].setAttribute("width","700");
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
			}
		}
	}
});
$(document).ready(function() {
	$("a#bilbobox").fancybox({
		'hideOnContentClick': true
	}); 
});

