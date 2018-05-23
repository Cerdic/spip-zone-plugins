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
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'publier_auto',
				'label' => _T('chapitres:configurer_publier_auto_label'),
				'label_case' => _T('chapitres:configurer_publier_auto_label_case'),
			),
		),
	);

	return $saisies;
}