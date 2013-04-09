<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 * Définition des tables
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline declarer_tables_interfaces (SPIP)
 * 
 * Déclarer les interfaces : on ajoute les champs de la table spip_documents
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function getid3_declarer_tables_principales($tables_principales){

	$tables_principales['spip_documents']['field']['duree'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['bitrate'] = "INTEGER NOT NULL";
	$tables_principales['spip_documents']['field']['hasvideo'] = "VARCHAR(3) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['hasaudio'] = "VARCHAR(3) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['framecount'] = "INTEGER";
	$tables_principales['spip_documents']['field']['framerate'] = "FLOAT";
	$tables_principales['spip_documents']['field']['videobitrate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['videocodec'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['videocodecid'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiobitrate'] = "INTEGER NOT NULL";
	$tables_principales['spip_documents']['field']['audiobitratemode'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiosamplerate'] = "INTEGER NOT NULL";
	$tables_principales['spip_documents']['field']['audiochannels'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiocodec'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiocodecid'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['encodeur'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['bits'] = "INTEGER NOT NULL";
	

	return $tables_principales;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * 
 * On ajoute nos champs dans les champs editables de la table spip_documents
 * 
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function getid3_declarer_tables_objets_sql($tables){
	$tables['spip_documents']['champs_editables'][] = 'duree';
	$tables['spip_documents']['champs_editables'][] = 'bitrate';
	$tables['spip_documents']['champs_editables'][] = 'hasvideo';
	$tables['spip_documents']['champs_editables'][] = 'hasaudio';
	$tables['spip_documents']['champs_editables'][] = 'framecount';
	$tables['spip_documents']['champs_editables'][] = 'framerate';
	$tables['spip_documents']['champs_editables'][] = 'videobitrate';
	$tables['spip_documents']['champs_editables'][] = 'videocodec';
	$tables['spip_documents']['champs_editables'][] = 'videocodecid';
	$tables['spip_documents']['champs_editables'][] = 'audiobitrate';
	$tables['spip_documents']['champs_editables'][] = 'audiobitratemode';
	$tables['spip_documents']['champs_editables'][] = 'audiosamplerate';
	$tables['spip_documents']['champs_editables'][] = 'audiocodec';
	$tables['spip_documents']['champs_editables'][] = 'audiocodecid';
	$tables['spip_documents']['champs_editables'][] = 'audiochannels';
	$tables['spip_documents']['champs_editables'][] = 'encodeur';
	$tables['spip_documents']['champs_editables'][] = 'bits';
	
	return $tables;
}
?>