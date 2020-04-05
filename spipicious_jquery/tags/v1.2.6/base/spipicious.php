<?php
/**
 * SPIP.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational (http://www.erational.org)
 *
 * © 2007-2013 - Distribue sous licence GNU/GPL
 *
 * Déclarations relatives à la base de données
 * 
 * @package SPIP\SPIPicious\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer les interfaces de la table spip_spipicious pour le compilateur
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function spipicious_declarer_tables_interfaces($interface){
	/**
	 * Une jointure sur chaque table pour faciliter
	 */
	$interface['tables_jointures'][] = 'spipicious';

	$interface['table_des_tables']['spipicious']='spipicious';

	return $interface;
}

/**
 * Déclarer la tables principale de spipicious
 *
 * @pipeline declarer_tables_principales
 * @param array $tables_principales
 *     Description des tables
 * @return array $tables_principales
 *     Description complétée des tables
 */
function spipicious_declarer_tables_principales($tables_principales){
	$spip_spipicious = array(
	  	"id_mot"	=> "bigint(21) NOT NULL",
	  	"id_auteur"	=> "bigint(21) NOT NULL",
		"id_objet"	=> "bigint(21) NOT NULL",
	  	"objet"		=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"position"	=> "int(10) NOT NULL",
		"statut"	=> "varchar(10) DEFAULT 'publie' NOT NULL",
		"maj"		=> "TIMESTAMP");

	$spip_spipicious_key = array(
		"PRIMARY KEY"	=> "id_mot, id_auteur, objet, id_objet",
		"KEY id_mot" => "id_mot",
		"KEY id_auteur" => "id_auteur",
		"KEY objet" => "objet",
		"KEY id_objet" => "id_objet");

	$tables_principales['spip_spipicious'] = array(
		'field' => &$spip_spipicious,
		'key' => &$spip_spipicious_key);

	return $tables_principales;
}
?>
