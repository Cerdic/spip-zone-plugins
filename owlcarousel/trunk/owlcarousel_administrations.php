<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function owlcarousel_upgrade($nom_meta_base_version, $version_cible) {

	// Création du tableau des mises à jour.
	$maj = array();

	$config = array(
		'css' => 'on',
		'header_prive'=> 'on'
	);

	// Tableau de la configuration par défaut
	$maj['0.0.1'] = array(
		array('ecrire_config', 'owlcarousel', $config)
	);
	
	// Maj du plugin.
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/*
 *   Désintaller
 */
function owlcarousel_vider_tables($nom_meta_base_version) {
	// Supprimer les méta, ou oublie pas celle de la base.
	effacer_meta('owlcarousel');
	effacer_meta($nom_meta_base_version);
}
