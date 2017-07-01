<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;
function formulaires_configurer_liens_associes_saisies_dist() {
	include_spip('inc/config');

	$config = lire_config('liens_associes', array());

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('liens_associes:cfg_titre_parametrages')
			),

			'saisies' => array(
				array(
					'saisie' => 'choisir_objets',
					'options' => array(
						'nom' => 'objets',
						'defaut' => $config['objets'],
						'label' => _T('liens_associes:label_lier_objets')
					)
				)
			)
		)
	);
}
?>
