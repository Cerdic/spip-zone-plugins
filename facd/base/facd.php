<?php
/**
 * Fichier des pipelines de déclaration de tables
 *
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Déclarer la table spip_facd_conversions dans la liste des tables pour pouvoir :
 * - la créer
 * - la mettre à jour
 * 
 * @param array $tables_principales
 * 	Un tableau de description des tables
 * @param array $tables_principales
 * 	Le tableau complété
 */
function facd_declarer_tables_principales($tables_principales){
	/**
	 * - id_facd_conversion int identifiant numérique
	 * - id_document int identifiant numérique du document à convertir
	 * - id_auteur int identifiant numérique de l'auteur ayant lancé l'encodage
	 * - fonction string fonction d'encodage à utiliser, sinon on regarde dans convertir/extension...
	 * - options string array serializé des options à passer à la fonction  de conversion
	 * - extension string l'extension de sortie souhaitée
	 * - statut string statut de la conversion 'oui','non','en_cours','erreur'
	 * - maj timestamp date de mise à jour de la conversion
	 */
	$spip_facd_conversions = array(
		"id_facd_conversion" => "BIGINT(21) NOT NULL auto_increment",
		"id_document" => "BIGINT(21) NOT NULL DEFAULT '0'",
		"id_auteur" => "BIGINT(21) NOT NULL DEFAULT '0'",
		"fonction" => "text DEFAULT '' NOT NULL",
		"options" => "text DEFAULT '' NOT NULL",
		"extension"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"statut"	=> "VARCHAR(21)", // Peut être oui,non,en_cours
		"infos" => "TEXT DEFAULT '' NOT NULL", // infos serialisées
		"maj" => "TIMESTAMP"
	);
	$spip_facd_conversions_key = array(
		"PRIMARY KEY" => "id_facd_conversion",
		"KEY id_document" => "id_document",
		"KEY statut" => "statut"
	);

	$tables_principales['spip_facd_conversions'] = array(
		'field' => &$spip_facd_conversions,
		'key' => &$spip_facd_conversions_key
	);

	return $tables_principales;
}

/**
 * Déclaration dans la table des tables pour sauvegarde
 * 
 * @param array $interface
 * 	Un tableau de description des tables
 * @return array $interface
 * 	Le table de description des tables complété 
 */
function facd_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['facd_conversions'] = 'facd_conversions';
	$interfaces['table_date']['facd_conversions']='maj';
	$interfaces['exceptions_des_tables']['facd_conversions']['date']='maj';
	return $interfaces;
}

?>