<?php
/*
 * Plugin Licence
 * (c) 2007-2012 fanouch
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function licence_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['id_licence'] = "bigint(21) NOT NULL";
	$tables_principales['spip_articles']['field']['id_licence'] = "bigint(21) NOT NULL";
	return $tables_principales;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * On ajoute nos champs dans les champs editables de la table spip_documents
 */
function licence_declarer_tables_objets_sql($tables){
	$tables['spip_articles']['champs_editables'][] = 'id_licence';
	$tables['spip_articles']['champs_versionnes'][] = 'id_licence';
	$tables['spip_documents']['champs_editables'][] = 'id_licence';
	$tables['spip_documents']['champs_versionnes'][] = 'id_licence';
	
	return $tables;
}
?>