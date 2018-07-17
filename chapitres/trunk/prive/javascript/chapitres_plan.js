/**
 * Chapitres : gestion du plan
 *
 * Mettre en jstree
 * Mettre en sticky
 */
jQuery(function($){

	var plan = '#chapitres-plan';
	var plan_conteneur = '.chapitres-plan';

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

		// Déplier le chapitre ou la branche en cours
		if ($(this).hasClass('editables')) {
			$(this).jstree('open_all', $(plan + ' li.expose'));
		} else {
			$(this).jstree('open_node', $(plan + ' li.expose'));
		}
		// Sélectionner le chapitre en cours (censé ouvrir les parents mais ne fonctionne pas)
		$(this).jstree('select_node', $(plan + ' li.expose'), true, false);

		// Liens cliquables
		liens_cliquables();

		// Boutons de pliage
		$(plan_conteneur + ' .pliage a').click(function(e) {
			if ($(this).data('pliage') == 'deplier') {
				$(plan).jstree('open_all');
			} else {
				$(plan).jstree('close_all');
			}
			return false;
		});

		// Sticky
		$(plan_conteneur).stick_in_parent({
			parent: '#navigation'
		});
		// Recalculer le sticky avec le pliage
		$(plan).on('open_all.jstree', function(){
			$(plan_conteneur).trigger('sticky_kit:recalc');
		});
		$(plan).on('close_all.jstree', function(){
			$(plan_conteneur).trigger('sticky_kit:recalc');
		});

	});

	// Liens cliquables lors des changements
	$(plan).bind('open_node.jstree open_all.jstree view_all', function(e, data) {
		liens_cliquables();
	});
	function liens_cliquables(){
		$(plan + ' li a').click(function(e) {
			var href = $(this).attr('href');
			if (href !== '#') {
				document.location.href = href;
			}
		});
	}

});