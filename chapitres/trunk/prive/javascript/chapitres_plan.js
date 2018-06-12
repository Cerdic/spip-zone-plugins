/**
 * Chapitres : gestion du plan
 *
 * Mettre ne jstree
 * Mettre en sticky
 */
jQuery(function($){

	var plan = '#chapitres-plan';
	var plan_parent = '.chapitres-plan';

	// Initialiser jstree
	$(plan).jstree({
		core: {
			animation: false,
			themes: {
				icons: false
			}
		}
	});

	// Des choses après le chargement de jstree
	$(plan).bind('ready.jstree', function(e, data) {

		// Déplier ceux à la racine
		$(this).jstree('open_node', $(plan + ' > ul > li'));
		// Déplier le chapitre en cours (toute la branche si vue complète)
		if ($(this).hasClass('editables')) {
			$(this).jstree('open_all', $(plan + ' li.expose'));
		} else {
			$(this).jstree('open_node', $(plan + ' li.expose'));
		}
		// Sélectionner le chapitre en cours
		$(this).jstree('select_node', $(plan + ' li.expose'));
		// Liens cliquables
		$(plan + ' li a').click(function(e) {
			var href = $(this).attr('href');
			if (href !== '#') {
				document.location.href = href;
			}
		});

		// Boutons de pliage
		$(plan_parent + ' .pliage a').click(function(e) {
			if ($(this).data('pliage') == 'deplier') {
				$(plan).jstree('open_all');
			} else {
				$(plan).jstree('close_all');
			}
			return false;
		});

		// Sticky
		$(plan_parent).stick_in_parent({
			parent: '#navigation'
		});
		// Recalculer le sticky avec le pliage
		$(plan).on('open_all.jstree', function(){
			$(plan_parent).trigger('sticky_kit:recalc');
		});
		$(plan).on('close_all.jstree', function(){
			$(plan_parent).trigger('sticky_kit:recalc');
		});

	});

});