;(function ($) {
jQuery(document).ready(function(){

	/* Remet le title sur les chosen s'il en existait un sur le select */
	spip_chosen_title = function() {
		$('.chosen-container').each(function () {
			/* Si un title était sur le sélect d'origine, le remettre sur le html de Chosen */
			if (title = $(this).prev().attr('title')) {
				$(this).attr('title', title);
			}
		});
	}

	/* Homogénéise la taille de select chosen présents à l'intérieur d'un tableau.
	 *
	 * Lorsqu'on place des sélecteurs dans une colonne de tableau, la largeur
	 * de la colonne fluctue en fonction du contenu qu'elle possède et
	 * celle-ci s'agrandit au fur et à mesure que les Chosen se chargent.
	 * Chaque Chosen n'a du coup plus forcément la même taille que son voisin
	 * (sa taille étant définie - en pixels - avec la taille du conteneur)
	 *
	 * Du coup, on regarde le premier Chosen de chaque colonne d'un tableau
	 * on applique sa taille aux autres chosen de cette colonne.
	 */
	spip_chosen_table_width = function() {
		$('table').has('.chosen-container').each(function() {
			$taille = []; // la taille des premiers chosen pour chaque colonne
			$(this).find('.chosen-container').each(function () {
				index = $(this).parent('td').index();
				if (!$taille[index]) {
					$taille[index] = $(this).width() + 10; // un tout petit rien de plus en général
				}
				$(this).css('width', $taille[index]).find("> .chosen-drop").css('width', $taille[index] - 2);
			});
		});
		
	}

	/* Ajoute la propriete overflow:visible au li contenant le select chosen (pour contrer le css de SPIP) */
	spip_chosen_visible = function() {
		$('.chosen-container').parents('li,fieldset,form').each(function () {
			$(this).css("overflow", "visible");
		});
	}

	/* lance Chosen sur les .chosen */
	spip_chosen = function() {
		var selecteur = (typeof(selecteur_chosen) != 'undefined') ? selecteur_chosen+',' : '';
		var options = (typeof(options_chosen) == 'object') ? $.extend(options_chosen, ((typeof(langue_chosen) == 'object') ? langue_chosen : {})) : ((typeof(langue_chosen) == 'object') ? langue_chosen : {});
		$(selecteur +" select.chosen").chosen(options);
		spip_chosen_title();
		spip_chosen_visible();
		spip_chosen_table_width();
	}

	spip_chosen();
	onAjaxLoad(spip_chosen);
});
})(jQuery);
