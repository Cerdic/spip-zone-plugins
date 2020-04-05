<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des saisies
 */
function formulaires_configurer_selections_editoriales_saisies_dist() {

	include_spip('inc/config');

	$saisies = array(
		array(
			'saisie' => 'choisir_objets',
			'options' => array(
				'nom' => 'objets',
				'label' => _T('selections_editoriales:configurer_objets_label'),
				'exclus' => array('spip_selections', 'spip_selections_contenus'),
				'defaut' => lire_config('selections_editoriales/objets'),
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'reutilisation',
				'label_case' => _T('selections_editoriales:configurer_reutilisation_label_case'),
				'explication' => _T('selections_editoriales:configurer_reutilisation_explication'),
				'defaut' => lire_config('selections_editoriales/reutilisation'),
			),
		),
	);

	return $saisies;
}