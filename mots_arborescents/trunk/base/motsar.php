<?php
/**
 * Fichier déclarant les champs de tables
 *
 * @plugin     Mots arborescents
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Motsar\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajouter les colonnes nécessaire à notre plugin sur les groupes et les mots
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function motsar_declarer_tables_objets_sql($tables){

	$tables['spip_mots']['field']["id_parent"]     = "bigint(21) DEFAULT 0 NOT NULL";
	$tables['spip_mots']['field']["id_mot_racine"] = "bigint(21) DEFAULT 0 NOT NULL"; // equivalent id_secteur des rubriques
	$tables['spip_mots']['field']["profondeur"]    = "smallint(5) DEFAULT '0' NOT NULL";

	// configuration pour activer l'arborescence de mots sur un groupe donné
	$tables['spip_groupes_mots']['field']["mots_arborescents"] = "varchar(3) NOT NULL DEFAULT ''";

	return $tables;
}

/**
 * Ajouter les Alias des tables HIERARCHIE_MOTS 
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interface
 * 		Description des interfaces pour le compilateur
 * @return
 * 		Description complétée des interfaces
**/
function motsar_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['hierarchie_mots'] = 'mots';
	return $interfaces;
}
