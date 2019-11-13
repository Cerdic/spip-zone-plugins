<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin.
 *
 * @package SPIP\ISOCODE\COLLECTION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclare les collections accessibles via l'API ezREST.
 * Par défaut, le plugin propose une liste de collections.
 *
 * @pipeline liste_ezcollection
 *
 * @param array $collections Configuration des collections déjà déclarées.
 *
 * @return array Collections complétées.
**/
function isocode_liste_ezcollection($collections) {

	// Initialisation du tableau des collections
	if (!$collections) {
		$collections = array();
	}

	// Les index désignent les collections, le tableau associé contient les filtres admissibles.
	$collections['subdivisions'] = array(
		'module'    => 'isocode',
		'cache'     => array(
			'type'  => 'ezrest',
			'duree' => 3600 * 24 * 30
		),
		'filtres'   => array(
			array(
				'critere'         => 'pays',
				'est_obligatoire' => false
			),
		),
		'ressource' => 'prefixe'
	);

	$collections['pays'] = array(
		'module'    => 'isocode',
		'cache'     => array(
			'type'  => 'ezrest',
			'duree' => 3600 * 24 * 30
		),
		'filtres'   => array(
			array(
				'critere'         => 'region',
				'est_obligatoire' => false,
				'champ_nom'       => 'code_num_region',
				'champ_table'     => 'iso3166countries'
			),
			array(
				'critere'         => 'continent',
				'est_obligatoire' => false,
				'champ_nom'       => 'code_continent',
				'champ_table'     => 'iso3166countries'
			),
		),
		'ressource' => 'code_alpha2'
	);

	$collections['zones'] = array(
		'module'  => 'isocode',
		'cache'     => array(
			'type'  => 'spip',
			'duree' => 3600 * 24 * 30
		),
		'filtres' => array()
	);

	$collections['continents'] = array(
		'module'  => 'isocode',
		'cache'     => array(
			'type'  => 'spip',
			'duree' => 3600 * 24 * 30
		),
		'filtres' => array()
	);

	return $collections;
}
