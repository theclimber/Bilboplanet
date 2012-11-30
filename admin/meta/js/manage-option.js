$(document).ready(function($) {

	$("#option-list").ready(function() {
		listopt();
	});

});

/***********************************************************************/
/************************* Functions ***********************************/
/***********************************************************************/
// Display all settings
function listopt() {
	$('#options-button-update').show();
	$('#options-button-close').hide();
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'option', 'action' : 'list'},
		cache: false,
		success: function(msg){
			$("#options-list").html(msg);
			$("#options-form").hide();
			$("#options-list").show();
		}
	});
	return false;
}

function join() {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'option', 'action' : 'join'},
		success: function(msg){
			$("#options-button-join").css('display','none');
		}
	});
	return false;
}

// Display form to update settings
function formopt() {
	$('#flash-log').hide();
	$('#options-button-update').hide();
	$('#options-button-close').show();
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'option', 'action' : 'options-form'},
		cache: false,
		success: function(msg){
			$("#options-form").html(msg);
			$("#options-list").hide();
			$("#options-form").show();
			$("#options-form").ready(function() {
				subscription_state('options-form');
				votes_state('options-form');
			});
		}
	});
	return false;
}

// Update settings (submit form)
function updateopt() {
	$('#options-loading').show();
	$('#flash-log').show();
	var data = $('#manage_options').serialize();
	$.ajax({
		type: "POST",
		url: "api/",
		data: "ajax=option&action=update&"+data,
		cache: false,
		success: function(msg){
			$('#options-loading').hide();
			$(msg).flashmsg();
			listopt();
		}
	});
	return false;
}

// Preview modal with content subscription page	
function call_preview(id) {
	var data = '#'+id;
	$(data).preview({
		opacity: '0.5'
	})
}

// Show / hide textarea subscription_content
function subscription_state(id) {
	var data = '#'+id;
	$(data).ready(function() {
			if ($('#subscription').is(':checked')) {
					$('#subscription_content').show();
					$('#preview_button').css('display', 'inline');
			}
			else {
					$('#subscription_content').hide();
					$('#preview_button').css('display', 'none');
			}
	});
}

// Show / hide system votes
function votes_state(id) {
	var data = '#'+id;
	$(data).ready(function() {
		if($('#show_votes').is(':checked')) {
			$('#votes_system').show();
		}
		else {
			$('#votes_system').hide();
		}
	});
}
