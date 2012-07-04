<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function spipmotion_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['duree'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['hasvideo'] = "VARCHAR(3) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['hasaudio'] = "VARCHAR(3) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['framecount'] = "INTEGER";
	$tables_principales['spip_documents']['field']['framerate'] = "FLOAT";
	$tables_principales['spip_documents']['field']['pixelformat'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['bitrate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['videobitrate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['videocodec'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['videocodecid'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiobitrate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['audiosamplerate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['audiocodec'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiocodecid'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiochannels'] = "INTEGER";
	$tables_principales['spip_documents']['field']['rotation'] = "INTEGER";
	$tables_principales['spip_documents']['field']['metadatas'] = "TEXT DEFAULT '' NOT NULL";

	return $tables_principales;
}

// Declarer dans la table des tables pour sauvegarde
function spipmotion_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['spipmotion_metas'] = 'spipmotion_metas';
	return $interfaces;
}

/**
 * 
 */
function spipmotion_declarer_tables_auxiliaires($tables_auxiliaires){	
	$spip_spipmotion_metas = array(
		"nom" => "VARCHAR (255) NOT NULL",
		"valeur" => "text DEFAULT ''",
		"impt"  => "VARCHAR(3) DEFAULT 'oui' NOT NULL",
		"maj"   => "TIMESTAMP");

	$spip_spipmotion_metas_key = array(
		"PRIMARY KEY"   => "nom");

	$tables_auxiliaires['spip_spipmotion_metas'] = array(
		'field' => &$spip_spipmotion_metas, 
		'key' => &$spip_spipmotion_metas_key
	);
	return $tables_auxiliaires;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * On ajoute nos champs dans les champs editables de la table spip_documents
 */
function spipmotion_declarer_tables_objets_sql($tables){
	$tables['spip_documents']['champs_editables'][] = 'duree';
	$tables['spip_documents']['champs_editables'][] = 'hasvideo';
	$tables['spip_documents']['champs_editables'][] = 'framecount';
	$tables['spip_documents']['champs_editables'][] = 'framerate';
	$tables['spip_documents']['champs_editables'][] = 'pixelformat';
	$tables['spip_documents']['champs_editables'][] = 'bitrate';
	$tables['spip_documents']['champs_editables'][] = 'videobitrate';
	$tables['spip_documents']['champs_editables'][] = 'videocodec';
	$tables['spip_documents']['champs_editables'][] = 'videocodecid';
	$tables['spip_documents']['champs_editables'][] = 'hasaudio';
	$tables['spip_documents']['champs_editables'][] = 'audiobitrate';
	$tables['spip_documents']['champs_editables'][] = 'audiocodec';
	$tables['spip_documents']['champs_editables'][] = 'audiocodecid';
	$tables['spip_documents']['champs_editables'][] = 'audiochannels';
	$tables['spip_documents']['champs_editables'][] = 'rotation';
	$tables['spip_documents']['champs_editables'][] = 'metadatas';
	
	return $tables;
}
?>