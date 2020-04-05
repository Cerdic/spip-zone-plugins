<?php
/**
 * Ce fichier contient la fonction de déclaration des configurations de collections. Elle appelle le pipeline
 * `declarer_collections_svp` pour les plugins qui le souhaitent (voir SVP Typologie).
 *
 * @package SPIP\SVPAPI\EZCOLLECTION\COLLECTION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclare les collections accessibles via l'API ezREST.
 * Par défaut, le plugin propose les collections `plugins` et `depots`.
 *
 * @pipeline liste_ezcollection
 *
 * @param array $collections Configuration des collections déjà déclarées.
 *
 * @return array Collections complétées.
**/
function svpapi_liste_ezcollection($collections) {

	// Initialisation du tableau des collections
	if (!$collections) {
		$collections = array();
	}

	// Les index désignent les collections, le tableau associé contient les filtres admissibles.
	// -- on cale la durée des caches sur la récurrence de mise à jour du référentiel des plugins.
	include_spip('genie/svp_taches_generales_cron');
	$collections['plugins'] = array(
		'module'    => 'svpapi',
		'ressource' => 'prefixe',
		'cache'     => array(
			'type'  => 'ezrest',
			'duree' => 3600 * _SVP_PERIODE_ACTUALISATION_DEPOTS
		),
		'filtres'   => array(
			array(
				'critere'         => 'compatible_spip',
				'est_obligatoire' => false
			),
		)
	);

	$collections['depots'] = array(
		'module'  => 'svpapi',
		'cache'     => array(
			'type'  => 'ezrest',
			'duree' => 3600 * _SVP_PERIODE_ACTUALISATION_DEPOTS
		),
		'filtres' => array(
			array(
				'critere'         => 'type',
				'est_obligatoire' => false
			)
		)
	);

	return $collections;
}
