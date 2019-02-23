<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_configurer_espaces_saisies_dist() {
	include_spip('inc/config');

	$config = lire_config('espaces', array());

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('espaces:cfg_titre_parametrages')
			),

			'saisies' => array(
				array(
					'saisie' => 'choisir_objets',
					'options' => array(
						'nom' => 'objets',
						'label' => _T('espaces:champ_chambres_objets'),
						'defaut' => $config['objets']
					)
				),

			)
		),
	);
}

