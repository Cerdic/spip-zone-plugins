<?php
/**
 * Plugin Torrent pour Spip
 * Licence GPL (c) 2008
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/** 
 * Déclarer les jointures entre tables
 *
 * Chaque fichier torrent est un  document avec des propriétés complémentaires
 *
 */
function torrent_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_documents'][] = 'torrents';
	
	//-- Table des tables ----------------------------------------------------
	
	$interface['table_des_tables']['torrents']='torrents';

	return $interface;
}

/**
 * Table principale
 * 
 * Reprise des denominations de phpBTTrackerMod
 * 
 */
function torrent_declarer_tables_principales($tables_principales){
	$spip_torrents = array(
	    "id_document" => "bigint(21) NOT NULL",
		"info_hash" 	=> "char(40) DEFAULT '' NOT NULL",
		"filename" 	=> "varchar(255) DEFAULT '' NOT NULL",
		"seeds" 	=> "int UNSIGNED DEFAULT 0 NOT NULL",
		"leechers" 	=> "int UNSIGNED DEFAULT 0 NOT NULL",
		"finished" 	=> "int UNSIGNED DEFAULT 0 NOT NULL");
	
	$spip_torrents_key = array(
		"PRIMARY KEY" => "info_hash");
	
	$tables_principales['spip_torrents'] = array(
		'field' => &$spip_torrents,
		'key' => &$spip_torrents_key);
		
	return $tables_principales;
}

/**
 * Tables de jointures n-m
 */
function torrent_declarer_tables_auxiliaires($tables_auxiliaires){
    return $tables_auxiliaires;
}
