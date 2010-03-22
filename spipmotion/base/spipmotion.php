<?php
/**
* Plugin SPIPmotion
* par kent1 (http://kent1.sklunk.net)
*
* Copyright (c) 2007-2009
* Logiciel libre distribué sous licence GNU/GPL.
*
* Définition des tables
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function spipmotion_declarer_tables_principales($tables_principales){
	$spip_spipmotion_attentes = array(
		"id_spipmotion_attente" => "BIGINT(21) NOT NULL auto_increment",
		"id_document" => "BIGINT(21) NOT NULL DEFAULT '0'",
		"id_objet" => "BIGINT(21) NOT NULL DEFAULT '0'",
		"objet" => "VARCHAR(25)",
		"id_auteur" => "BIGINT(21) NOT NULL DEFAULT '0'",
		"extension"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"encode"	=> "VARCHAR(21)", // Peut être oui,non,en_cours
		"maj" => "TIMESTAMP"
	);
	$spip_spipmotion_attentes_key = array(
		"PRIMARY KEY" => "id_spipmotion_attente",
		"KEY id_document" => "id_document",
		"KEY id_objet" => "id_objet",
		"KEY encode" => "encode"
	);

	$tables_principales['spip_spipmotion_attentes'] = array(
		'field' => &$spip_spipmotion_attentes,
		'key' => &$spip_spipmotion_attentes_key
	);

	$tables_principales['spip_documents']['field']['duree'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['hasvideo'] = "VARCHAR(3) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['framecount'] = "INTEGER";
	$tables_principales['spip_documents']['field']['framerate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['pixelformat'] = "INTEGER";
	$tables_principales['spip_documents']['field']['bitrate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['videobitrate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['videocodec'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['hasaudio'] = "VARCHAR(3) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiobitrate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['audiosamplerate'] = "INTEGER";
	$tables_principales['spip_documents']['field']['audiocodec'] = "VARCHAR(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_documents']['field']['audiochannels'] = "INTEGER";
	$tables_principales['spip_documents']['field']['id_orig'] = "BIGINT(21) NOT NULL";

	return $tables_principales;
}

// Declarer dans la table des tables pour sauvegarde
function spipmotion_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['spipmotion_attentes'] = 'spipmotion_attentes';
	return $interfaces;
}

?>