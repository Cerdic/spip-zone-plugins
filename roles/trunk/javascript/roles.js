;(function ($) {
jQuery(document).ready(function(){
	spip_chosen = function() {
		/* Charger chosen sur les sélecteurs de roles */
		$("select.selection_roles").chosen();
		$('.chzn-container').each(function (index) {
			/* remettre la bonne taille des éléments qui change avec la
			 * présence de chosen du fait du changement de la largeur de colonne */
			select = $(this).prev().show(); /* il faut le repasser visible pour avoir une taille correcte ! */
			width = select.width();
			select.hide();
			$(this).css('width', width).find("> .chzn-drop").css('width', width - 2);
			/* Si un title était sur le sélect d'origine, le remettre sur le html de Chosen */
			if (title = select.attr('title')) { $(this).attr('title', title); }
		});
	}
	spip_chosen();
	onAjaxLoad(spip_chosen);
});
})(jQuery);
