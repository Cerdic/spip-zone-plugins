/* global $ */

$.fn.formulaireMassicoterImage = function ( options ) {
	'use strict';

	options = $.extend(
		true,
		{ zoom: 1 },
		options
	);

	var self = this,
		img = self.parent().find('.image-massicot img');

	form_init();

	/**
	 * Initialisation du formulaire
	 *
	 * Si les saisies n'ont pas de valeurs définies, on les initialise â la taille
	 * de l'image.
	 */
	function form_init () {

		var valeurs_form = form_get();
		console.log(valeurs_form);

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

		console.log('form_get');

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
	function form_set(valeurs) {
		console.log('form_set');
		console.log(valeurs);

		self.find('input#champ_zoom').attr('value', valeurs.zoom);
		self.find('input[name=x1]').attr('value', valeurs.x1);
		self.find('input[name=x2]').attr('value', valeurs.x2);
		self.find('input[name=y1]').attr('value', valeurs.y1);
		self.find('input[name=y2]').attr('value', valeurs.y2);
	}
};
