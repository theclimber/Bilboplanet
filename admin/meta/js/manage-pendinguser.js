$(document).ready(function() {
	$("#pendinguser-list").ready( function() {
		updateUserList();
	});

});

function updateUserList(num_page, nb_items) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'pendinguser', 'action' : 'list', 'num_page': num_page, 'nb_items' : nb_items},
		success: function(msg){
			$("#pendinguser-list").html(msg);
			}
		});
	}
