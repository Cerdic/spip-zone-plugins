/*
 * Gestion des boutons d'édition d'un album dans l'espace privé.
 * - afficher le formulaire d'édition dans le header
 * - afficher le formulaire d'ajout de document dans le footer
 */
jQuery(document).ready(function($) {
	// édition du texte dans le header
	function editer_album(album) {
		var header = album.find(".header-album");
		var btn_editer = header.find(".boutons-edition .bouton.editer");
		var btn_fermer = header.find(".boutons-edition .bouton.fermer");
		var texte = header.find(".contenu .texte");
		var edition = header.find(".contenu .edition");
		btn_editer.click(function() {
			header.addClass("hover");
			texte.slideUp(300);
			edition.slideDown(300, function(){edition.find("input[id='titre']").focus();});
			btn_editer.hide(); btn_fermer.show(); 
			return false;
		});
		btn_fermer.click(function() {
			header.removeClass("hover");
			edition.slideUp(300);texte.slideDown(300);
			btn_editer.show(); btn_fermer.hide();
			return false;
		});
	}
	// ajout de documents
	function remplir_album(album) {
		var footer = album.find(".footer-album");
		var remplir = footer.find(".remplir-album");
		var boutons = footer.find(".boutons-edition");
		var btn_remplir = boutons.find(".bouton.remplir");
		var btn_fermer = remplir.find(".bouton.fermer");
		btn_remplir.click(function() {
			remplir.slideDown(300, function(){remplir.find("input[name='fichier_upload[]']").focus();});
			boutons.slideUp(300);
			footer.addClass("hover");
			return false;
		});
		btn_fermer.click(function() {
			boutons.slideDown(300);
			remplir.slideUp(300);
			footer.removeClass("hover");
			return false;
		});
	}
	// fonction d'appel
	function outils_albums() {
		var albums = $(".boite-album[data-objet='album']");
		if (albums.length > 0) {
			albums.each(function( index, album ) {
				// éditer le texte
				editer_album($(album));
				// ajouter des documents
				remplir_album($(album));
			});
		}
	}
	// go !
	outils_albums();
	if (window.jQuery) jQuery(function(){onAjaxLoad(outils_albums);});
});
