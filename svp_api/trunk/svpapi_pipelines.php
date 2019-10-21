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
	$collections['plugins'] = array(
		'ressource' => 'prefixe',
		'module'    => 'svpapi',
		'filtres'   => array(
			array(
				'critere'         => 'compatible_spip',
				'est_obligatoire' => false
			),
		)
	);

	$collections['depots'] = array(
		'module'  => 'svpapi',
		'filtres' => array(
			array(
				'critere'         => 'type',
				'est_obligatoire' => false
			)
		)
	);

	return $collections;
}
