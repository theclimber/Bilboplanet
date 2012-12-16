function refresh_tribe(tribe_id,page){
	var tribe_div = 'div#tribe-'+tribe_id;
	$(tribe_div).fadeTo('slow', 0.5, function(){});
	$.ajax({
			type:"POST",
			url: "api/",
			data: {'ajax' : 'main', 'action':'tribe', 'tribe_id':tribe_id,'page':page},
			success: function(msg) {
				// find tribe on page and replace
				$(tribe_div).replaceWith(msg);
				$(tribe_div).fadeTo('slow', 1, function(){});
				$(tribe_div).trigger('ready');
//				console.debug(msg);
			}
	});
}
function tribe_prev(tribe,page) {
    refresh_tribe(tribe,page+1);
}
function tribe_next(tribe,page) {
    if (page > 0)
        refresh_tribe(tribe,page-1);
}
