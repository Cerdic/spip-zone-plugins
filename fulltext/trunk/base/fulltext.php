<?php
/**
 * Plugin Fulltext
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function fulltext_declarer_tables_objets_sql($tables) {
	$tables['spip_documents']['field']['contenu'] = "TEXT DEFAULT '' NOT NULL";
	$tables['spip_documents']['field']['extrait'] = "VARCHAR(3) NOT NULL default 'non'";
	$tables['spip_documents']['rechercher_champs']['contenu'] = 1;

	return $tables;
}
