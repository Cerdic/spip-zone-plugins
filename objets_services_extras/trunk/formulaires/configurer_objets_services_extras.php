<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_configurer_objets_services_extras_saisies_dist() {
	include_spip('inc/config');

	$config = lire_config('objets_services_extras', array());

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('objets_services_extras:cfg_titre_parametrages')
			),

			'saisies' => array(
				array(
					'saisie' => 'choisir_objets',
					'options' => array(
						'nom' => 'objets',
						'label' => _T('objets_services_extras:champ_chambres_objets'),
						'defaut' => $config['objets']
					)
				),
			)
		),
	);
}

