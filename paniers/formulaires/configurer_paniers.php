<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_paniers_saisies_dist(){
	include_spip('inc/config');
	$config = lire_config('paniers');

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('paniers:limite_titre')
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'exp1',
						'texte' => _T('paniers:limite_explication')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'limite_ephemere',
						'label' => _T('paniers:limite_ephemere_label'),
						'defaut' => $config['limite_ephemere'],
						'li_class' => 'long_label'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'limite_enregistres',
						'label' => _T('paniers:limite_enregistres_label'),
						'defaut' => $config['limite_enregistres'],
						'li_class' => 'long_label'
					)
				)
			)
		)
	);
}

?>
