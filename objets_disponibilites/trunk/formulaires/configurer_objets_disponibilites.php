<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_configurer_objets_disponibilites_saisies_dist() {
	include_spip('inc/config');

	$config = lire_config('objets_disponibilites', array());

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('objets_disponibilites:cfg_titre_parametrages')
			),

			'saisies' => array(
				array(
					'saisie' => 'choisir_objets',
					'options' => array(
						'nom' => 'objets',
						'label' => _T('objets_disponibilites:champ_chambres_objets'),
						'defaut' => $config['objets']
					)
				),

			)
		),
	);
}

