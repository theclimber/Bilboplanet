/**
* Gestionnaire de l'admin
*/
var BP_Administrator = new Class({

	Implements: [Events, Options],

	options: {
		IdPage: 'BP_page',
		IdPannel: 'bp_pannel',
		IdPannelSeparator: 'BP_separator',
		IdUserBar: 'BP_userbar',
		IdHeader: 'BP_head'
	},

	initialize: function(options){
		this.setOptions(options);
		this.pages = $(this.options.IdPage);
		this.pannel = $(this.options.IdPannel);
		this.pannelSeparator = $(this.options.IdPannelSeparator);
		this.userbar = $(this.options.IdUserBar);

		// Gestion du redimentionnement du navigateur
		this.resizePages();
		window.addEvent('resize', this.resizePages.bind(this));

		// Ajoute les tips
		this.tips = new Tips('.tips',{className: 'tooltip', offsets: {'x': -16, 'y': 16} });
		this.tips.addEvent('show', function(tip){
			tip.setStyle('opacity', 0.8);
		});

		// Ajoute ACP box
		this.acpbox = new ACPbox({className: 'acp'});
		Window.implement({
				$alert: function(message, title, options) {
					this.acpbox.alert(message, title, options);
				}.bind(this),
				$confirm: function(message, title, options) {
					this.acpbox.confirm(message, title, options);
				}.bind(this),
				$prompt: function(message, title, options) {
					this.acpbox.prompt(message, title, options);
				}.bind(this)
		});

		// Active le Pannel
		this.launchPannel();
	},

	/**
	* Redimentionne l'element Page de l'admin en fonction de la taille du navigateur
	*/
	resizePages: function() {
		var Pages_height = window.getSize().y;
		if($(this.options.IdHeader)) // Header
			Pages_height = Pages_height - $(this.options.IdHeader).getSize().y;

		this.pannelSeparator.setStyle('height', Pages_height);
		this.pannelSeparator.getElements('div').setStyle('height', Pages_height);
		this.pannel.setStyle('height', Pages_height);
		this.pages.setStyle('height', Pages_height);
	},

	/**
	* Active le Pannel
	*/
	launchPannel: function() {
		// Ajoute le Toggle du pannel
		this.pannel.set('morph',{duration: 500});
		this.pannelWidth = this.pannel.getStyle('width').toInt();
		this.pannelSeparator.addEvent('mousedown', function(){
			// Hide
			if(this.pannel.getStyle('margin-left').toInt() == 0) {
				this.pannel.morph({'margin-left': - this.pannelWidth});
			}
			// Show
			else{
				this.pannel.morph({'margin-left': 0});
			}
			
		}.bind(this));
	}
});

/**
* Alert, Confirm, Prompt Box
*/
var ACPbox = new Class({
	Implements: [Events, Options],

	options: {
		zindex: 999,
		container: null,
		className: null,
		overlayClassName: 'acp-overlay',
		overlayOpacity: 0.5,
		overlayShowDuration: 250,
		buttonClassName: 'button',
		buttonOkText : 'OK',
		buttonCancelText : 'Annuler',
		inputClassName: 'input'
	},

	initialize: function(options) {
		this.setOptions(options);

		// Si on a un overlay
		if(this.options.overlayOpacity.toFloat() > 0) {
			this.overlay = new Element('div').inject(this.options.container || document.body);
			if (this.options.overlayClassName)
				this.overlay.addClass(this.options.overlayClassName);
			this.overlay.setStyles({'position': 'absolute', 'top': 0, 'left': 0, 'opacity': 0, 'visibility': 'hidden', 'z-index': (this.options.zindex - 1)}).set('morph', {duration: this.options.overlayShowDuration});
		}

		// Création de la box
		this.acpbox = new Element('div').inject(this.options.container || document.body);
		if (this.options.className)
			this.acpbox.addClass(this.options.className);
		this.acpbox.setStyles({'position': 'absolute', 'visibility': 'hidden', 'z-index': this.options.zindex}).set('morph');

		// Préparation des champs de formulaire
		this.form = new Element('form', {
			'class': 'acp-form'
		});
		this.input = new Element('input');
		if (this.options.inputClassName) {
			this.input.addClass(this.options.inputClassName);
		}

		// Préparation des buttons
		this.buttons = new Element('div', {
			'class': 'acp-buttons'
		});
		// Boutton OK
		this.buttonOk = new Element('input', {
			'type': 'submit',
			'value': this.options.buttonOkText
		});
		// Boutton Annuler
		this.buttonCancel = new Element('input', {
			'type': 'submit',
			'value': this.options.buttonCancelText
		});
		if (this.options.buttonClassName) {
			this.buttonOk.addClass(this.options.buttonClassName);
			this.buttonCancel.addClass(this.options.buttonClassName);
		}
	},

	/**
	* Affiche acpbox
	*/
	show: function() {
		// Affiche l'overlay
		if(this.overlay) {
			this.overlay.setStyles({
				'height': window.getScrollSize().y,
				'width': window.getScrollSize().x
			}).morph({'opacity': this.options.overlayOpacity.toFloat()});
		}

		// Affiche la box
		this.acpbox.setStyles({
			visibility: 'visible',
			opacity: 1
		});

		// Donne le focus au boutton
		if(this.acpbox.retrieve('acp:type') == 'alert')
			this.buttonOk.focus();
		else if(this.acpbox.retrieve('acp:type') == 'confirm')
			this.buttonCancel.focus();
		else if(this.acpbox.retrieve('acp:type') == 'prompt')
			this.input.focus();

		this.fireEvent('show', this);
	},

	/**
	* Ferme acpbox et envois les infos
	*/
	close: function(hide) {
		if(this.acpbox.retrieve('acp:hide') || hide) {
			// Enleve les events des actions pour ne pas poster 2 fois l'info (ex. doubleclic)
			this.form.removeEvents().addEvent('submit', function(e){ e.stop(); });
			this.buttonOk.removeEvents();
			this.buttonCancel.removeEvents();

			// Cache la box
			this.acpbox.setStyles({
				visibility: 'hidden',
				opacity: 0
			});

			if(this.overlay)
				this.overlay.morph({'opacity': 0 });
			
			this.fireEvent('hide', this);
		}

		this.fireEvent('close', [this.acpbox.retrieve('acp:return'), this]);
	},

	/**
	* Clean acpbox et l'initialise pour une nouvelle execution
	*/
	clean: function() {
		// Enleve les events des actions (buttons + form)
		this.form.removeEvents();
		this.buttonOk.removeEvents();
		this.buttonCancel.removeEvents();

		// Rezet l'events close
		this.removeEvents('close');

		// Rezet le type et le return et hide
		this.acpbox.store('acp:return', false).store('acp:type', null).store('acp:hide', true);

		// On vide la box
		this.acpbox.setStyles({'visibility': 'hidden', 'opacity':0}).empty();
		// SI l'overlay existe
		if(this.overlay)
			this.overlay.setStyles({'visibility': 'hidden', 'opacity':0});
	},

	alert: function(message, title, options) {
		var params = Array.link(arguments, {message: String.type, title: String.type, options: Object.type});
		params.options = params.options || {};

		// on a un message
		if (params.message) {
			this.clean();
			this.acpbox.store('acp:type', 'alert');
			if(params.options.hide === false) this.acpbox.store('acp:hide', false);
			if(params.options.onClose && $type(params.options.onClose) == 'function')
				this.addEvent('close', params.options.onClose);
			// Id de la box et de l'overlay
			if(params.options.id) {
				this.acpbox.set('id', params.options.id);
				if(this.overlay)
					this.overlay.set('id', params.options.id + '-overlay');
			}

			// Titre
			if(params.title) new Element('h1', {'class': 'acp-title', 'html': params.title}).inject(this.acpbox);
			// Message
			new Element('p', {'class': 'acp-text', 'html': params.message}).inject(this.acpbox);
			// Button
			this.buttons.inject(this.acpbox);
			this.buttonOk.addEvent('click', function() {
				this.acpbox.store('acp:return', true);
				this.close();
			}.bind(this)).inject(this.buttons);

			this.show();
		}
	},

	confirm: function(message, title, options) {
		var params = Array.link(arguments, {message: String.type, title: String.type, options: Object.type});
		params.options = params.options || {};

		// on a un message
		if (params.message) {
			this.clean();
			this.acpbox.store('acp:type', 'confirm');
			if(params.options.hide === false) this.acpbox.store('acp:hide', false);
			if(params.options.onClose && $type(params.options.onClose) == 'function')
				this.addEvent('close', params.options.onClose);
			// Id de la box et de l'overlay
			if(params.options.id) {
				this.acpbox.set('id', params.options.id);
				if(this.overlay)
					this.overlay.set('id', params.options.id + '-overlay');
			}

			// Titre
			if(params.title) new Element('h1', {'class': 'acp-title', 'html': params.title}).inject(this.acpbox);
			// Message
			new Element('p', {'class': 'acp-text', 'html': params.message}).inject(this.acpbox);
			// Button
			this.buttons.inject(this.acpbox);
			this.buttonOk.addEvent('click', function() {
				this.acpbox.store('acp:return', true);
				this.close();
			}.bind(this)).inject(this.buttons);
			this.buttonCancel.addEvent('click', function() {
				this.acpbox.store('acp:return', false);
				this.close();
			}.bind(this)).inject(this.buttons);

			this.show();
		}
	},

	prompt: function(message, title, options) {
		var params = Array.link(arguments, {message: String.type, title: String.type, options: Object.type});
		params.options = params.options || {};

		// on a un message
		if (params.message) {
			this.clean();
			this.acpbox.store('acp:type', 'prompt');
			if(params.options.hide === false) this.acpbox.store('acp:hide', false);
			if(params.options.onClose && $type(params.options.onClose) == 'function')
				this.addEvent('close', params.options.onClose);
			// Id de la box et de l'overlay
			if(params.options.id) {
				this.acpbox.set('id', params.options.id);
				if(this.overlay)
					this.overlay.set('id', params.options.id + '-overlay');
			}

			// Titre
			if(params.title) new Element('h1', {'class': 'acp-title', 'html': params.title}).inject(this.acpbox);
			// Message
			new Element('p', {'class': 'acp-text', 'html': params.message}).inject(this.acpbox);
			// Input
			this.form.inject(this.acpbox);
			this.input.inject(this.form);
			this.input.set('type',params.options.type || 'text');
			this.input.set('value',params.options.value || '');
			this.form.addEvent('submit', function(e) {
				e.stop();
				if(this.input.get('value') != '')
					this.acpbox.store('acp:return', this.input.get('value'));
				else
					this.acpbox.store('acp:return', false);
				this.close();
			}.bind(this));

			// Button
			this.buttons.inject(this.acpbox);
			this.buttonOk.addEvent('click', function() {
				if(this.input.get('value') != '')
					this.acpbox.store('acp:return', this.input.get('value'));
				else
					this.acpbox.store('acp:return', false);
				this.close();
			}.bind(this)).inject(this.buttons);
			this.buttonCancel.addEvent('click', function() {
				this.acpbox.store('acp:return', false);
				this.close();
			}.bind(this)).inject(this.buttons);

			this.show();
		}
	}
});
