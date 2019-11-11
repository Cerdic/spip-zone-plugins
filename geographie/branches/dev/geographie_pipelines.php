<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Utilisation du pipeline afficher_complement_objet
 *
 * @pipeline afficher_complement_objet
 *
 * @param array $flux
 *     Données du pipeline
 *
 * @return array
 *     Données du pipeline
 */
function geographie_afficher_complement_objet($flux) {
	include_spip('inc/config');
	$table_sql = table_objet_sql($flux['args']['type']);

	// Ajouter un bloc de liaison avec les trucs géo sur les objets configurés pour ça
	foreach (array('pays', 'regions', 'departements', 'arrondissements', 'communes') as $source) {
		if (in_array($table_sql, lire_config("geographie/$source/lier_objets", array()))) {
			$infos = recuperer_fond('prive/objets/editer/liens', array(
				'table_source' => 'geo_' . $source,
				'objet' => $flux['args']['type'],
				'id_objet' => intval($flux['args']['id']),
			));

			$flux['data'] .= $infos;
		}
	}

	return $flux;
}
