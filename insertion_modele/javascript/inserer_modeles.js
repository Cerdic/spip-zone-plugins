var curseur;
$(document).ready(function() {
	// Insertion du div de la modale
	$("body").append('<div class="window" id="modale_images"></div><div id="inserer_modeles_mask"></div>');

	//select all the a tag with name equal to modal
	$('.outil_barre_img_dft').click(function(e) {

		// sauvegarder le curseur		
		var range = $("textarea[name=texte]").getSelection();

		//Cancel the link behavior
		e.preventDefault();

		// Qui ?
		var id_objet, objet;
		if ($('input[name=id_article]').length > 0)
		{
			id_objet = $('input[name=id_article]').val();
			objet = "article";
		} else if ($('input[name=id_rubrique]').length > 0)
		{
			id_objet = $('input[name=id_rubrique]').val();
			objet = "rubrique";
		}
		
		// Mettre dans les pipeline de document la création des vignettes pour ne pas avoir une page vide !
		
		// Recupérer les images en AJAX pour l'afficher dans la modal
		$.ajax({
			type: "GET",
			url: "?exec=modal_images",
			data: "objet=" + objet + "&id_objet=" + id_objet,
			success: function(r){$("#modale_images").html(r);},
			async: false
		}).responseText;

		recalculTailleMask ()
		
		// la transition du mask (simple fade)
		$('#inserer_modeles_mask').fadeIn(500);
		$('#inserer_modeles_mask').fadeTo("fast",0.8);

		// la transition de la modale
		$("#modale_images").fadeIn(500);
		
		$(window).resize(function(){
			recalculTailleModale();
			recalculTailleMask ();
		});

	});

	// fermer si on clique sur le mask
	$('#inserer_modeles_mask').click(function () { 
		$(this).hide();
		$('.window').hide();
	});
});

onAjaxLoad(submitInsererModelesSelectImage);
onAjaxLoad(InsererModelesWindowClose);
onAjaxLoad(submitInsererModelesSelectParams);
onAjaxLoad(recalculTailleModale);

function recalculTailleMask ()
{
	// Choper les hauteur / largeur du document pour recouvrir tout
	var maskHeight = $(document).height();
	var maskWidth = $(window).width();

	// Mettre les dimension au fond noir
	$('#inserer_modeles_mask').css({'width':maskWidth,'height':maskHeight});
}

function recalculTailleModale ()
{
	// Dimension de la fenêtre
	var winH = $(window).height();
	var winW = $(window).width();
	
	//Calculer et positionner la modale au centre
	$("#modale_images").css('top',  winH/2-$("#modale_images").height()/2);
	$("#modale_images").css('left', winW/2-$("#modale_images").width()/2);
}

function submitInsererModelesSelectParams(){
	$("#edit_parametres").unbind("submit");

	$("#edit_parametres").submit(function(){			
		$(".fadeOut").fadeTo("fast",0.5);
		var inputs = $("#edit_parametres").serialize();
		$.ajax({
			type: "GET",
			url: "?exec=calcul_parametres",
			data: inputs,
			success:function(r){
				$("textarea[name=texte]").replaceSelection(r, true);
				$('#inserer_modeles_mask, .window').hide();
			},
			async: false
		});

		return false;
	 });
}

function submitInsererModelesSelectImage(){
	$("#select_image").unbind("submit");
	
	$("#select_image").submit(function(){			
		$("#div_image_select").fadeTo("fast",0.5);
		var inputs = $("#select_image").serialize();
		$.ajax({
			type: "GET",
			url: "?exec=modal_images_parametres",
			data: inputs,
			success:function(r){
				$("#modale_images").html(r).fadeTo("fast", 1);
			},
			async: false
		});
		
		return false;
	});
}

function InsererModelesWindowClose(){
	$(".window .close").unbind("click");

	// fermer
	$('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		$('#inserer_modeles_mask, .window').hide();
	});
}