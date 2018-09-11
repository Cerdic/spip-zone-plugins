/* global $ */

$.fn.formulaireMassicoterImage = function ( options ) {
	'use strict';

	options = $.extend(
		true,
		{ zoom: 1 },
		options
	);

	var self = this,
		conteneur = self.parent().find('.image-massicot .conteneur'),
		// l'image
		img = self.parent().find('.image-massicot img'),
		largeur_image = parseInt(img.attr('width'), 10),
		hauteur_image = parseInt(img.attr('height'), 10),
		// options
		dimensions_forcees = options.forcer_dimensions instanceof Object,
		largeur_forcee = dimensions_forcees ? parseInt(options.forcer_dimensions.largeur, 10) : NaN,
		hauteur_forcee = dimensions_forcees ? parseInt(options.forcer_dimensions.hauteur, 10) : NaN,
		// On garde en mémoire la sélection telle qu'elle serait sans le zoom,
		// pour pouvoir zoomer-dézoomer perdre de la précision à cause d'erreurs
		// d'arrondi.
		selection_nozoom,
		// widgets
		imgAreaSelector,
		slider,
		selecteur_format = self.find('select[name=format]');

	form_init();
	GUI_init();

	selection_nozoom = form_get();
	img_set(form_get());

	/**
	 * Initialisation du formulaire
	 *
	 * Si les saisies n'ont pas de valeurs définies, on les initialise â la taille
	 * de l'image.
	 */
	function form_init () {

		var valeurs_form = form_get();
		// console.log(valeurs_form);

		if (isNaN(valeurs_form.x1)) {
			form_set({
				x1: 0,
				x2: parseInt(img.attr('width'),10),
				y1: 0,
				y2: parseInt(img.attr('height'),10),
				zoom: 1
			});
		}
	}

	/**
	 * Récupérer les valeurs du formulaire.
	 */
	function form_get () {
		// console.log('form_get');

		return {
			x1: parseInt(self.find('input[name=x1]').val(), 10),
			x2: parseInt(self.find('input[name=x2]').val(), 10),
			y1: parseInt(self.find('input[name=y1]').val(), 10),
			y2: parseInt(self.find('input[name=y2]').val(), 10),
			zoom: self.find('input[name=zoom]').val()
		};
	}

	/**
	 * Mettre à jour les valeurs du formulaire
	 */
	function form_set (valeurs) {
		// console.log('form_set');
		// console.log(valeurs);

		self.find('input[name=x1]').attr('value', valeurs.x1);
		self.find('input[name=x2]').attr('value', valeurs.x2);
		self.find('input[name=y1]').attr('value', valeurs.y1);
		self.find('input[name=y2]').attr('value', valeurs.y2);
		self.find('input[name=zoom]').attr('value', valeurs.zoom);
	}

	/**
	 * Initialisation du widget de sélection
	 */
	function GUI_init () {

		var selection = form_get();

		imgAreaSelector = conteneur.imgAreaSelect({
			instance: true,
			handles: true,
			show: true,
			x1: selection.x1,
			x2: selection.x2,
			y1: selection.y1,
			y2: selection.y2,
			/* On fait toutes les initialisations des autres widgets dans ce
			 * callback, pour être certain de pouvoir utiliser
			 * imgAreaSelector.setOptions sans faire planter le widget. */
			onInit: function () {

				if (dimensions_forcees) {

					imgAreaSelector.setOptions({
						aspectRatio: largeur_forcee + ':' + hauteur_forcee,
						minWidth: Math.round(largeur_forcee * selection.zoom),
						minHeight: Math.round(hauteur_forcee * selection.zoom)
					});

					imgAreaSelector.update();
				}

				slider_init(selection);
				// init_selecteur_format();
				// init_bouton_reinit();
			},
			onSelectChange: function (img, selection) {

				selection_nozoom = selection;
				form_set(selection);
			}
		});
	}

	/**
	 * Mettre à jour le widget de sélection
	 */
	function selector_set (selection) {

		imgAreaSelector.setSelection(
			selection.x1,
			selection.y1,
			selection.x2,
			selection.y2
		);
		imgAreaSelector.update();
	}

	/**
	 * Initialisation du slider
	 */
	function slider_init (selection) {

		slider = self.find('#zoom-slider').slider({
			max: 1,
			min: 0.01,
			value: selection.zoom,
			step: 0.01,
			create: function () {

				// if (dimensions_forcees) {
				// 	selection.zoom = zoom_min_get();
				// 	$(this).slider('option', 'value', selection.zoom);
				// 	maj_image(selection.zoom);
				// 	selection_initiale = forcer_dimensions_selection({
				// 		x1: 0,
				// 		x2: Math.round(img.width()),
				// 		y1: 0,
				// 		y2: Math.round(img.height())
				// 	}, zoom);
				// } else {
				// 	maj_image(zoom);
				// }
				// maj_formulaire(selection_initiale, zoom);
			},
			slide: function (event, ui) {

				var selection = form_get();
				selection.zoom = ui.value;

				form_set(selection);
				img_set(selection);
			}
		});
	}

	/**
	 * Zoomer l'image et met à jour la sélection
	 */
	function img_set (selection) {

		conteneur
			.css('width', selection.zoom * largeur_image + 'px')
			.css('height', selection.zoom * hauteur_image + 'px')
			.css('margin-left', '-' + (Math.max((selection.zoom*largeur_image - 780),0) / 2) + 'px' );

		img
			.css('width', Math.min(1, selection.zoom) * largeur_image + 'px')
			.css('padding-top', (Math.max(1, selection.zoom) - 1) / 2 * hauteur_image);

		selector_set(selection);
	}
};
