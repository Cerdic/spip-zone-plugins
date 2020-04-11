/** Gérer le formulaire d’illustration de documents avec Bigup */
function formulaires_adapter_image_avec_bigup () {
	// trouver les input qui envoient des fichiers
	$(".formulaire_adapter_image")
		.find("form .editer_fichier_upload")
		.find("label").hide().end()
		.find("input[type=file].bigup_illustration")
		.not('.bigup_done')
		.bigup()
		.on('bigup.fileSuccess', function(event, file, description) {
			var bigup = file.bigup;
			var input = file.emplacement;

			var data = $.extend(bigup.getFormData(), {
				joindre_upload: true,
				joindre_zip: true, // les zips sont conservés zippés systématiquement.
				formulaire_action_verifier_json: true,
				bigup_reinjecter_uniquement: [description.bigup.identifiant],
			});

			// verifier les champs
			$.post(bigup.target, data, null, 'json')
				.done(function(erreurs) {
					var erreur = data.fichier_upload || erreurs.message_erreur;
					if (erreur) {
						bigup.presenter_erreur(input, erreur);
					} else {
						delete data.formulaire_action_verifier_json;
						var conteneur = bigup.form.parents('.formulaire_spip');
						conteneur.animateLoading();
						// Faire le traitement prévu, supposant qu'il n'y aura pas d'erreur...
						$.post(bigup.target, data)
							.done(function(html) {
								bigup.presenter_succes(input, _T('bigup:succes_vignette_envoyee'));
								bigup.form.parents('.formulaire_spip').parent().html(html);
							})
							.fail(function(data) {
								conteneur.endLoading();
								bigup.presenter_erreur(input, _T('bigup:erreur_probleme_survenu'));
							});
					}
				})
				.fail(function(data) {
					bigup.presenter_erreur(input, _T('bigup:erreur_probleme_survenu'));
				});
		})
		.closest('.editer').find('.dropfiletext').html(_T('bigup:deposer_la_vignette_ici'));
}
jQuery(function($) {
	formulaires_adapter_image_avec_bigup();
	onAjaxLoad(formulaires_adapter_image_avec_bigup);
});
