// spiplistes_courrier_edit.js
// utilis� par _SPIPLISTES_EXEC_COURRIER_EDIT

// $LastChangedRevision: 15830 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-10-07 05:41:13 +0200 (dim., 07 oct. 2007) $

$(document).ready(function(){
	
	$("#ajax-loader").hide();
	
	$("#ajax-loader").ajaxStart(function(){
			$(this).show();
	});
		
	$("#ajax-loader").ajaxStop(function(){
		$(this).hide();
	});

	// deux boutons de validation dans la page
	// s�lectionne soit une validation du contenu titre texte
	// soit valide le contenu g�n�r� par pr�visu
	$("#formulaire_courrier_edit").submit(function(){
		if($("#btn_courrier_edit").val() == "oui") {
		// c'est le bouton du bas courrier_edit qui valide
			return (true);
		}
		else {
		// c'est le bouton de previsu qui valide
			var	 data = $('input,textarea,radio,select, checkbox', this).serialize();
			$.ajax({ type: "POST", 
						url: "./?exec=spiplistes_courrier_previsu", 
						data: data, 
						success: function(msg){  $("#apercu-courrier").html(msg); }
				});
			}
		return (false);
	});
});