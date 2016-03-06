/* jshint strict: global, undef: true, unused: true, curly: true,
   eqeqeq: true, freeze: true, funcscope: true, futurehostile: true,
   nonbsp: true */
/* globals QUnit */
"use strict";

QUnit.module('Fonction forcer_dimension_selection');

// On recopie la fonction ici pour ne pas se prendre le chou… Ça n'est
// pas top, mais c'est compliqué de faire autrement.
// Du coup il faut initialiser les variables qui sont sensées être
// présentes dans la pile au moment de l'appel de la fonction
var largeur_image = 1000,
	hauteur_image = 2000,
	forcer_largeur = 200,
	forcer_hauteur = 300;

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

	selection = {
		x1: x1,
		x2: x2,
		y1: y1,
		y2: y2
	};

	return selection;
}

QUnit.test('On ne touche pas à ce qui va déjà bien', function (assert) {

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 0, x2: 200, y1:0, y2:300 },
			1
		),
		{ x1: 0, x2: 200, y1:0, y2:300 },
		'pas de zoom, sélection à la bonne taille'
	);

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 50, x2: 250, y1:50, y2:350 },
			1
		),
		{ x1: 50, x2: 250, y1:50, y2:350 },
		'pas de zoom, sélection à la bonne taille, décalé'
	);

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 0, x2: 100, y1:0, y2:150 },
			0.5
		),
		{ x1: 0, x2: 100, y1:0, y2:150 },
		'zoom, sélection à l\'échelle'
	);

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 50, x2: 150, y1:50, y2:200 },
			0.5
		),
		{ x1: 50, x2: 150, y1:50, y2:200 },
		'zoom, sélection à l\'échelle, décalé'
	);

});


QUnit.test('On adapte les dimensions quand c\'est nécessaire.', function (assert) {

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 0, x2: 50, y1:0, y2:200 },
			1
		),
		{ x1: 0, x2: 200, y1:0, y2:300 },
		'pas de zoom, sélection trop petite'
	);

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 0, x2: 50, y1:0, y2:200 },
			1
		),
		{ x1: 0, x2: 200, y1:0, y2:300 },
		'pas de zoom, sélection trop petite'
	);

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 0, x2: 300, y1:0, y2:750 },
			1
		),
		{ x1: 0, x2: 400, y1:75, y2:675 },
		'pas de zoom, sélection trop grande'
	);

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 0, x2: 300, y1:0, y2:750 },
			0.5
		),
		{ x1: 0, x2: 400, y1:75, y2:675 },
		'zoom, sélection trop grande'
	);

});

QUnit.test('On prend en compte les bords de l\'image.', function (assert) {

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 950, x2: 1000, y1: 1900, y2: 2000 },
			1
		),
		{ x1: 800, x2: 1000, y1: 1700, y2: 2000 },
		'image trop petite collée en bas à droite'
	);

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 0, x2: 1000, y1: 0, y2: 2000 },
			1
		),
		{ x1: 0, x2: 1000, y1: 250, y2: 1750 },
		'sélection prenant toute l\'image'
	);

	assert.deepEqual(
		forcer_dimensions_selection(
			{ x1: 0, x2: 500, y1: 0, y2: 1000 },
			0.5
		),
		{ x1: 0, x2: 500, y1: 125, y2: 875 },
		'zoom, sélection prenant toute l\'image'
	);

});
