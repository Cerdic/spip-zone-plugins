<?php
/**
* Plugin Podcast
* par kent1
*
* Copyright (c) 2010
* Logiciel libre distribué sous licence GNU/GPL.
*
* Définition des tables
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function podcast_declarer_tables_principales($tables_principales){

	$tables_principales['spip_documents']['field']['explicit'] = "VARCHAR(5) DEFAULT 'clean' NOT NULL";
	$tables_principales['spip_documents']['field']['podcast'] = "VARCHAR(3) DEFAULT 'oui' NOT NULL";

	return $tables_principales;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * On ajoute nos champs dans les champs editables de la table spip_documents
 */
function podcast_declarer_tables_objets_sql($tables){
	$tables['spip_documents']['champs_editables'][] = 'explicit';
	$tables['spip_documents']['champs_editables'][] = 'podcast';
	
	return $tables;
}

?>