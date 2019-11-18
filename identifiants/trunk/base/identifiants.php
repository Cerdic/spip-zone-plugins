<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Identifiants
 * @copyright  2015
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des tables des objets éditoriaux
 *
 * Ajout de la déclaration de la colonne `identifiant` sur les objets configurés.
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function identifiants_declarer_tables_objets_sql($tables) {

	include_spip('identifiants_fonctions'); // Aukazou
	$tables_identifiables = identifiants_lister_tables_identifiables(true);
	foreach ($tables_identifiables as $table) {
		// if (!isset($tables[$table]['field']['identifiant'])) { // Allez, on ne sait jamais
			$tables[$table]['field']['identifiant'] = "varchar(255) NOT NULL DEFAULT ''";
			$tables[$table]['key']['KEY identifiant'] = 'identifiant';
			$tables[$table]['champs_editables'][] = 'identifiant';
			$tables[$table]['champs_versionnes'][] = 'identifiant';
			$tables[$table]['rechercher_champs']['identifiant'] = 5;
		// }
	}

	return $tables;
}
