<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1), BoOz
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 * Définition des tables
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function getid3_declarer_tables_principales($tables_principales){

	$tables_principales['spip_documents']['field']['duree'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['bitrate'] = "INTEGER NOT NULL";
	$tables_principales['spip_documents']['field']['bitrate_mode'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiosamplerate'] = "INTEGER NOT NULL";
	$tables_principales['spip_documents']['field']['encodeur'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['bits'] = "INTEGER NOT NULL";
	$tables_principales['spip_documents']['field']['canaux'] = "text DEFAULT '' NOT NULL";

	return $tables_principales;
}

?>