// spiplistes_courrier_edit.js
// utilisé par _SPIPLISTES_EXEC_COURRIER_EDIT

// $LastChangedRevision: 15830 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-10-07 05:41:13 +0200 (dim., 07 oct. 2007) $

jQuery(document).ready(function(){
	
	jQuery("#ajax-loader").hide();
	
	jQuery("#ajax-loader").ajaxStart(function(){
			jQuery(this).show();
	});
		
	jQuery("#ajax-loader").ajaxStop(function(){
		jQuery(this).hide();
	});

	// deux boutons de validation dans la page
	// sélectionne soit une validation du contenu titre texte
	// soit valide le contenu généré par prévisu
	jQuery("#formulaire_courrier_edit").submit(function(){
		if(jQuery("#btn_courrier_edit").val() == "oui") {
		// c'est le bouton du bas courrier_edit qui valide
			return (true);
		}
		else {
		// c'est le bouton de previsu qui valide
			var data = jQuery('input[@type=checkbox][@checked],input[@type=radio][@checked],select,textarea',this).serialize();
			jQuery.ajax({ type: "POST", 
						url: "./?exec=spiplistes_courrier_previsu", 
						data: data, 
						success: function(msg){  jQuery("#apercu-courrier").html(msg); }
				});
			}
		return (false);
	});
	jQuery("#avec_intro").click(function(){
		if($(this).attr('checked')) {
			jQuery("#choisir_intro").show();
			jQuery(this).val('oui');
		} else {
			jQuery("#choisir_intro").hide();
			jQuery(this).val('non');
		}
	});
	jQuery("#avec_patron").click(function(){
		if($(this).attr('checked')) {
			jQuery("#choisir_patron").show();
			jQuery(this).val('oui');
		} else {
			jQuery("#choisir_patron").hide();
			jQuery(this).val('non');
		}
	});
	jQuery("#avec_sommaire").click(function(){
		if($(this).attr('checked')) {
			jQuery("#patron_pos").show();
			jQuery("#choisir_sommaire").show();
			jQuery(this).val('oui');
		} else {
			jQuery("#patron_pos").hide();
			jQuery("#choisir_sommaire").hide();
			jQuery(this).val('non');
		}
	});
});