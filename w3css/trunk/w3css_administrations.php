<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function w3css_upgrade($nom_meta_base_version, $version_cible) {

	// Création du tableau des mises à jour.
	$maj = array();

	$config_default = array(
		'extend' => '',
		'namespace' => 'w3-',
		'theme' => '#616161',
	);

	// Tableau de la configuration par défaut
	$maj['create'] = array(
		array('ecrire_config', 'w3css', $config_default)
	);

	// Maj du plugin.
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/*
 *   Désintaller
 */
function w3css_vider_tables($nom_meta_base_version) {
	// Supprimer les méta, ou oublie pas celle de la base.
	effacer_meta('w3css');
	effacer_meta($nom_meta_base_version);
}