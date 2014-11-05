<?php
/**
 * Plugin Fulltext
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function fulltext_declarer_tables_principales($tables_principales) {
	$tables_principales['spip_documents']['field']['contenu'] = "TEXT DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['extrait'] = "VARCHAR(3) NOT NULL default 'non'";

	return $tables_principales;
}

?>