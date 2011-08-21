<?php
/**
 * Plugin tradrub
 * Licence GPL (c) 2008-2010 Stephane Laurent (Bill), Matthieu Marcillaud
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajouter id_trad a la table rubriques
 * @param array $tables description des objets editoriaux
 * @return array
 */
function tradrub_declarer_tables_objets_sql($tables){
	// Extension de la table rubriques
	$tables['spip_rubriques']['field']['id_trad'] = "bigint(21) DEFAULT '0' NOT NULL";
	return $tables;
}

?>
