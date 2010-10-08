/*----------------------------------------------------------------------*/
/*    Fonction qui verifie si la valeur d'un element est vide ou pas    */
/*----------------------------------------------------------------------*/

function isEmpty(element) {
  var tmp;
  if(element.value == "") {
    tmp = true;
  } else {
    tmp = false;
  }
  return tmp;
}


/*-------------------------------------------------------*/
/*    Fonction qui verifie la bonne saisie d'un email    */
/*------------------------------------------------------ */

function verifMail(element) {
  var tmp;
  var arobase = element.value.indexOf("@",1);
  var point  = element.value.indexOf(".", arobase+1);
  if((arobase > -1) && (element.value.length > 2) && (point > 1)) {
    tmp = true;
  } else {
    tmp = false;
  }
  return tmp;
} 

/*-------------------------------------------------------*/
/*    Verification de la bonne saisie du formulaire      */
/*------------------------------------------------------ */
function verifForm(myForm) {

  var tmp = false;

  if(isEmpty(myForm.nom))  {
  
    alert("Veuillez entrer votre nom !");
    myForm.nom.focus();
    tmp = false;
    
  } else {
    
    if(isEmpty(myForm.email) || !verifMail(myForm.email))  {
      
      alert("Veuillez entrer une adresse email correcte !");
      myForm.email.focus();
      tmp = false;
      
    } else {
    
      if(isEmpty(myForm.url))  {

        alert("Veuillez entrer l'adresse de votre site web !");
	myForm.url.focus();
	tmp = false;
    
      } else {

        if(isEmpty(myForm.rss))  {

	  alert("Veuillez entrer au moin une categorie !");
	  myForm.rss.focus();
	  tmp = false;

	} else {

	  if(!myForm.ok.checked) {

	    alert("Veuillez accepter le reglement !");
	    myForm.ok.focus();
	    tmp = false;

	  } else {

	    tmp = true;
 	  }
 	}
      }
    }
  }
  return tmp;
}

