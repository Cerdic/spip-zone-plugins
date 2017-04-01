<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_configurer_reservation_bank_saisies_dist() {
	include_spip('inc/config');
	$config = lire_config('reservation_bank');

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('reservation_evenement:cfg_titre_parametrages')
			),

			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'cacher_paiement_public',
						'label' => _T('reservation_bank:label_cacher_paiement_public'),
						'defaut' => $config['cacher_paiement_public']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'preceder_formulaire',
						'label' => _T('reservation_bank:label_preceder_formulaire'),
						'defaut' => $config['preceder_formulaire'],
						'afficher_si' => '@cacher_paiement_public@ == ""',
					)
				),
			),


		)
	);
}
?>
