/**
 * Fonction a lancer lors de la verification du formulaire qui s'occupe d'empecher la sauvegarde
 */

function crayon_affiche_submit(me,id){
	// Si aucune erreur
	if(id.find('p.erreur:visible,label.error:visible,span.erreur_message:visible').length == 0){
		// On (re)active les combinaisons de touches par defaut des crayons
		id.bind('form-pre-serialize',function(event, form, formOptions, veto){
			veto.veto = false;
		});
		// Reafficher le bouton de validation
		id.find('.crayon-submit').show();
	}
	else{
		id.bind('form-pre-serialize',function(event, form, formOptions, veto){
			veto.veto = true;
		});
		// Cacher le bouton de validation
		id.find('.crayon-submit').hide();
	}
}

jQuery.validator.setDefaults({
	errorElement:"span",
	errorClass: "erreur_message",
	errorPlacement: function(error, element) {
		element.parents('li').addClass('erreur').find('label').after(error);
	},
	highlight:function(){}
});