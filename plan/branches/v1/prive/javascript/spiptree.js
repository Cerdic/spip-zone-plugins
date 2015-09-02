;(function($){

$.fn.spiptree = function(options) {

	var $mytree = $(this);
	var $mytree_source = $mytree.clone();
	// $mytree.after($mytree_source);

	options.plugins = [ "types", "search", "state" ];
	if (options.drag) {
		options.plugins.push("dnd");
	}

	options.types = {
		"#" : {
			"valid_children" : ["default"]
		},
		"default" : {
			"icon" : options.default.icon,
			"valid_children" : [ "default" ]
		}
	}

	$.each(options.objets, function(nom, desc) {
		options.types.default.valid_children.push(desc.type);
		options.types.default.valid_children.push("box_" + desc.type);
		options.types[desc.type] = {
			"icon" : desc.icon,
			"max_children" : 0,
			"max_depth" : 0
		};
		options.types["box_" + desc.type] = {
			"icon" : desc.icon,
			"max_depth" : 1,
			"valid_children" : [ desc.type ]
		};
	});

	$mytree.jstree({
		"plugins" : options.plugins,
		"core" : {
			"animation" : 0,
			"check_callback" : true,
			"data" : function (node, cb) {
				// on est obligé de tout charger en ajax (même la racine)
				// donc on charge 1 fois la racine avec le html d'origine
				if (node.id === '#') {
					cb($mytree_source.html());
				}

				// et pour ce qu'on ne connait pas (classe css 'jstree-closed' sur un LI, et pas de UL à l'intérieur)
				// on fait un appel ajax pour obtenir la liste correspondant à l'objet souhaité, lorsque c'est demandé.
				else {
					var id_rubrique = node.parent.split('-')[1];
					var params = {
						"id_rubrique": id_rubrique,
						"objet": node.data.jstree.objet
					};
					if (options.statut) {
						params.statut = options.statut;
					}
					$.ajax({
						url: options.urls.plan,
						data: params,
						dataType: 'html',
						cache: false,
					}).done(function(data) {
						cb(data);
					});
				}
			}
		},
		"search" : {
			"show_only_matches" : true,
		},
		"types" : options.types
	});

	// un clic d'une feuille amène sur son lien
	// mais… éviter que le plugin 'state' clique automatiquement lorsqu'il restaure
	// la sélection précédente !
	$mytree.one("restore_state.jstree", function () {
		$(this).on("changed.jstree", function (e, data) {
			data.instance.save_state();
			var node = data.instance.get_node(data.node, true);
			if (node) {
				location.href = node.children('a').attr('href');
			}
		});
	});

	// lorsqu'on déplace un nœud
	$mytree.on("move_node.jstree", function(event, data) {
		// si les parents sont identiques : pas de changement,
		// on ne peut/veut pas gérer ici les positionnements

		// console.log(data);

		if (data.old_parent == data.parent) {
			// data.instance.refresh();
			return true;
		}

		// il existe 2 cas de boites :
		// - un item (rubrique, article, site) a été déplacé
		// - un conteneur (box_xx) a été déplacé (ie: tous les articles qu'il contient par exemple)
		//   dans ce cas on retrouve tous les identifiants déplacés
		var box = (data.node.type.substring(0, 4) == 'box_');
		var infos = data.node.id.split('-'); // articles-rubrique-30 (box) ou article-30 (item)

		if (box) {
			var ids = [];
			$.each(data.node.children, function(key, val) {
				ids.push( val.split('-')[1] );
			});
			var params = {
				objet: infos[0],
				id_objet: ids, 
				id_rubrique_source: infos[2],
				id_rubrique_destination: data.parent.split('-')[1]
			}
		} else if (infos[0] == 'rubrique') {
			// les rubriques n'ont pas de 'box_' et sont directement dans les sous rubriques
			var params = {
				objet: infos[0],
				id_objet: [ infos[1] ],
				id_rubrique_source: (data.old_parent == '#' ? 0 : data.old_parent.split('-')[1]),
				id_rubrique_destination: (data.parent == '#' ? 0 : data.parent.split('-')[1])
			}
		} else {
			// un item, sa destination est soit une box (de même type) soit une rubrique
			var dest = data.parent.split('-'); // articles-rubrique-30 (box) ou rubrique-30
			var params = {
				objet: infos[0],
				id_objet: [ infos[1] ],
				id_rubrique_source: data.old_parent.split('-')[2],
				id_rubrique_destination: (dest.length == 3 ? dest[2] : dest[1]),
			}
		}

		$.ajax({
			url: options.urls.deplacer,
			data: params,
			dataType: 'json',
			cache: false,
		}).done(function(response) {
			// console.log('done', response);
			ajaxReload('contenu');
		});

		return true;
	});


	// recherche automatique
	$mytree_search = $("#mytree_search");

	var to = false;
	$mytree_search.keyup(function () {
		if (to) { clearTimeout(to); }
		to = setTimeout(function () {
			var v = $mytree_search.val();
			$mytree.jstree(true).search(v);
		}, 250);
	});

};

})(jQuery);
