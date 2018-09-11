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
		dimensions_image = {
			x: parseInt(img.attr('width'), 10),
			y: parseInt(img.attr('height'), 10)
		},
		// options
		dimensions_forcees = options.forcer_dimensions instanceof Object,
		contrainte_selection = {
			x: dimensions_forcees ? parseInt(options.forcer_dimensions.largeur, 10) : NaN,
			y: dimensions_forcees ? parseInt(options.forcer_dimensions.hauteur, 10) : NaN
		},
		// On garde en mémoire la sélection telle qu'elle a été saisie via le
		// widget de sélection, en ignorant l'effet d'éventuels zooms à venir.
		// Ça permet d'éviter une perte de précision lorsqu'on zoome et dézoome
		// avec le slider.
		derniere_selection_widget,
		// widgets
		imgAreaSelector,
		slider,
		selecteur_format = self.find('select[name=format]'),
		bouton_reset = self.find('.bouton_reset'),
		// Raccourcis
		round = Math.round,
		max = Math.max,
		min = Math.min,
		// La suite de tests
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

		// On travaille sur une copie de l'objet passé en paramètre, pour ne pas
		// faire d'effets de bord
		var s = Object.assign({}, selection);

		/* En mode "dimensions forcées", on permet des sélections plus
		   grandes que les dimensions voulues. Il faut alors tout
		   remettre à la bonne échelle. */
		if (dimensions_forcees) {
			s = etendre_selection(s, contrainte_selection);
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

		var s_init = form_get();

		imgAreaSelector = conteneur.imgAreaSelect({
			instance: true,
			handles: true,
			show: true,
			x1: s_init.x1,
			x2: s_init.x2,
			y1: s_init.y1,
			y2: s_init.y2,
			/* On fait toutes les initialisations des autres widgets dans ce
			 * callback, pour être certain de pouvoir utiliser
			 * imgAreaSelector.setOptions sans faire planter le widget. */
			onInit: function () {

				if (dimensions_forcees) {

					imgAreaSelector.setOptions({
						aspectRatio: contrainte_selection.x + ':' + contrainte_selection.y,
						minWidth: round(contrainte_selection.x * s_init.zoom),
						minHeight: round(contrainte_selection.y * s_init.zoom)
					});

					imgAreaSelector.update();
				}

				slider_init(s_init, function () {
					/* Après avoir un initialisé le slider, la mise en page ne
					   bougera plus. On peut alors initialiser la sélection */
					var s = GUI_get_selection();
					img_set(s);
					selector_set(s);
					derniere_selection_widget = s;
				});

				selecteur_format_init();
				bouton_reset_init();
			},
			onSelectChange: function (img, s) {

				/* Le widget nous donne un objet avec des infos inutiles, on
				 * nettoie un peu… */
				delete s.width;
				delete s.height;

				if (isNaN(s.x1)) { delete s.x1; }
				if (isNaN(s.x2)) { delete s.x2; }
				if (isNaN(s.y1)) { delete s.y1; }
				if (isNaN(s.y2)) { delete s.y2; }

				/* Quand le wigdet ne donne rien d'utile, on prends les valeurs
				 * enregistrées dans le formulaire. */
				s = $.extend(form_get(), s);

				/* S'il est déjà disponible, on utilise plutôt la valeur de zoom
				 * du slider, qui correspond à ce qu'on voit vraiment à
				 * l'écran. */
				if (slider && slider.slider instanceof Function) {
					s.zoom = slider.slider('option', 'value');
				}

				derniere_selection_widget = s;
				form_set(s);
			}
		});
	}

	/**
	 * Retourne la sélection courante
	 */
	function GUI_get_selection () {

		var s = imgAreaSelector.getSelection();

		delete s.width;
		delete s.height;

		if (isNaN(s.x1)) { delete s.x1; }
		if (isNaN(s.x2)) { delete s.x2; }
		if (isNaN(s.y1)) { delete s.y1; }
		if (isNaN(s.y2)) { delete s.y2; }

		s = $.extend(form_get(), s);

		if (slider && slider.slider instanceof Function) {
			s.zoom = slider.slider('option', 'value');
		}

		return s;
	}

	/**
	 * Mettre à jour le widget de sélection
	 */
	function selector_set (s) {

		imgAreaSelector.setSelection(s.x1, s.y1, s.x2, s.y2);
		imgAreaSelector.update();
	}

	/**
	 * Initialisation du slider
	 */
	function slider_init (selection, init_callback) {

		slider = self.find('#zoom-slider').slider({
			max: 2,
			min: 0.01,
			value: selection.zoom,
			step: 0.01,
			create: function () {
				return init_callback();
			},
			slide: function (event, ui) {

				var s = imgAreaSelector.getSelection();

				/* Le widget nous donne un objet avec des infos inutiles, on
				 * nettoie un peu… */
				delete s.width;
				delete s.height;

				s.zoom = parseFloat(ui.value);

				s = zoom_selection(s, derniere_selection_widget, dimensions_image);

				if (dimensions_forcees) {
					s = contraindre_selection(s, contrainte_selection, derniere_selection_widget, dimensions_image);

					imgAreaSelector.setOptions({
						aspectRatio: contrainte_selection.x + ':' + contrainte_selection.y,
						minWidth: round(contrainte_selection.x * min(1, s.zoom)),
						minHeight: round(contrainte_selection.y * min(1, s.zoom))
					});
				}

				form_set(s);
				img_set(s);
				selector_set(s);
			}
		});
	}

	/**
	 * Initialisation du sélecteur de format
	 */
	function selecteur_format_init () {

		selecteur_format.change(function (e) {

			var s = GUI_get_selection(),
				format = e.target.value;

			if (format) {
				dimensions_forcees = true;

				format = format.split(':');
				contrainte_selection.x = parseInt(format[0], 10);
				contrainte_selection.y = parseInt(format[1], 10);

				imgAreaSelector.setOptions({
					aspectRatio: contrainte_selection.x + ':' + contrainte_selection.y,
					minWidth: round(contrainte_selection.x * min(1, s.zoom)),
					minHeight: round(contrainte_selection.y * min(1, s.zoom))
				});

				s = contraindre_selection(s, contrainte_selection, derniere_selection_widget, dimensions_image);

				form_set(s);
				img_set(s);
				selector_set(s);
				derniere_selection_widget = s;

			} else {
				dimensions_forcees = false;
				contrainte_selection.x = NaN;
				contrainte_selection.y = NaN;

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
	 * Initialisation du bouton de réinitialisation
	 */
	function bouton_reset_init () {

		bouton_reset.click(function (e) {

			var s = {
				x1: 0,
				x2: dimensions_image.x,
				y1: 0,
				y2: dimensions_image.y,
				zoom: 1
			};

			slider.slider('option', 'value', s.zoom);
			selecteur_format.val('').trigger('change');

			form_set(s);
			img_set(s);
			selector_set(s);
			derniere_selection_widget = s;

			e.preventDefault();
			return false;
		});
	}

	/**
	 * Zoome l'image et met à jour la sélection
	 */
	function img_set (s) {

		conteneur
			.css('width', s.zoom * dimensions_image.x + 'px')
			.css('height', s.zoom * dimensions_image.y + 'px')
			.css('margin-left', '-' + (max((s.zoom*dimensions_image.x - 780),0) / 2) + 'px' );

		img
			.css('width', min(1, s.zoom) * dimensions_image.x + 'px')
			.css('padding-top', (max(1, s.zoom) - 1) / 2 * dimensions_image.y);
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
	function zoom_selection (selection, last_selection, image) {

		var last = Object.assign({}, last_selection),
			zoom = selection.zoom,
			s = { zoom: zoom },
			// La taille des marges autour de l'image
			marge_last = {
				x: (max(1, last.zoom) - 1) / 2 * image.x,
				y: (max(1, last.zoom) - 1) / 2 * image.y,
			},
			marge = {
				x: (max(1, zoom) - 1) / 2 * image.x,
				y: (max(1, zoom) - 1) / 2 * image.y,
			},
			// L'écart entre la sélection est le bord de l'image
			ecart_last = {
				x1: marge_last.x - last.x1,
				y1: marge_last.y - last.y1,
				x2: last.x2 - (image.x + marge_last.x),
				y2: last.y2 - (image.y + marge_last.y)
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
					image.x * zoom,
					last.x2 / last.zoom * zoom
				)),
				y2: round(min(
					image.y * zoom,
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
					image.x * zoom,
					marge.x + ((image.x + ecart_last.x2) / min(1, last.zoom))
				)),
				y2: round(min(
					image.y * zoom,
					marge.y + ((image.y + ecart_last.y2) / min(1, last.zoom))
				))
			});
		}

		return s;
	}

	tests.push(make_test_equals(
		'zoomer la sélection sans zoom ne fait rien',
		{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 1},
		function () {
			return zoom_selection(
				{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 1},
				{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 1},
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'zoomer la sélection en partant sans zoom fonctionne',
		{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 0.5},
		function () {
			return zoom_selection(
				{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 0.5},
				{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 1},
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'zoomer la sélection en partant d\'un zoom < 1 fonctionne',
		{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 0.25},
		function () {
			return zoom_selection(
				{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 0.25},
				{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 0.5},
				{ x: 1000, y: 600 }
			);
		}
	));
	tests.push(make_test_equals(
		'zoomer la sélection en partant d\'un zoom < 1 fonctionne',
		{ x1: 650, x2: 1000, y1: 150, y2: 350, zoom: 2},
		function () {
			return zoom_selection(
				{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 2},
				{ x1: 200, x2: 400, y1: 0, y2: 100, zoom: 0.5},
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'zoomer la sélection en partant d\'un zoom > 1 fonctionne',
		{ x1: 25, x2: 250, y1: 0, y2: 100, zoom: 0.5},
		function () {
			return zoom_selection(
				{ x1: 300, x2: 1000, y1: 150, y2: 350, zoom: 0.5},
				{ x1: 300, x2: 1000, y1: 150, y2: 350, zoom: 2},
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'zoomer la sélection en partant d\'un zoom > 1 fonctionne',
		{ x1: 550, x2: 1250, y1: 300, y2: 500, zoom: 3},
		function () {
			return zoom_selection(
				{ x1: 300, x2: 1000, y1: 150, y2: 350, zoom: 3},
				{ x1: 300, x2: 1000, y1: 150, y2: 350, zoom: 2},
				{ x: 500, y: 300 }
			);
		}
	));

	/**
	 * Retourne la sélection aux dimensions imposées dont le centre est
	 * identique à la sélection passée en paramètre.
	 */
	function contraindre_selection (selection, contrainte, last_selection, image) {

		var s = Object.assign({}, selection),
			zoom_min = max(
				contrainte.x / image.x,
				contrainte.y / image.y
			);

		// Si l'image est trop petite, on commence par zoomer.
		if ((zoom_min > 1) && (s.zoom < zoom_min)) {
			s.zoom = zoom_min;
			s = zoom_selection(s, last_selection, image);
		}

		// Maintenant qu'on est certain d'avoir la place, on calcule une
		// nouvelle sélection.
		var taille_canevas  = {
				x: round(image.x * s.zoom),
				y: round(image.y * s.zoom)
			},
			centre = {
				x: (s.x2 + s.x1) / 2,
				y: (s.y2 + s.y1) / 2
			},
			zoom_reel = min(1, s.zoom),
			// echelle max avant que la sélection ne dépasse du canevas
			echelle_max = min(
				taille_canevas.x / zoom_reel / contrainte.x,
				taille_canevas.y / zoom_reel / contrainte.y
			),
			echelle_x = (s.x2 - s.x1) / zoom_reel / contrainte.x,
			echelle_y = (s.y2 - s.y1) / zoom_reel / contrainte.y,
			echelle = min(
				max(zoom_reel, (echelle_x + echelle_y) / 2),
				echelle_max
			),
			largeur_selection = contrainte.x * zoom_reel * echelle,
			hauteur_selection = contrainte.y * zoom_reel * echelle;

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

	tests.push(make_test_equals(
		'contraindre une sélection ok ne la modifie pas (zoom = 1)',
		{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 1 },
		function () {
			return contraindre_selection(
				{ x1: 100, x2: 200, y1: 0, y2: 50, zoom: 1 },
				{ x: 100, y: 50 },
				null,
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection ok ne la modifie pas (zoom = 1, zoom_cible petit)',
		{ x1: 0, x2: 150, y1: 25, y2: 75, zoom: 0.25 },
		function () {
			return contraindre_selection(
				{ x1: 0, x2: 150, y1: 25, y2: 75, zoom: 0.25 },
				{ x: 300, y: 100 },
				{ x1: 0, x2: 600, y1: 100, y2: 300, zoom: 1 },
				{ x: 600, y: 400 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection ok ne la modifie pas (zoom < 1)',
		{ x1: 50, x2: 100, y1: 0, y2: 25, zoom: 0.5 },
		function () {
			return contraindre_selection(
				{ x1: 50, x2: 100, y1: 0, y2: 25, zoom: 0.5 },
				{ x: 100, y: 50 },
				null,
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection ok ne la modifie pas (zoom > 1)',
		{ x1: 50, x2: 150, y1: 0, y2: 50, zoom: 2 },
		function () {
			return contraindre_selection(
				{ x1: 50, x2: 150, y1: 0, y2: 50, zoom: 2 },
				{ x: 100, y: 50 },
				null,
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection trop grande fonctionne (zoom = 1)',
		{ x1: 0, x2: 500, y1: 50, y2: 300, zoom: 1 },
		function () {
			return contraindre_selection(
				{ x1: 0, x2: 500, y1: 0, y2: 400, zoom: 1 },
				{ x: 100, y: 50 },
				null,
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection trop grande fonctionne (zoom < 1)',
		{ x1: 0, x2: 500, y1: 150, y2: 250, zoom: 0.5 },
		function () {
			return contraindre_selection(
				{ x1: 0, x2: 500, y1: 0, y2: 400, zoom: 0.5 },
				{ x: 100, y: 20 },
				null,
				{ x: 1000, y: 800 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection trop grande fonctionne (zoom > 1)',
		{ x1: 0, x2: 1000, y1: 200, y2: 400, zoom: 2 },
		function () {
			return contraindre_selection(
				{ x1: 0, x2: 1000, y1: 0, y2: 600, zoom: 2 },
				{ x: 100, y: 20 },
				null,
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection trop petite fonctionne (zoom = 1)',
		{ x1: 200, x2: 300, y1: 125, y2: 175, zoom: 1 },
		function () {
			return contraindre_selection(
				{ x1: 240, x2: 260, y1: 145, y2: 155, zoom: 1 },
				{ x: 100, y: 50 },
				null,
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection trop petite fonctionne (zoom < 1)',
		{ x1: 175, x2: 225, y1: 145, y2: 155, zoom: 0.5 },
		function () {
			return contraindre_selection(
				{ x1: 190, x2: 210, y1: 145, y2: 155, zoom: 0.5 },
				{ x: 200, y: 40 },
				null,
				{ x: 800, y: 600 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection trop petite fonctionne (zoom > 1)',
		{ x1: 450, x2: 550, y1: 275, y2: 325, zoom: 2 },
		function () {
			return contraindre_selection(
				{ x1: 490, x2: 510, y1: 295, y2: 305, zoom: 2 },
				{ x: 100, y: 50 },
				null,
				{ x: 500, y: 300 }
			);
		}
	));
	tests.push(make_test_equals(
		'contraindre une sélection fonctionne quand l\'image est trop petite',
		{ x1: 0, x2: 1000, y1: 250, y2: 350, zoom: 2 },
		function () {
			return contraindre_selection(
				{ x1: 0, x2: 500, y1: 0, y2: 300, zoom: 1 },
				{ x: 1000, y: 100 },
				{ x1: 0, x2: 500, y1: 0, y2: 300, zoom: 1 },
				{ x: 500, y: 300 }
			);
		}
	));

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

	/* eslint-disable no-console */
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
	/* eslint-enable no-console */

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

	return self;
};

// Local Variables:
// indent-tabs-mode: t
// tab-width: 4
// End:
