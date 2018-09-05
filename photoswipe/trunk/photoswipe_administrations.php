<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function photoswipe_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['0.1.0'] = array(
		array('ecrire_config','photoswipe', array(
			'galerie' => 'on'
		))
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function photoswipe_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}