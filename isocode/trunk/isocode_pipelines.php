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
 * Déclare les collections accessibles via HTTP GET.
 * Par défaut, le plugin propose une liste de collections.
 *
 * @pipeline liste_ezcollection
 *
 * @param array $collections Configuration des collections déjà déclarées.
 *
 * @return array Collections complétées.
**/
function isocode_liste_ezcollection($collections) {

	// Les index désignent les collections, le tableau associé contient les filtres admissibles.
	$collections = array(
		'subdivisions' => array(
			'ressource' => 'prefixe',
			'module'    => 'isocode',
			'filtres'   => array(
				array(
					'critere'         => 'pays',
					'est_obligatoire' => false
				),
			)
		),
		'pays' => array(
			'ressource' => 'code_alpha2',
			'module'    => 'isocode',
			'filtres'   => array(
				array(
					'critere'         => 'region',
					'champ_table'     => 'code_num_region',
					'est_obligatoire' => false
				),
				array(
					'critere'         => 'continent',
					'champ_table'     => 'code_continent',
					'est_obligatoire' => false
				),
			)
		),
		'regions'  => array(
			'module'  => 'isocode',
			'filtres' => array()
		),
		'continents'  => array(
			'module'  => 'isocode',
			'filtres' => array()
		)
	);

	return $collections;
}
