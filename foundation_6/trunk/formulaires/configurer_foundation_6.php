<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_foundation_6_saisies() {
	// Lire le fichier YAML qui contient la structure du formulaire.
	$saisies = array(
		array(
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'javascript',
				'label' => _T('foundation_6:activer_javascript'),
				'explication' => _T('foundation_6:activer_javascript_explication')
			)
		),
		array(
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'foundation-icons',
				'label' => _T('foundation_6:icons'),
				'explication' => _T('foundation_6:icons_explication')
			)
		)
	);
	return $saisies;
}

function formulaires_configurer_foundation_6_charger() {
	// Lire la configuration de foundation
	$config = lire_config('foundation_6');

	return $config;
}