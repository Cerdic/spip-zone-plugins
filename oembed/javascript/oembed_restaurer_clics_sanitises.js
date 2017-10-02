/**
 * Si les scripts des boutons lecture d’obembed ont été mangés pour des raisons de sécurité,
 * mettre un lien sur l’image vers la source d’origine. C'est le cas pour les liens oembed sur des commentaires
 * de forum par exemple.
 */
if (window.jQuery) {
	jQuery(function($) {
		$(".oembed .oe-play-button button:not([onclick])").each(function() {
			var link = $(this).closest(".spip_documents").find(".oe-title").attr("href");
			$(this).wrap("<a href=\"" + link + "\" target=\"_blank\" style=\"display:block; position:absolute; top:0; left:0; right:0; bottom:0; background:transparent\"></a>")
		})
	})
}