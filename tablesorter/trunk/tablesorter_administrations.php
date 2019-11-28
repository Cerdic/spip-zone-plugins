<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj de tablesorter
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function tablesorter_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('base/upgrade');
	$maj = array();
	$maj['create'] = array();
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Désinstallation
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function tablesorter_vider_tables($nom_meta_base_version) {

	include_spip('inc/meta');
	include_spip('inc/config');
	// On efface la version enregistrée
	effacer_config('tablesorter');
	effacer_meta($nom_meta_base_version);
}

