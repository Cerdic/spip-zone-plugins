<?php
/*
 * Plugin Licence
 * (c) 2007-2010 fanouch
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function licence_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['id_licence'] = "bigint(21) NOT NULL";
	$tables_principales['spip_articles']['field']['id_licence'] = "bigint(21) NOT NULL";
	return $tables_principales;
}

?>