<?php

/**
 * Fonctions de déclarations des tables dans la bdd
 *
 * @package SPIP\Tradrub\Pipelines
 * @license
 *     Licence GPL (c) 2008-2010 Stephane Laurent (Bill), Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajouter id_trad à la table rubriques
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des objets editoriaux
 * @return array
 *     Description des objets editoriaux
 */
function tradrub_declarer_tables_objets_sql($tables){
	// Extension de la table rubriques
	$tables['spip_rubriques']['field']['id_trad'] = "bigint(21) DEFAULT '0' NOT NULL";
	return $tables;
}

?>
