<?php
/**
 * Ce fichier contient l'ensemble des constantes et des utilitaires nécessaires au fonctionnement du plugin.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cache');
include_spip('inc/config');

function demo_cache() {

	// Initialisation du plugin. La configuration du plugin est fournie par le service taxonomie_cache_configurer().
	$plugin = 'taxonomie';

	echo '<br />Nettoyage de la configuration du plugin pour être sur de partir de zéro.';
	$config_cache = lire_config('cache', array());
	if (isset($config_cache[$plugin])) {
		unset($config_cache[$plugin]);
		ecrire_config('cache', $config_cache);
	}

	echo '<br />Lecture de la configuration : vide car jamais enregistrée';
	$retour = cache_configuration_lire($plugin);
	var_dump($retour);

	echo '<br />Test de cache inexistant : la configuration est enregistrée.';
	$cache1 = array(
		'service' => 'itis',
		'action'  => 'record',
		'tsn'     => 132588
	);
	$retour = cache_existe($plugin, $cache1);
	var_dump($retour);

	echo '<br />Lecture de la configuration : cette fois elle est complète';
	$retour = cache_configuration_lire($plugin);
	var_dump($retour);

	echo '<br />Ecriture d\'un tableau dans cache wikipedia inexistant : on stocke la config des caches récupérée à l\'étape précédente.';
	$cache2 = array(
		'service' => 'wikipedia',
		'action'     => 'get',
		'tsn'     => 132588,
		'langue'  => 'fr'
	);
	$retour = cache_ecrire($plugin, $cache2, $retour);
	var_dump($retour);

	echo '<br />Test de cache existant : le chemin complet est retourné.';
	$retour = cache_existe($plugin, $cache2);
	var_dump($retour);

	echo '<br />Lecture du cache précédemment écrit : on retrouve la config désérialisée';
	$retour = cache_lire($plugin, $cache2);
	var_dump($retour);

	echo '<br />Suppression du cache créé';
//	$retour = cache_supprimer($plugin, $cache2);
	var_dump($retour);
}
