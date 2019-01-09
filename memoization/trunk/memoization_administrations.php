<?php
/**
 * Fichier gérant l'installation et la désinstallation du plugin
 *
 * @package SPIP\memoization\Installation
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
		return;
}

/**
 * Installation/maj des tables de memoization...
 *
 * @param string $nom_meta_base_version
 *	 Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *	 Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function memoization_upgrade($nom_meta_base_version, $version_cible) {
	// Création des tables
	include_spip('base/create');
	include_spip('base/abstract_sql');

	$maj = array();
	// Pas de create

	$maj['20190109'] = array(
		array('memoization_migrer_config_pages')
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function memoization_migrer_config_pages() {
	include_spip('inc/config');
	debug_log ("memoization_migrer_config_pages done", "memoization_migrer_config_pages");
	if ($methode=lire_config('memoization/pages')) {
		ecrire_config('memoization/methode', $methode);
		effacer_config('memoization/pages');
	}
}

function memoization_vider_tables($nom_meta_base_version) {
	include_spip('inc/config');
	include_spip('inc/meta');

	effacer_config("memoization");
	effacer_meta($nom_meta_base_version);
}
