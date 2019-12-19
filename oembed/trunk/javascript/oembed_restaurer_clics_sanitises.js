/**
 * Si les scripts des boutons lecture d’obembed ont été mangés pour des raisons de sécurité,
 * mettre un lien sur l’image vers la source d’origine. C'est le cas pour les liens oembed sur des commentaires
 * de forum par exemple.
 */
if (window.jQuery) {
	function relink_oembed(){
		jQuery(".oembed", this)
			.find(".oe-play-button button:not([onclick])")
			.not('.relinked').each(function() {
				var href = jQuery(this).closest(".spip_documents").find(".oe-title").attr("href");
				if (href) {
					jQuery(this)
						.addClass('relinked')
						.wrap("<a href=\"" + href + "\" target=\"_blank\" rel=\"noopener\" style=\"display:block; position:absolute; top:0; left:0; right:0; bottom:0; background:transparent;overflow:hidden\"></a>")
				}
			});
	}
	jQuery(relink_oembed);
	onAjaxLoad(relink_oembed);
}