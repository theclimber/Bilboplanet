/* Fonction de vote */
function vote(num_article, token, type) {
  jQuery.ajax({
  type: "POST",
  url: "api/votes.php",
  data: "num_article=" + num_article + "&token=" + token + "&type=" + type,
  success: function(msg){
	if (msg == 'needlogin') {
		display_login();
	} else {
		if(type == "positif") {
		  val = parseInt( $.trim($("#vote" + num_article).html()) ) + 1;
		} else {
		  val = parseInt( $.trim($("#vote" + num_article).html()) ) - 1;
		}

		/* On supprime les liens de votes */
		$("#aoui" + num_article).remove();
		$("#anon" + num_article).remove();

		/* On incremente le compteur de vote */
		$("#vote" + num_article).text(val.toString());

		/* on rajoute un class au nombre de vote pour changer la couleur */
		$("#vote" + num_article).removeClass("vote");
		$("#vote" + num_article).addClass("avote");

		/* On reaffiche les images de votes sans possibilite de vote */
		$("#vote" + num_article).after("<span class='avote'>&nbsp;votes<br/><span id='imgoui' title='vote oui'></span><span id='imgnon' title='vote non'></span>");
	}
  },
  error: function() {
    alert("Error : this should not happen");
  }
  });
}
