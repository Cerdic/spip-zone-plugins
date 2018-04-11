<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

function formulaires_configurer_prestations_saisies_dist() {
	$config = lire_config('prestations');
	
	$saisies = array(
		array(
			'saisie' => 'choisir_objets',
			'options' => array(
				'nom' => 'objets',
				'label' => _T('Contenus dans lesquels machin'),
				'exclus' => array('spip_prestations', 'spip_prestations_unites', 'spip_prestations_types'),
				'defaut' => $config['objets'],
			),
		),
	);
	
	return $saisies;
}
