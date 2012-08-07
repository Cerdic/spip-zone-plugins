/**
 * javascript/fmp3_prive.js
 * 
 * $LastChangedRevision$
 * $LastChangedBy$
 * $LastChangedDate$
 * 
 * @author Christian Paulus (paladin@quesaco.org)
 * 
 * Licence GNU
 * 
 * Fonctions utilisées pour l'espace privé.
 * 
 */

jQuery(document).ready(function() {
	
	var request_uri = window.location.search;
	var cible_id = "";
	
	/* page article ? */
	if(request_uri.match(/^\?exec=article[s]?&id_article=/)) 
	{
		var article_id = request_uri.replace(/^\?exec=article[s]?&id_article=([0-9]+).*$/, "$1");
		if(article_id > 0)
		{
			var cible_id = "#iconifier-" + article_id;
		}
	}

	/* page rubrique SPIP <= 2 ? */
	else if(request_uri.match(/^\?exec=naviguer&id_rubrique=/)) 
	{
		var rubrique_id = request_uri.replace(/^\?exec=naviguer&id_rubrique=([0-9]+).*$/, "$1");
		if(rubrique_id > 0)
		{
			var cible_id = "#iconifier-" + rubrique_id;
		}
	}
	/* page rubrique SPIP >= 3 ? */
	else if(request_uri.match(/^\?exec=rubrique&id_rubrique=/)) 
	{
		var rubrique_id = request_uri.replace(/^\?exec=rubrique&id_rubrique=([0-9]+).*$/, "$1");
		if(rubrique_id > 0)
		{
			var cible_id = "#iconifier-" + rubrique_id;
		}
	}
	
	/* page de configuration principale du site ? */
	else if(
			request_uri.match(/^\?exec=configuration/) ||
			request_uri.match(/^\?exec=configurer_identite/)
			) 
	{
		var cible_id = "#iconifier-0";
	}

	$.fn.extend({
		/*
		 * Place un écouteur au submit
		 * Si le conteneur IFRAME est modifié, recharge la boite
		 */
		form_survey: function(){    
			/* $("#son-loader").addClass("loader-visible");/* A voir + tard */
			if ($.browser.msie) {
				// load() ne fonctionne pas toujours sous IE ??
				// voir si autre méthode...
				$("#form-boite-son").hover(function(){
					$(this).son_recharger_boite();
				});
			}
			else {
				$("#hiddeniframe").load(function(){
					$(this).son_recharger_boite();
				});
			}
			return(true);
		}
		,
		/*
		 * Plieur/déplieur (click sur triangle)
		 */
		swap_me: function(i1, t1, i2, t2, id_couche) {
			/*if($(this).hasClass("haut")) /* hasClass n'existe pas dans la dist */
			if($(this).attr("class") == "haut")
			{
				$(this).removeClass("haut").addClass("bas").attr("src", i2).attr("alt", t2).attr("title", t2);
				$(id_couche).show();
			}
			else {
				$(this).removeClass("bas").addClass("haut").attr("src", i1).attr("alt", t1).attr("title", t1);
				$(id_couche).hide();
			}
		}
		,
		/*
		 * Rafraissement de la boite
		 */
		son_recharger_boite: function(){
			if((fmp3_boite_son_url.length > 0) && (cible_id.length > 0))
			{
				$.ajax({
					type: "POST"
					, data : ""
					, url: fmp3_boite_son_url
					, success: function(msg){
						$("#fmp3_boite_son").html(msg);
					}
				});
			}
		}
		,
		son_supprimer: function(){
			$.ajax({
				type: "POST"
				, data : "supprimer=oui"
				, url: fmp3_boite_son_url
				, success: function(msg){
					$("#fmp3_boite_son").html(msg);
				}
			});
		}
		,
		son_valider_champ: function(e){ 
			var fichier_son = $("input[@name=fichier-son]").val();
			if(fichier_son.length > 0)
			{
				return(true);
			}
			else {
				alert(e);
			}
			return(false);
		}
	});
	
	/*
	 * Ajoute la boite son pour l'objet
	 */
	if((fmp3_boite_son_url.length > 0) && (cible_id.length > 0))
	{
		/**
		 * SPIP 3 ne connait plus iconifier.
		 * Dommage !
		 * Cibler le bloc logo de l'objet
		 */
		if ( fmp3_spip_version_3 )
		{
			cible_id = "div.formulaire_editer_logo";
		}
		
		/**
		 * Coller la boite edition du son juste après
		 */
		$(cible_id).after("<div id=\"fmp3_boite_son\"></div>");
		
		if ($("#fmp3_boite_son").html() == "") {
			$.ajax({
				type: "POST"
				, data : ""
				, url: fmp3_boite_son_url
				, success: function(msg){
					$("#fmp3_boite_son").html(msg);
				}
				,
				error: function(jqXHR, textStatus, errorThrown) {
				  // Une erreur s'est produite lors de la requete
				}
			});
			
		}
	}

	/*
	 * Active le player
	 */
	$(".mp3").jmp3();

});
