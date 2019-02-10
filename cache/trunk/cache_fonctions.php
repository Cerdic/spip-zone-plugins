<?php
/**
 * Ce fichier contient les fonctions d'API du plugin Cache Factory qui servent aussi de filtres dans
 * les squelettes.
 *
 * @package SPIP\CACHE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Retourne la liste des plugins utilisant les services de Cache Factory.
 *
 * @api
 * @filtre
 *
 * @uses lire_config()
 *
 * @return array
 *        Tableau des préfixes de plugin utilisant Cache Factory.
 */
function cache_plugin_repertorier() {

	// Initialisation du tableau des plugins.
	$plugins = array();

	// Récupération de la meta du plugin Cache
	include_spip('inc/config');
	$configuration = lire_config('cache', array());

	if ($configuration) {
		// Chaque plugin est un index de la meta 'cache'.
		$plugins = array_keys($configuration);
	}

	return $plugins;
}
