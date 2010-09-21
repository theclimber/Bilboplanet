function select(id_article) {
	var content = '<div class="description">'+
		'Merci d\'evaluer l\'article en fonction des criteres suivants</div>'+
		'<table id="popupform">'+
		'<tr class="title"><td></td><td>Bon</td><td>Moyen</td><td>Mauvais</td></tr>'+
		'<tr><td><label for="style">Style et forme</label></td>'+
			'<td><input type="radio" name="style" value="2"/></td>'+
			'<td><input type="radio" name="style" value="1"/></td>'+
			'<td><input type="radio" name="style" value="0"/></td></tr>'+
		'<tr><td><label for="contenu">Approfondissement du sujet</label></td>'+
			'<td><input type="radio" name="contenu" value ="2" /></td>'+
			'<td><input type="radio" name="contenu" value ="1" /></td>'+
			'<td><input type="radio" name="contenu" value ="0" /></td></tr>'+
		'<tr><td><label for="recherche">References et sources</label></td>'+
			'<td><input type="radio" name="recherche" value ="2" /></td>'+
			'<td><input type="radio" name="recherche" value ="1" /></td>'+
			'<td><input type="radio" name="recherche" value ="0" /></td></tr>'+
		'</table>';
	Boxy.askform(content, function(val) {
		val['select'] = id_article;
		val['user_id'] = user_id;
		$("#flash").html('Requete en cours');
		$("#flash")[0].setAttribute('class','ajax-loading');
		$("#action"+id_article)[0].setAttribute('class','ajax-loading');
		$.ajax({
			type: "POST",
			url: "selection_api.php",
			data : val,
			success: function(msg){
				$("#line"+id_article)[0].removeAttribute('class', 'unselected');
				$("#line"+id_article)[0].setAttribute('class', 'selected');
				$("#action"+id_article)[0].removeAttribute('class','ajax-loading');
				
				$("#action"+id_article).html('<span id="action'+id_article+'"><img src="images/like-light.png" title="Sélectionner cet article" /></span>');
				$("#flash").html(msg);
				$("#flash")[0].setAttribute('class','succeed');
				update_pool();
			}
		});
	}, {
		title: "Veuillez evaluer cet article",
	});
}

function update_pool(){
	$("#selected-pool").ready( function() {
		$.ajax({
			type: "POST",
			url: "selection_api.php",
			data : {'selected' : '', 'user_id': user_id},
			success: function(msg){
				$("#selected-pool").html(msg);
			}
		});
	});
}

$(document).ready(function() {
	$("#articles-list").ready( function() {
		$.ajax({
			type: "POST",
			url: "selection_api.php",
			data : {'filter' : '', 'user_id': user_id},
			success: function(msg){
				$("#articles-list").html(msg);
			}
		});
	});
	update_pool();
	$("#filter-form").submit(function() {
		var $inputs = $('#filter-form :input');
		var values = {};
		$inputs.each(function() {
			values[this.name] = $(this).val();
		});
		values['user_id'] = user_id;
		$.ajax({
			type: "POST",
			url: "selection_api.php",
			data: values,
			success: function(msg){
				$("#articles-list").html(msg);
			}
		});
		return false;
	});
});

