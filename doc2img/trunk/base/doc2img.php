<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline declarer_tables_principales
 * Ajoute un champs page sur les documents
 *
 * @param array $tables_principales Tableau de description des tables
 * @return array $tables_principales Tableau de description des tables complété
 */
function doc2img_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['page'] = "bigint DEFAULT '0' NOT NULL";
	return $tables_principales;

}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * On ajoute notre champs dans les champs editables de la table spip_documents
 * 
 * @param array $tables : le tableau des tables des objets déclarés
 * @return array $tables : le tableau des tables complété 
 */
function doc2img_declarer_tables_objets_sql($tables){
	$tables['spip_documents']['champs_editables'][] = 'em_type';
	
	return $tables;
}
?>