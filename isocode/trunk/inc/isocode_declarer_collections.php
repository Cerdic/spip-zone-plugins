<?php
/**
 * Ce fichier contient la fonction de déclaration des configurations de collections suivant l'api utilisée.
 * Elle appelle le pipeline `declarer_collections_isocode` pour les plugins qui le souhaitent.
 *
 * @package SPIP\SVPAPI\CONFIGURATION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclare les collections accessibles via HTTP GET.
 * Par défaut, le plugin propose une liste de collections pour chaque api.
 *
 * @pipeline declarer_collections_svp
 *
 * @return array
 * 		   Description des collections.
**/
function inc_isocode_declarer_collections_dist($api) {

	// Initialisation en static pour les performances du tableau de toutes les collections
	static $collections = array();

	if (empty($collections)) {
		// Les index désignent les collections, le tableau associé contient les filtres admissibles.
		$collections['geographie'] = array(
			'subdivisions' => array(
				'ressource' => 'prefixe',
				'module'    => 'geographie',
				'filtres'   => array(
					array(
						'critere'         => 'pays',
						'est_obligatoire' => false
					),
				)
			),
			'pays' => array(
				'ressource' => 'code_alpha2',
				'module'    => 'geographie',
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
				'module'  => 'geographie',
				'filtres' => array()
			),
			'continents'  => array(
				'module'  => 'geographie',
				'filtres' => array()
			)
		);

		// On complète par des collections fournies par d'autres plugin
		$collections = pipeline('declarer_collections_isocode', $collections);
	}

	// On retourne les collections pour l'api demandée
	// Initialisation de la liste des collections pour l'api demandée.
	$collections_api = isset($collections[$api]) ? $collections[$api] : array();

	return $collections_api;
}
