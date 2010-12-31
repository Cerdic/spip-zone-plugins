// spiplistes_courrier_edit.js
// utilise' par _SPIPLISTES_EXEC_COURRIER_EDIT

// $LastChangedRevision: 15830 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-10-07 05:41:13 +0200 (dim., 07 oct. 2007) $

jQuery(document).ready(function(){
	
	// [@attr] style selectors removed in jQuery 1.3 (voir http://docs.jquery.com/Selectors)
	// rendre compatible avec anciennes versions de jQuery
	var jqss = (jQuery.fn.jquery < '1.3') ? '@' : '';
	
	jQuery('#ajax-loader').hide();
	
	jQuery('#ajax-loader').ajaxStart(function(){
			jQuery(this).show();
	});
		
	jQuery('#ajax-loader').ajaxStop(function(){
		jQuery(this).hide();
	});

	// deux boutons de validation dans la page
	// sélectionne soit une validation du contenu titre texte
	// soit valide le contenu généré par prévisu
	jQuery('#formulaire_courrier_edit').submit(function(){
		if(jQuery('#btn_courrier_edit').val() == 'oui') {
		// c'est le bouton du bas courrier_edit qui valide
			return (true);
		}
		else {
		// c'est le bouton de previsu qui valide
			var data = jQuery('input['+jqss+'name=titre],input['+jqss+'name=avec_patron],input['+jqss+'name=id_courrier],input['+jqss+'type=checkbox]['+jqss+'checked],input['+jqss+'type=radio]['+jqss+'checked],select,textarea',this).serialize();
			jQuery.ajax({ type: 'POST', 
						url: './?exec=spiplistes_courrier_previsu', 
						data: data,
						async:false,
						success: function(msg){
							jQuery('#apercu-courrier').html(msg);
						}
				});
			}
		return (false);
	});
	jQuery('#avec_intro').click(function(){
		if($(this).attr('checked')) {
			jQuery('#choisir_intro').show();
			jQuery(this).val('oui');
		} else {
			jQuery('#choisir_intro').hide();
			jQuery(this).val('non');
		}
	});
	jQuery('#avec_patron').click(function(){
		if($(this).attr('checked')) {
			jQuery('#choisir_patron').show();
			jQuery(this).val('oui');
		} else {
			jQuery('#choisir_patron').hide();
			jQuery(this).val('non');
		}
	});
	jQuery('#avec_sommaire').click(function(){
		if($(this).attr('checked')) {
			jQuery('#patron_pos').show();
			jQuery('#choisir_sommaire').show();
			jQuery(this).val('oui');
		} else {
			jQuery('#patron_pos').hide();
			jQuery('#choisir_sommaire').hide();
			jQuery(this).val('non');
		}
	});
	
	jQuery.fn.extend({
		switch_previsu: function() {
			jQuery('.switch-previsu').toggle();
		}
	});

});