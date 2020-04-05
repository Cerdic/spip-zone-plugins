<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin Rubriques virtuelles
 * @license GPL (c) 2016
 * @author kent1
 *
 * @package SPIP\Rubriques_virtuelles\Pipelines
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Modifier la déclaration de la table spip_rubriques
 * On lui ajoute un champ "virtuel" qui est éditable, versionné, présent dans la recherche
 *
 * @pipeline declarer_tables_objets_sql
 *
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function rubriques_virtuelles_declarer_tables_objets_sql($tables) {
	$tables['spip_rubriques']['field']['virtuel'] = "text DEFAULT '' NOT NULL";
	$tables['spip_rubriques']['champs_editables'][] = 'virtuel';
	$tables['spip_rubriques']['champs_versionnes'][] = 'virtuel';
	$tables['spip_rubriques']['rechercher_champs']['virtuel'] = 3;
	return $tables;
}
