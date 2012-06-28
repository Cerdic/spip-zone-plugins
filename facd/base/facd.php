<?php
/**
 * FACD
 * File d'Attente de Conversion de Documents
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

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

// Declarer dans la table des tables pour sauvegarde
function facd_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['facd_conversions'] = 'facd_conversions';
	return $interfaces;
}

?>