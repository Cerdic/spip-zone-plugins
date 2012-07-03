<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 * Définition des tables
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

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

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * On ajoute nos champs dans les champs editables de la table spip_documents
 */
function getid3_declarer_tables_objets_sql($tables){
	$tables['spip_documents']['champs_editables'][] = 'duree';
	$tables['spip_documents']['champs_editables'][] = 'bitrate';
	$tables['spip_documents']['champs_editables'][] = 'bitrate_mode';
	$tables['spip_documents']['champs_editables'][] = 'audiosamplerate';
	$tables['spip_documents']['champs_editables'][] = 'encodeur';
	$tables['spip_documents']['champs_editables'][] = 'bits';
	$tables['spip_documents']['champs_editables'][] = 'canaux';
	
	return $tables;
}
?>