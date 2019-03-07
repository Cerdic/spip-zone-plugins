
/* Gestion du tri des listes de liens et de leur enregistrement en JS Drag n drop. */
function spip_ordonner_liens() {
	if (!$.fn.sortable) {
		return false;
	}
	var $listes = $('.liste-objets-lies').has('tbody button[name^="ordonner_lien"]');
	if (!$listes.length) {
		return false;
	}
	var $tables = $listes.find('.liste');
	if (!$tables.length) {
		return false;
	}
	$tables.find('tbody').each(function(){
		// détruire / recréer le sortable à chaque appel ajax
		if ($(this).has('.ui-sortable').length) {
			$(this).sortable('destroy');
		}
		// pas de tri possible s'il n'y a qu'un seul élément.
		var $trs = $(this).find('tr');
		if ($trs.length < 2) {
			return true; // continue
		}
		// ajouter l’icone de grab, dans le premier <td> de chaque ligne
		// enlever le bouton par défaut pour ordonner
		$trs.find('> :first-child').each(function(){
			$(this).find('button[name^="ordonner_lien"]').hide();
			if ($(this).has('.deplacer').length === 0) {
				$(this).append($('<span class="deplacer"></span>').attr('title', trad_deplacer_element));
			}
		});
		// enlever le lien "Ordonner les ..."… on s’en occupe en JS
		$(this).parent('.liste-objets-lies').find('.action .button[name^="ordonner_lien"]').hide();

		$(this).sortable({
			axis: "y",
			handle: "",
			placeholder: "ui-state-highlight deplacer-lien-placeholder",
			start: function(event, ui) {
				ui.item.addClass('deplacer-en-mouvement');
			},
			stop: function(event, ui) {
				ui.item.removeClass('deplacer-en-mouvement');
			},
			update: function (event, ui) {
				var items = $(this);
				var item = ui.item;
				function get_lien(item) {
					var lien = item.data('lien').split("/");
					return {
						objet: lien[0],
						id_objet: lien[1]
					}
				}
				function get_rang(item) {
					var rang = item.data('rang');
					return rang !== undefined ? rang : 0;
				}
				// l’objet source (auteur/3) est indiqué dans l'attribut data-lien de chaque ligne (tr)
				var source = get_lien(item);
				// l'objet lié (article/5) est indiqué dans l'attribut data-lien sur la liste
				var lie = get_lien(items.parents(".liste-objets-lies"));
				var rang = 1;
				if (item.prev().length) {
					rang = 1 + get_rang(item.prev());
				}
				var cle = [source.objet, source.id_objet, lie.objet, lie.id_objet].join('-');
				var button = $('<button type="submit" name="ordonner_lien[' + cle + ']" value="' + rang + '"></button>');
				items.append(button);
				button.click();
			}
		});
	});
}

/* Initialisation et relance en cas de chargement ajax */
if (window.jQuery) {
	jQuery(function($){
		if (!$.js_spip_ordonner_liens_charge) {
			$.js_spip_ordonner_liens_charge = true;
			spip_ordonner_liens();
			onAjaxLoad(spip_ordonner_liens);
		}
	});
}