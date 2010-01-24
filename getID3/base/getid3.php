<?php
/**
* Plugin getid3
* par BoOz, kent1
*
* Copyright (c) 2007-2010
* Logiciel libre distribué sous licence GNU/GPL.
*
* Définition des tables
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function getid3_declarer_tables_principales($tables_principales){

	$tables_principales['spip_documents']['field']['duree'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['bitrate'] = "INTEGER DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['bitrate_mode'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiosamplerate'] = "INTEGER DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['encodeur'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['bits'] = "INTEGER DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['canaux'] = "text DEFAULT '' NOT NULL";

	return $tables_principales;
}

?>