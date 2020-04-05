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

?>