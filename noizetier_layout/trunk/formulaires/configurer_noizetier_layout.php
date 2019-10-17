<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	//return;
}

/**
 * Déclaration des saisies
 */
function formulaires_configurer_noizetier_layout_saisies_dist() {

	include_spip('inc/config');

	$saisies = array(
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'inclure_css_public',
				'label' => _T('noizetier_layout:champ_cfg_inclure_css_public_label'),
				'label_case' => _T('noizetier_layout:champ_cfg_inclure_css_public_label_case'),
				'explication' => _T('noizetier_layout:champ_cfg_inclure_css_public_explication'),
				'defaut' => lire_config('noizetier_layout/inclure_css_public'),
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'activer_container',
				'label' => _T('noizetier_layout:champ_cfg_activer_container_label'),
				'label_case' => _T('noizetier_layout:champ_cfg_activer_container_label_case'),
				'explication' => _T('noizetier_layout:champ_cfg_activer_container_explication'),
				'defaut' => lire_config('noizetier_layout/activer_container'),
			),
		),
	);

	return $saisies;
}
