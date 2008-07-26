

function afficher_infos_meta() {
	var compteur_inserer_photo_popup = 0;

	$('#portfolio_portfolio td.document img.miniature_document').parent().each(function() {
		
		compteur_inserer_photo_popup++;
		
		var fichier = $(this).attr("href");
		var type = $(this).attr("type");

		if (type == "image/jpeg") {
			url = "?exec=pave_exif&fichier="+fichier;
		
				$(this).after("<div class='inserer_photo_meta' id='inserer_photo"+compteur_inserer_photo_popup+"' return false;\"></div>");
				$("#inserer_photo"+compteur_inserer_photo_popup).load(url);
		
			
		}	
	});

}

window.onload = function () {

	afficher_infos_meta();

	// Machin pas terrible du tout pour reafficher les meta-donnees quand reload ajax (ajouter/supprimer document)
	$('#portfolio').bind("mouseover", function () {
		if (!$(".inserer_photo_meta:first").is(":visible")) afficher_infos_meta();
	});



}