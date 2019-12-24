<?php
/**
 * Plugin Groupes arborescents de mots clés
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Ajouter les colonnes nécessaire à notre plugin sur
 * les groupes et les mots
 *
 * @param array $tables
 * 		Description des tables
 * @return array
 * 		Description complétée des tables
 */
function gma_declarer_tables_objets_sql($tables){
	$tables['spip_mots']['field']["id_groupe_racine"] = "bigint(21) DEFAULT 0 NOT NULL";
	$tables['spip_groupes_mots']['field']["id_groupe_racine"] = "bigint(21) DEFAULT 0 NOT NULL";
	$tables['spip_groupes_mots']['field']["id_parent"]= "bigint(21) DEFAULT 0 NOT NULL";
	return $tables;
}

/**
 * Ajouter les Alias des tables HIERARCHIE_GROUPES_MOTS 
 *
 * @param array $interface
 * 		Description des interfaces pour le compilateur
 * @return
 * 		Description complétée des interfaces
**/
function gma_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['hierarchie_groupes_mots']   = 'groupes_mots';
	return $interfaces;
}