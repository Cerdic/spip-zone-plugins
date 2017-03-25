/* jshint strict: true, undef: true, unused: true, curly: true,
   eqeqeq: true, freeze: true, funcscope: true, futurehostile: true,
   nonbsp: true */
/* globals $ */

$.fn.formulaireMassicoterImage = function ( options ) {
	"use strict";

	options = $.extend(true,
					   {
						   zoom: 1
					   },
					   options
					  );

	var zoom = options.zoom,
		img = $('.image-massicot img'),
		largeur_image = parseInt(img.attr('width'), 10),
		hauteur_image = parseInt(img.attr('height'), 10),
		premier_chargement = isNaN(parseInt($('input[name=x1]').val(), 10)),
		selection_initiale,
		selection_nozoom,
		slider,
		imgAreaSelector,
		mode_dimensions_forcees = (options.forcer_dimensions !== null),
		forcer_hauteur,
		forcer_largeur,
		select_format = $('#champ_format');

	/* Si le formulaire n'a pas été chargé en php, on s'en occupe ici. */
	if (premier_chargement) {
		selection_initiale = {
			x1: 0,
			x2: parseInt(img.attr('width'),10),
			y1: 0,
			y2: parseInt(img.attr('height'),10)
		};
	} else {
		selection_initiale = {
			x1: parseInt($('input[name=x1]').val(), 10),
			x2: parseInt($('input[name=x2]').val(), 10),
			y1: parseInt($('input[name=y1]').val(), 10),
			y2: parseInt($('input[name=y2]').val(), 10)
		};
	}

	if (mode_dimensions_forcees) {
		forcer_largeur = parseInt(options.forcer_dimensions.largeur, 10);
		forcer_hauteur = parseInt(options.forcer_dimensions.hauteur, 10);
	}

	/* On garde en mémoire la sélection telle qu'elle serait sans le
	   zoom, pour pouvoir zoomer-dézoomer perdre de la précision à
	   cause d'erreurs d'arrondi. */
	selection_nozoom = {
		x1: selection_initiale.x1 / zoom,
		x2: selection_initiale.x2 / zoom,
		y1: selection_initiale.y1 / zoom,
		y2: selection_initiale.y2 / zoom
	};

	/* On crée ensuite le slider de zoom */
	slider = $('#zoom-slider').slider({
		max: 1,
		min: 0.01,
		value: options.zoom,
		step: 0.01,
		create: function () {

			zoom = $('input#champ_zoom').attr('value');

			if (premier_chargement && mode_dimensions_forcees) {
				zoom = calculer_zoom_min();
				$(this).slider('option', 'value', zoom);
				maj_image(zoom);
				selection_initiale = forcer_dimensions_selection({
					x1: 0,
					x2: Math.round(img.width()),
					y1: 0,
					y2: Math.round(img.height())
				}, zoom);
			} else {
				maj_image(zoom);
			}
			maj_formulaire(selection_initiale, zoom);
		},
		slide: function (event, ui) {

			zoom = ui.value;

			maj_image(zoom);

			var selection = zoomer_selection(selection_nozoom, zoom);

			if (mode_dimensions_forcees) {
				selection = forcer_dimensions_selection(selection, zoom);
			}

			maj_selection(selection);
			maj_formulaire(selection, zoom);

			imgAreaSelector.setOptions({
				minWidth: Math.round(forcer_largeur * zoom),
				minHeight: Math.round(forcer_hauteur * zoom)
			});
		}
	});

	/* On crée le widget de sélection */
	imgAreaSelector = img.imgAreaSelect({
		instance: true,
		handles: true,
		show: true,
		x1: selection_initiale.x1,
		x2: selection_initiale.x2,
		y1: selection_initiale.y1,
		y2: selection_initiale.y2,
		onSelectChange: function (img, selection) {
			selection_nozoom = {
				x1: selection.x1 / zoom,
				x2: selection.x2 / zoom,
				y1: selection.y1 / zoom,
				y2: selection.y2 / zoom
			};
			maj_formulaire(selection, zoom);
		}
	});

	/* Options propres au mode avec dimensions imposées */
	if (mode_dimensions_forcees) {

		slider.slider('option', 'min', calculer_zoom_min());

		imgAreaSelector.setOptions({
			aspectRatio: forcer_largeur + ':' + forcer_hauteur,
			minWidth: Math.round(forcer_largeur * zoom),
			minHeight: Math.round(forcer_hauteur * zoom)
		});
	}

	/* Gestion du selecteur de format */
	select_format.change(function (e) {

		var format = e.target.value;

		afficher_erreur('');

		if (format) {
			mode_dimensions_forcees = true;

			format = format.split(':');
			forcer_largeur = parseInt(format[0], 10);
			forcer_hauteur = parseInt(format[1], 10);

			if ((largeur_image < forcer_largeur) || (hauteur_image < forcer_hauteur)) {
				afficher_erreur(options.messages.erreur_image_trop_petite);
			} else {
				slider.slider('option', 'min', calculer_zoom_min());

				imgAreaSelector.setOptions({
					aspectRatio: forcer_largeur + ':' + forcer_hauteur,
					minWidth: Math.round(forcer_largeur * zoom),
					minHeight: Math.round(forcer_hauteur * zoom)
				});

				selection_initiale = forcer_dimensions_selection(
					imgAreaSelector.getSelection(),
					zoom
				);

				maj_selection(selection_initiale);

				maj_formulaire(selection_initiale, zoom);

				return;
			}
		}

		mode_dimensions_forcees = false;

		forcer_largeur = null;
		forcer_hauteur = null;

		slider.slider('option', 'min', 0.01);

		imgAreaSelector.setOptions({
			aspectRatio: '',
			minWidth: 1,
			minHeight: 1
		});
	})
		.trigger('change');

	/* Et enfin on s'occupe du bouton de réinitialisation */
	$('#formulaire_massicoter_image_reset').click(function (e) {

		$('#zoom-slider').slider('option', 'value', 1);
		select_format.val('').trigger('change');

		maj_image(1);

		var selection = {
			x1: 0,
			x2: Math.round(img.width()),
			y1: 0,
			y2: Math.round(img.height())
		};
		selection_nozoom = selection;
		maj_selection(selection);
		maj_formulaire(selection, 1);

		e.preventDefault();
		return false;
	});

	/*************/
	/* Fonctions */
	/*************/

	/* Une fonction pour afficher les erreurs */
	function afficher_erreur (msg) {

		$('#formulaire_massicoter_image .erreur_message').remove();

		if (msg) {
			$('#formulaire_massicoter_image ul')
				.before('<div class="erreur_message">' + msg + '</div>');
		}

		imgAreaSelector.update();
	}

	/* Mise à jour du formulaire */
	function maj_formulaire (selection, zoom) {

		/* En mode "dimensions forcées", on permet des sélections plus
		   grandes que les dimensions voulues. Il faut alors tout
		   remettre à la bonne échelle. */
		if (mode_dimensions_forcees) {
			var zoom_selection = calculer_zoom_selection(selection);
			zoom = zoom * zoom_selection;
			selection = {
				x1: Math.round(selection.x1 * zoom_selection),
				x2: Math.round(selection.x1 * zoom_selection) + forcer_largeur,
				y1: Math.round(selection.y1 * zoom_selection),
				y2: Math.round(selection.y1 * zoom_selection) + forcer_hauteur
			};
		}

		$('input#champ_zoom').attr('value', zoom);
		$('input[name=x1]').attr('value', selection.x1);
		$('input[name=x2]').attr('value', selection.x2);
		$('input[name=y1]').attr('value', selection.y1);
		$('input[name=y2]').attr('value', selection.y2);

		$('.dimensions').html((selection.x2 - selection.x1) + ' x ' + (selection.y2 - selection.y1));
	}

	/* Une fonction qui agrandi ou rapetisse l'image */
	function maj_image (zoom) {

		img
			.css('width', zoom * largeur_image + 'px')
			.css('height', 'auto')
			.css('margin-left', '-' + (Math.max((zoom*largeur_image - 780),0) / 2) + 'px' );
	}

	/* Une fonction pour mettre à jour la sélection */
	function maj_selection (selection) {

		imgAreaSelector.setSelection(
			selection.x1,
			selection.y1,
			selection.x2,
			selection.y2
		);
		imgAreaSelector.update();
	}

	/* Calculer l'effet d'un zoom sur une sélection.
	   Retourne la sélection zoomée. */
	function zoomer_selection (selection, zoom) {

		var nouvelle_selection = {};

		nouvelle_selection.x1 = Math.round(selection.x1 * zoom);
		nouvelle_selection.x2 = Math.round(selection.x2 * zoom);
		nouvelle_selection.y1 = Math.round(selection.y1 * zoom);
		nouvelle_selection.y2 = Math.round(selection.y2 * zoom);

		nouvelle_selection.x1 = Math.max(0, nouvelle_selection.x1);
		nouvelle_selection.y1 = Math.max(0, nouvelle_selection.y1);
		nouvelle_selection.x2 = Math.min(
        nouvelle_selection.x2,
        Math.round(img.width())
    );
		nouvelle_selection.y2 = Math.min(
        nouvelle_selection.y2,
        Math.round(img.height())
    );

		return nouvelle_selection;
	}

	/* Retourne une sélection aux dimensions reçues en option, en
	   essayeant de rester le plus proche possible de la sélection
	   passée en paramètre. On essaie de garder le même centre. */
	function forcer_dimensions_selection(selection, zoom) {

		var x1 = selection.x1,
			x2 = selection.x2,
			y1 = selection.y1,
			y2 = selection.y2,
			zoom_max = Math.min(
				(largeur_image * zoom) / forcer_largeur,
				(hauteur_image * zoom) / forcer_hauteur
			),
			echelle_x = (x2 - x1) / forcer_largeur,
			echelle_y = (y2 - y1) / forcer_hauteur,
			echelle = Math.min(
				Math.max(zoom, (echelle_x + echelle_y) / 2),
				zoom_max
			),
			largeur_selection = forcer_largeur * echelle,
			hauteur_selection = forcer_hauteur * echelle,
			centre = {
				x: (x2 + x1) / 2,
				y: (y2 + y1) / 2
			};

		x1 = Math.round(Math.max(0, centre.x - (largeur_selection / 2)));
		x2 = Math.round(x1 + largeur_selection);
		y1 = Math.round(Math.max(0, centre.y - (hauteur_selection / 2)));
		y2 = Math.round(y1 + hauteur_selection);

		if (x2 > largeur_image) {
			x1 = x1 - (x2 - largeur_image);
			x2 = largeur_image;
		}
		if (y2 > hauteur_image) {
			y1 = y1 - (y2 - hauteur_image);
			y2 = hauteur_image;
		}

		return {
			x1: x1,
			x2: x2,
			y1: y1,
			y2: y2
		};
	}

	/* La plus grande valeur de zoom possible avant d'être plus petit
	   que les dimensions forcées */
	function calculer_zoom_min() {

		return Math.min(
			1,
			Math.max(
				forcer_largeur / largeur_image,
				forcer_hauteur / hauteur_image
			)
		);
	}

	/* Calcul le zoom qu'il faut pour mettre une selection aux
	   dimensions imposées */
	function calculer_zoom_selection(selection) {

		var zoom_x = forcer_largeur / (selection.x2 - selection.x1),
			zoom_y = forcer_hauteur / (selection.y2 - selection.y1);

		return Math.max(zoom_x, zoom_y);
	}
};
