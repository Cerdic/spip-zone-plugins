/* global $ */

$.fn.formulaireMassicoterImage = function ( options ) {
	'use strict';

	options = $.extend(
		true,
		{ zoom: 1 },
		options
	);

	var self = this,
		conteneur = self.parent().find('.image-massicot'),
		img = conteneur.find('img'),
		largeur_image = parseInt(img.attr('width'), 10),
		hauteur_image = parseInt(img.attr('height'), 10),
		// options
		dimensions_forcees = options.forcer_dimensions instanceof Object,
		largeur_forcee = dimensions_forcees ? parseInt(options.forcer_dimensions.largeur, 10) : NaN,
		hauteur_forcee = dimensions_forcees ? parseInt(options.forcer_dimensions.hauteur, 10) : NaN,
		// On garde en mémoire la sélection telle qu'elle a été saisie via le
		// widget de sélection, en ignorant l'effet d'éventuels zooms à venir.
		// Ça permet d'éviter une perte de précision lorsqu'on zoome et dézoome
		// avec le slider.
		derniere_selection_widget,
		// widgets
		imgAreaSelector,
		slider,
		selecteur_format = self.find('select[name=format]'),
		round = Math.round,
		max = Math.max,
		min = Math.min,
		tests = [];

	tests.push(make_test(
		'Jouer les tests joue les tests',
		function () { return true; }
	));

	form_init();
	GUI_init();


	/**
	 * Initialisation du formulaire
	 *
	 * Si les saisies n'ont pas de valeurs définies, on les initialise â la taille
	 * de l'image.
	 */
	function form_init () {

		var valeurs_form = form_get();
		// console.log('-- form init');
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
			zoom: parseFloat(self.find('input[name=zoom]').val())
		};
	}

	/**
	 * Mettre à jour les valeurs du formulaire
	 */
	function form_set (selection) {
		// console.log('-- form_set');
		// console.log(s);

		// On travaille sur une copie de l'objet passé en paramètre, pour ne pas
		// faire d'effets de bord
		var s = Object.assign({}, selection);

		/* En mode "dimensions forcées", on permet des sélections plus
		   grandes que les dimensions voulues. Il faut alors tout
		   remettre à la bonne échelle. */
		if (dimensions_forcees) {
			s = etendre_selection(s, { x: largeur_forcee, y: hauteur_forcee });
		}

		self.find('input[name=x1]').attr('value', s.x1);
		self.find('input[name=x2]').attr('value', s.x2);
		self.find('input[name=y1]').attr('value', s.y1);
		self.find('input[name=y2]').attr('value', s.y2);
		self.find('input[name=zoom]').attr('value', s.zoom);

		self.find('.dimensions').html((s.x2 - s.x1) + ' x ' + (s.y2 - s.y1));
	}

	function etendre_selection(s, dimensions) {

		var echelle = (s.x2 - s.x1) / dimensions.x;

		return {
			x1: round(s.x1 / echelle),
			x2: round(s.x1 / echelle) + dimensions.x,
			y1: round(s.y1 / echelle),
			y2: round(s.y1 / echelle) + dimensions.y,
			zoom: s.zoom / echelle
		};
	}

	tests.push(make_test_equals(
		'etendre la selection ne fait rien quand on est déjà aux bonnes dimensions',
		{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 1},
		function () {
			return etendre_selection(
				{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 1},
				{ x: 100, y: 50 }
			);
		}
	));
	tests.push(make_test_equals(
		'etendre la selection ne fait rien quand on est déjà aux bonnes dimensions',
		{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 2},
		function () {
			return etendre_selection(
				{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 2},
				{ x: 100, y: 50 }
			);
		}
	));
	tests.push(make_test_equals(
		'etendre la selection agrandit quand il le faut',
		{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 0.5},
		function () {
			return etendre_selection(
				{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 0.25},
				{ x: 200, y: 100 }
			);
		}
	));
	tests.push(make_test_equals(
		'etendre la selection agrandit quand il le faut',
		{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 1},
		function () {
			return etendre_selection(
				{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 0.5},
				{ x: 200, y: 100 }
			);
		}
	));

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
						minWidth: round(largeur_forcee * selection.zoom),
						minHeight: round(hauteur_forcee * selection.zoom)
					});

					imgAreaSelector.update();
				}

				slider_init(selection, function () {
					/* Après avoir un initialisé le slider, la mise en page ne
					   bougera plus. On peut alors initialiser la sélection */
					derniere_selection_widget = form_get();
					img_set(form_get());
				});
				selecteur_format_init();
				// init_bouton_reinit();
			},
			onSelectChange: function (img, selection) {

				/* Le widget nous donne un objet avec des infos inutiles, on
				 * nettoie un peu… */
				delete selection.width;
				delete selection.height;

				if (isNaN(selection.x1)) { delete selection.x1; }
				if (isNaN(selection.x2)) { delete selection.x2; }
				if (isNaN(selection.y1)) { delete selection.y1; }
				if (isNaN(selection.y2)) { delete selection.y2; }

				/* Quand le wigdet ne donne rien d'utile, on prends les valeurs
				 * enregistrées dans le formulaire. */
				selection = $.extend(form_get(), selection);

				derniere_selection_widget = selection;
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
	function slider_init (selection, init_callback) {

		slider = self.find('#zoom-slider').slider({
			max: 1,
			min: 0.01,
			value: selection.zoom,
			step: 0.01,
			create: function () {
				return init_callback();
			},
			slide: function (event, ui) {

				var selection = imgAreaSelector.getSelection();

				/* Le widget nous donne un objet avec des infos inutiles, on
				 * nettoie un peu… */
				delete selection.width;
				delete selection.height;

				selection.zoom = parseFloat(ui.value);
				selection = zoom_selection(selection);
				if (dimensions_forcees) {
					selection = contraindre_selection(selection);
				}

				form_set(selection);
				img_set(selection);
			}
		});
	}

	/**
	 * Initialisation du sélecteur de format
	 */
	function selecteur_format_init () {

		selecteur_format.change(function (e) {

			var selection = form_get(),
				format = e.target.value;

			if (format) {
				dimensions_forcees = true;

				format = format.split(':');
				largeur_forcee = parseInt(format[0], 10);
				hauteur_forcee = parseInt(format[1], 10);

				imgAreaSelector.setOptions({
					aspectRatio: largeur_forcee + ':' + hauteur_forcee,
					minWidth: round(largeur_forcee * min(1, selection.zoom)),
					minHeight: round(hauteur_forcee * min(1, selection.zoom))
				});

				slider.slider('option', 'min', zoom_min_get());
				slider.slider('option', 'value', max(zoom_min_get(), selection.zoom));

				selection = contraindre_selection(selection);

				img_set(selection);
				selector_set(selection);
				form_set(selection);

			} else {
				dimensions_forcees = false;
				largeur_forcee = NaN;
				hauteur_forcee = NaN;

				slider.slider('option', 'min', 0.01);

				imgAreaSelector.setOptions({
					aspectRatio: '',
					minWidth: 1,
					minHeight: 1
				});
			}
		})
			.trigger('change');
	}

	/**
	 * Zoomer l'image et met à jour la sélection
	 */
	function img_set (selection) {

		conteneur
			.css('width', selection.zoom * largeur_image + 'px')
			.css('height', selection.zoom * hauteur_image + 'px')
			.css('margin-left', '-' + (max((selection.zoom*largeur_image - 780),0) / 2) + 'px' );

		img
			.css('width', min(1, selection.zoom) * largeur_image + 'px')
			.css('padding-top', (max(1, selection.zoom) - 1) / 2 * hauteur_image);

		selector_set(selection);
	}

	/**
	 * Mettre à jour une sélection après un zoom
	 *
	 * On recalcule les coordonnées de la sélection en se basant sur la valeur
	 * du zoom, qu'on applique à la dernière sélection saisie via le widget.
	 * Cela permet d'éviter toute perte de précision lors de zooms et de
	 * dé-zooms.
	 *
	 * Retourne la sélection avec des coordonnées mises à jour.
	 */
	function zoom_selection (selection) {

		var last = derniere_selection_widget,
			zoom = selection.zoom,
			s = { zoom: zoom },
			// La taille des marges autour de l'image
			marge_last = {
				x: (max(1, last.zoom) - 1) / 2 * largeur_image,
				y: (max(1, last.zoom) - 1) / 2 * hauteur_image,
			},
			marge = {
				x: (max(1, zoom) - 1) / 2 * largeur_image,
				y: (max(1, zoom) - 1) / 2 * hauteur_image,
			},
			// L'écart entre la sélection est le bord de l'image
			ecart_last = {
				x1: marge_last.x - last.x1,
				y1: marge_last.y - last.y1,
				x2: last.x2 - (largeur_image + marge_last.x),
				y2: last.y2 - (hauteur_image + marge_last.y)
			};

		/* Si le zoom est < 1, on zoome la sélection pour qu'elle reste sur la
		 * même portion de l'image. */
		if (zoom <= 1) {
			/* Si la dernière sélection à été faite avec un zoom > 0, il faut
			 * lui déduire les marges. */
			if (last.zoom > 1) {
				last = {
					x1: max(0, last.x1 - marge_last.x),
					x2: max(0, last.x2 - marge_last.x),
					y1: max(0, last.y1 - marge_last.y),
					y2: max(0, last.y2 - marge_last.y),
					zoom: 1
				};
			}

			s = $.extend(s, {
				x1: round(max(
					0,
					last.x1 / last.zoom * zoom
				)),
				y1: round(max(
					0,
					last.y1 / last.zoom * zoom
				)),
				x2: round(min(
					largeur_image * zoom,
					last.x2 / last.zoom * zoom
				)),
				y2: round(min(
					hauteur_image * zoom,
					last.y2 / last.zoom * zoom
				)),
			});
		}
		/* Si le zoom est > 1, l'image n'est pas agrandie, alors on garde la
		 * taille de la sélection. Par contre on doit la décaler pour qu'elle
		 * reste sur la même portion de l'image. */
		else {
			s = $.extend(s, {
				x1: round(max(
					0,
					marge.x - (ecart_last.x1 / min(1, last.zoom))
				)),
				y1: round(max(
					0,
					marge.y - (ecart_last.y1 / min(1, last.zoom))
				)),
				x2: round(min(
					largeur_image * zoom,
					marge.x + ((largeur_image + ecart_last.x2) / min(1, last.zoom))
				)),
				y2: round(min(
					hauteur_image * zoom,
					marge.y + ((hauteur_image + ecart_last.y2) / min(1, last.zoom))
				))
			});
		}

		return s;
	}

	/**
	 * Retourne la sélection aux dimensions imposées dont le centre est
	 * identique à la sélection passée en paramètre.
	 */
	function contraindre_selection (s) {

		var zoom_min = zoom_min_get();

		// Si c'est nécessaire, on commence par zoomer.
		if (s.zoom < zoom_min) {
			s.zoom = zoom_min;
			s = zoom_selection(s);
		}

		// Une fois qu'on est certain d'avoir la place, on calcule une nouvelle
		// sélection.
		var taille_canevas  = {
				x: round(largeur_image * s.zoom),
				y: round(hauteur_image * s.zoom)
			},
			centre = {
				x: (s.x2 + s.x1) / 2,
				y: (s.y2 + s.y1) / 2
			},
			echelle_x = (s.x2 - s.x1) * min(1, s.zoom) / largeur_forcee,
			echelle_y = (s.y2 - s.y1) * min(1, s.zoom) / hauteur_forcee,
			echelle = max(1, min(echelle_x, echelle_y)),
			largeur_selection = largeur_forcee * echelle,
			hauteur_selection = hauteur_forcee * echelle;

		s = $.extend(s, {
			x1: round(max(0, centre.x - (largeur_selection / 2))),
			y1: round(max(0, centre.y - (hauteur_selection / 2))),
		});

		s.x2 = round(s.x1 + largeur_selection);
		s.y2 = round(s.y1 + hauteur_selection);

		if (s.x2 > taille_canevas.x) {
			s.x1 = s.x1 - (s.x2 - taille_canevas.x);
			s.x2 = taille_canevas.x;
		}
		if (s.y2 > taille_canevas.y) {
			s.y1 = s.y1 - (s.y2 - taille_canevas.y);
			s.y2 = taille_canevas.y;
		}

		return s;
	}

	function zoom_min_get () {

		return Math.max(
			largeur_forcee / largeur_image,
			hauteur_forcee / hauteur_image
		);
	}

	/**
	 * Fonctions utiles pour les tests.
	 */
	self.runTests = function (test_index) {

		if (test_index) {
			tests[test_index].call(test_index);
		} else {
			tests.forEach(function (test_func, index) {
				test_func.call(index);
			});
		}
	};

	function make_test (msg, test_func) {
		return function () {
			if (test_func.call()) {
				console.log(this + ' OK: ' + msg);
			} else {
				console.error(this + ' ' + msg);
			}
		};
	}

	function make_test_equals(msg, left, right) {
		return function () {

			// On utilise des copies pour éviter les effets de bords
			var left_val, right_val;

			if (left instanceof Function) {
				left_val = left.call();
			} else {
				left_val = left;
			}
			if (right instanceof Function) {
				right_val = right.call();
			} else {
				right_val = right;
			}

			if ((left_val instanceof Object && right_val instanceof Object && isEquivalent(left_val, right_val))
				|| (left_val === right_val)) {

				console.log(this + ' OK: ' + msg);
			} else {
				console.error(this + ' ' + msg);
				console.log(left_val);
				console.log(right_val);
			}
		};
	}

	return self;
};

/**
 * Fonctions utilitaires
 */

/* http://adripofjavascript.com/blog/drips/object-equality-in-javascript.html */
function isEquivalent(a, b) {
	// Create arrays of property names
	var aProps = Object.getOwnPropertyNames(a);
	var bProps = Object.getOwnPropertyNames(b);

	// If number of properties is different,
	// objects are not equivalent
	if (aProps.length != bProps.length) {
		return false;
	}

	for (var i = 0; i < aProps.length; i++) {
		var propName = aProps[i];

		// If values of same property are not equal,
		// objects are not equivalent
		if (a[propName] !== b[propName]) {
			return false;
		}
	}

	// If we made it this far, objects
	// are considered equivalent
	return true;
}
