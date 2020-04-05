<?php
/**
 * fonctions utiles à ce squelette
 *
 * On vérifie que les objets éventuellement déclarés dans le pipeline
 * identifiants_utiles sont bien activés
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     Tcharlss
 * @licence    GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Retourne les tables des objets suggérés non activés
 *
 * @return array
 */
function identifiants_objets_manquants() {

	include_spip('inc/config');
	$tables_objets_utiles_manquants = array();
	$tables_objets = lire_config('identifiants/objets', array());

	if (
		$identifiants_utiles = pipeline('identifiants_utiles')
		and is_array($identifiants_utiles)
		and $tables_objets_utiles = array_map('table_objet_sql', array_keys($identifiants_utiles))
	) {
		$tables_objets_utiles_manquants = array_diff($tables_objets_utiles, $tables_objets);
	}

	return $tables_objets_utiles_manquants;
}
