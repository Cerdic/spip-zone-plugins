
function calculer_hauteur_img_fond() {

	$("#skrollr-body").css("height", "auto");

	$(".remplir_vertical").css("height","auto").find(".interieur").each(function() {
		var hauteur_ecran = $(window).height();
	
		$(this).css( "min-height", hauteur_ecran );
		
		var contenu = $(this).find(".fonds_contenu");
		if (contenu.innerHeight() < hauteur_ecran) {
			var decal_contenu = (hauteur_ecran - contenu.innerHeight()) /2;
			contenu.css("padding-top", decal_contenu).css("padding-bottom", decal_contenu-1);
		} else {
			contenu.css("padding-top", 0).css("padding-bottom", 0);
		}
	});

	$(".fonds_svg").each(function() {
		if ($(this).attr("data-largeur") > 0) {
			var rapport_fichier = $(this).attr("data-hauteur") / $(this).attr("data-largeur");
			var rapport_box = $(this).height() / $(this).width();

			if (rapport_box > rapport_fichier) {
				$(this).find(".spip_vivus_svg").width( $(this).height() / rapport_fichier )
					.css("margin-left", ($(this).width() - $(this).find(".spip_vivus_svg").width())/2 );
			} else {
				$(this).find(".spip_vivus_svg").height( $(this).width() * rapport_fichier )
					.css("margin-top", ($(this).height() - $(this).find(".spip_vivus_svg").height())/2 );
			}
		}
	});
}


$(document).ready(calculer_hauteur_img_fond);
$(window).smartresize(calculer_hauteur_img_fond);