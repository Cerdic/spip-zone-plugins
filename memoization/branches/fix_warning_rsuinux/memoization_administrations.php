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

	$maj['create'] = array(
		array('memoization_init_cache_namespace'),
		array('memoization_init_cache_key'),
	);

	$maj['20190109'] = array(
		array('memoization_migrer_config_pages')
	);
	$maj['20190228'] = array(
		array('memoization_init_cache_key')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function memoization_init_cache_key() {
	include_spip("inc/securiser_action");
	$key = pack("H*", calculer_cle_action('memoization'));
	$key = base64_encode($key);
	ecrire_meta('cache_key', $key, 'non');

	// il faut redefinir le namespace car on introduit une cle -> invalidation des caches existants
	memoization_init_cache_namespace();
}

function memoization_init_cache_namespace() {
	include_spip('inc/acces');
	$namespace = dechex(crc32($_SERVER["DOCUMENT_ROOT"] . $_SERVER["SERVER_SIGNATURE"] . creer_uniqid()));
	ecrire_meta('cache_namespace', $namespace, 'non');
}


function memoization_migrer_config_pages() {
	include_spip('inc/config');
	spip_log ("memoization_migrer_config_pages done", "memoization_migrer_config_pages");
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
