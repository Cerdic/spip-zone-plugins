<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;
function formulaires_configurer_reservations_multiples_saisies_dist() {
	include_spip('inc/config');
	$config = lire_config('reservations_multiples', array());

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('reservations_multiples:cfg_titre_parametrages')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'multiple_personnes',
						'label' => _T('reservations_multiples:label_multiple_personnes'),
						'explication' => _T('reservations_multiples:explication_multiple_personnes'),
						'defaut' => $config['multiple_personnes']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'nombre_limite',
						'label' => _T('reservations_multiples:nombre_limite'),
						'afficher_si' => '@multiple_personnes@ == "on"',
						'defaut' => $config['nombre_limite']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'multiple_inscriptions',
						'label' => _T('reservations_multiples:label_multiple_inscriptions'),
						'explication' => _T('reservations_multiples:explication_multiple_inscriptions'),
						'defaut' => $config['multiple_personnes']
					)
				)
			)
		)
	);
}

