;(function ($) {
jQuery(document).ready(function(){

	/* Remet la bonne taille aux select chosen présents sur la page */
	/* Remet le title sur le chosen s'il en existait un sur le select */
	spip_chosen_width_and_title = function() {
		$('.chzn-container').each(function (index) {
			/*
			 * Remettre la bonne taille des éléments qui change avec la
			 * présence de chosen du fait du changement de la largeur de colonne
			 * lorsqu'on est dans un tableau.
			 * Il faut le repasser visible pour avoir une taille correcte !
			 */
			select = $(this).prev().show(); 
			width = select.width();
			select.hide();
			$(this).css('width', width).find("> .chzn-drop").css('width', width - 2);
			/* Si un title était sur le sélect d'origine, le remettre sur le html de Chosen */
			if (title = select.attr('title')) { $(this).attr('title', title); }
		});
	}

	/* lance Chosen sur les .chosen */
	spip_chosen = function() {
		$("select.chosen").chosen();
		spip_chosen_width_and_title();
	}

	spip_chosen();
	onAjaxLoad(spip_chosen);
});
})(jQuery);
