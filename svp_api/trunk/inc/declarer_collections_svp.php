<?php
/**
 * Ce fichier contient la fonction de déclaration des configurations de collections. Elle appelle le pipeline
 * `declarer_collections_svp` pour les plugins qui le souhaitent (voir SVP Typologie).
 *
 * @package SPIP\SVPAPI\CONFIGURATION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclare les collections accessibles via HTTP GET.
 * Par défaut, le plugin propose les collections `plugins` et `depots`.
 *
 * @pipeline declarer_collections_svp
 *
 * @return array
 * 		   Description des collections.
**/
function inc_declarer_collections_svp_dist() {

	// Initialisation en static pour les performances
	static $collections = array();

	if (!$collections) {
		// Les index désignent les collections, le tableau associé contient les filtres admissibles.
		// -- Par défaut, svpapi fournit deux collections, plugins et depots.
		$collections = array(
			'plugins' => array(
				'ressource' => 'prefixe',
				'module'    => 'svpapi',
				'filtres'   => array(
					array(
						'critere' => 'compatible_spip'
					),
				)
			),
			'depots'  => array(
				'module'  => 'svpapi',
				'filtres' => array(
					array(
						'nom' => 'type'
					)
				)
			)
		);

		// On complète par des collections fournies par d'autres plugin
		$collections = pipeline('declarer_collections_svp', $collections);
	}

	return $collections;
}
