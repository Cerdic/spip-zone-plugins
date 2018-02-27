<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

function formulaires_configurer_chapitres_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'choisir_objets',
			'options' => array(
				'nom' => 'objets',
				'label' => _T('chapitres:configurer_objets_label'),
				'exclus' => array('spip_chapitres'),
				'defaut' => lire_config('chapitres/objets'),
			),
		),
	);
	
	return $saisies;
}
