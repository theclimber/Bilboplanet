$(document).ready(function($) {

	/*******************************************************************/
	/* Redimensionnement des div par rapport a la taille de la fenetre */
	/*******************************************************************/

	// Lorsque le document est charge on appelle la fonction resizeDiv()
	resizeDiv();
	
	// Lorsque la fenetre est redimensionnee on appelle la fonction resizeDiv()
	$(window).bind('resize', function(){
		resizeDiv();		
	})
	
	function resizeDiv() {
		var Head_height = $('#BP_head').height();
		var Page_width = $(window).width();
		var Page_height = $(window).height();
		var Div_height = Page_height - Head_height;
		$("#BP_separator").css("height", Div_height);
		$("#BP_page").css("height", Div_height);
		$("#bp_pannel").css("height", Div_height);
	};
	
	/*****************************************************************************/
	/* Gestion des liens dont l'id est 'BP_Logout' et redirection vers le        */
	/* contenu de l'attribut rel									             */
	/*****************************************************************************/
	$('a[id="BP_Logout"]').click(function(e) {
		//Annule l'action par defaut
		e.preventDefault();
		
		// Verification de la presence de l'attribut rel
		if ($(this).attr('rel')) {
		
			//Recupere le contenu de l'attribut rel
			var url = $(this).attr('rel');
			
			//Redirection
			$(location).attr('href', url);	
		}
	})
	
	/*****************************************************************************/
	/* Gestion des liens dont l'attribut 'name' est modal afin d'afficher une    */
	/* div par dessus le contenu de la page avec un effet de transition          */
	/*****************************************************************************/
	
	//Sélectionner tous les liens dont le nom est modal
	$('a[name=modal]').click(function(e) {
		//Annule l'action par defaut
		e.preventDefault();
		
		//Recupere l'attribut href
		var id = $(this).attr('href');
	
		//Recupere la hauteur et la largeur de la fenetre
		var maskHeight = $(window).height();
		var maskWidth = $(window).width();
	
		//Définit la dimension du masque
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//Effet de transition
		$('#mask').fadeTo("slow",0.5);	
	
		//Get the window height and width
		var winH = $(window).height();
		var winW = $(window).width();

		//Centre le popup
		$(id).css('top',  winH/2-$(id).height()/2);
		$(id).css('left', winW/2-$(id).width()/2);
			
		//Effet de transition
		$(id).fadeIn(2000); 
	});
	
	//Bouton Close
	$('.window .close').click(function (e) {
		// Annule l'action par defaut
		e.preventDefault();
		// Fermeture de la fenetre modal
		$('#mask').hide();
		$('.window').hide();
	});		
	
	//Si on clique sur le mask on ferme la fenêtre
	$('#mask').click(function () {
		$(this).hide();
		$('.window').hide();
	});
	
	/*****************************************************************************/
	/* Fonction permettant l'affichage des tips en passant la souris sur un lien */
	/* Par defaut affiche l'attribtut 'title'									 */
	/* Si on spécifie le type 'rel' on affichage le contenu de l'attribut 'rel'	 */
	/*****************************************************************************/
	$.fn.tips = function(settings) {
		// Options par defaut: modifiable par l'appel de la fonction
		options =  {
			type: 		'title',	// type par défaut
			offsetX:    10, 		// positionnement horizontal par defaut
			offsetY:    -5,			// positionnement vertical par défaut
			delay:      500,		// Délai d'affichage par défaut
			baseLine:   'middle'
		}; 
		var options = $.extend(options, settings); 
		 
		return this.each(function(){ 
			var $$ 	   	= $(this); 					// Recupere l'element actuel
			var aTitle 	= $$.attr('title') || ''; 	// Recupere le champs title
			var aRel   	= $$.attr('rel')   || ''; 	// Recupere le champs rel
			var aTip   	= '';
			var timer;

			// Quand le curseur survole un element 
			$$.mouseover(function(e) {           
				// Supprime l'attribut 'title' afficher par defaut par le navigateur 
				$$.attr('title', '');
				
				// Initialisation du tip par defaut avec l'attribut 'title'
				if (aTitle) {
					aTip = $("<div class='tip-text'><span>"+ aTitle +"</span></div>");
				}
				
				// Si lors de l'appel le type 'rel' est specifie le tip prend la valeur de l'attribut 'rel'
				if (options.type == 'rel') {
					if (aRel) {
						aTip = $("<div class='tip-text'><span>"+ aRel +"</span></div>");
					}
				}

				// Le tip precedemment cree est ajoute au Body et masque
				aTip.appendTo("body").hide().css({ position:'absolute', top:0, left:0 });

				// Permet le suivi du tip avec la souris 
				$$.mousemove(function(e) { 
					aTip.css({
						left: e.pageX + options.offsetX + "px",
						top:  e.pageY + options.offsetY + "px"                            
					});
				}); 
				
				// Delai d'affichage du type - Modifiable avec l'option 'delay'
				timer = setTimeout( function() { aTip.show() }, options.delay);
			}) 
				
			// Quand le curseur ne survole plus un element 
			$$.mouseout(function(e) { 
				clearTimeout(timer);
				// On remet le title du navigateur en place 
				$$.attr("title", aTitle);   
				// On supprime le suivi de deplacement de la souris
				$$.unbind("mousemove");      
				// On supprime le tip
				aTip.remove(); 
			}); 
		}); 
	};
	
	/*****************************************************************************/
	/* Cocher/Decocher toutes les checkbox d'un formulaire                       */
	/* dont l'id est checkAll													 */
	/*****************************************************************************/
	$("#checkAllCheckboxes").click(function() {
		var checked_status = this.checked;
		$("input[id='checkAll']").each(function() {
			this.checked = checked_status;
		})
	});
	
	/******************************************************************************/
	/* Preview Textarea													  		  */
	/******************************************************************************/
	var $$ = $.fn.preview = function(settings) {
		// Options par défaut: modifiable par l'appel de la fonction
		var options =  {
			opacity: '0.8',
			text: {
				preview: 'Preview',
				close: 'Close'
			}
		};
		
		// Si des options sont passees lors de l'appel on les prends en consideration
		var $options = $.extend(options, settings); 
		
		// Mise en place de l'overlay
		var preview = $(document.createElement('div')).css({
			display: 'none',
			opacity: $options.opacity
		}).attr('id','overlay');
				
		// Rajout dans le body du preview
		$('body').append(preview);
		
		return this.each(function(){
			
			var textarea = $(this);
			
			// Generation du bouton Close
			var buttonClose = $(document.createElement('input')).attr({
				value:$options.text.close,
				type: 'button'
			}).addClass('button close');
			
			// Generation des div			
			var content = $(document.createElement('div')).addClass('preview_div');
			var header = $(document.createElement('p')).addClass('preview_header').text($options.text.preview+':');
			var message = $(document.createElement('div')).addClass('preview_message');
			var footer = $(document.createElement('div')).addClass('preview_footer');
			
			
			// Construction du footer avec le bouton Close
			footer.append('<hr />');
			footer.append(buttonClose);
			
			// Construction de la div: assemblage header + message + footer
			content.append(header).append(message).append(footer);
			
			content.fadeIn(4000);
			preview.after(content);
			
			// Effet de transition pour l'affichage
			preview.fadeIn(2000);	
			preview.fadeTo("slow", $options.opacity);
			
			// Affichage du contenu du textarea
			message.hide().html(htmlspecialchars(textarea.val())).fadeIn(6000);
			
			// Loop on each tag <code></code> 
			$('.preview_message').find('code').each(function(){
				$(this).html($(this).html().replace("/&/&amp,").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;"));
			});

			
			// Fermeture de la preview lorsque l'on clique sur le bouton close
			buttonClose.click(function() {
				preview.fadeOut($options.fade);
				content.fadeOut($options.fade,function() { message.empty(); });
				textarea.focus(); // On donne le focus à la textarea
				// On vide les objets
				content.empty;
				header.empty;
				message.empty;
				footer.empty;
			});
		});
	};
	
	/************************************************/
	/* Function pour convertir des charactères		*/
	/************************************************/
	function htmlspecialchars(str) {
		//str = str.replace(/&/g,"&amp;")
		//str = str.replace(/\"/g,"&quot;")
		//str = str.replace(/\'/g,"&#039;")
		//str = str.replace(/</g,"&lt;")
		//str = str.replace(/>/g,"&gt;")
		str = str.replace(/\n/g,"<br />")
		str = str.replace(/\n\n+/g, '<br /><br />')
		return str
	}
	
	/*************************************************/
	/* Collapse Menu								 */
	/*************************************************/
	$("#BP_separator").click(function(){
	
		// var $$ = $(this);
		var pannel_id = "#bp_pannel"; // Nom de la div a masquer
		var pannel_size = $("#bp_pannel").width(); // Recupere la propriete width
		var pannel_marginL = $(pannel_id).css("marginLeft"); // Recupere la marge a gauche
		
		// Si la marge à gauche est à 0px
		if (pannel_marginL == "0px") {
			// On masque la div "bp_pannel"
			$(pannel_id).animate({marginLeft:"-"+pannel_size+"px"}, 500 );
			// $$.attr("title", "Show");
		}
		else {
			// Sinon on affiche la div
			$(pannel_id).animate({marginLeft:"0px"}, 500 );
			// $$.attr("title", "Hide");
		}

	});
	
	/************************************************/
	/* Flash Box									*/
	/************************************************/
	$.fn.flashmsg = function() {
		var autoclose = false;
		var flash_class = $(this).attr('class');
		if (flash_class == 'flash_notice') {
			var autoclose = true;
		}
		var flash_content = $(this).html();
		var flash_overlay = $(document.createElement('div')).css({
			display: 'none'
		}).attr('class','flash_overlay').attr('id', 'flash_overlay');
		
		var flash_box = $(document.createElement('div')).attr('class', 'flash_box').attr('id', 'flash_box').addClass(flash_class);
		$(flash_box).html('<a class="flash_box_close" id="flash_box_close"></a>\n'+$(this).html());

		$('body').append(flash_overlay).append(flash_box);
				
		$('#flash_overlay').fadeIn('fast',function(){
			$('#flash_box').animate({'top':'160px'},500);
			//If autoclose = true (flashbox class = flash_notice) then define a timer (3 seconds) to close flashbox
			if(autoclose) {
				setTimeout(function() {$('#flash_box_close').click();} , 3000);
			}
		});

		// Simulate click on div #flash_box_close when press ESC or ENTER on keyboard
		$(document).keypress(function(e) {
			if ($('#flash_overlay').length != 0) {
				if ( e.keyCode == '27' || e.keyCode == '13') {
					$('#flash_box_close').click();
					if ( e.keyCode == '13') {
						return false;
					}
					else {
						return true;
					}
				}
			}
		});
		
		$('#flash_box_close, #flash_overlay, #flash_box, input[type="button"], input[type="submit"]').click(function(){
			$('#flash_box').animate({'top':'-200px'},500,function(){
				$('#flash_overlay').fadeOut('fast');
				//flash_class.empty;
				//flash_content.empty;
				flash_box.remove();
				flash_overlay.remove();
			});
		});
	};
	
})
