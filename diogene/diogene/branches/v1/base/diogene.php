<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 *
 * Déclaration des tables pour Diogene
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function diogene_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['diogenes']='diogenes';
	$interfaces['table_des_traitements']['DESCRIPTION'][]= _TRAITEMENT_RACCOURCIS;
	return $interfaces;
}

function diogene_declarer_tables_principales($tables_principales){
	$spip_diogenes = array(
			"id_diogene"	=> "bigint(21) NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"objet" => "varchar(25) DEFAULT '' NOT NULL",
			"id_secteur"	=> "text DEFAULT '' NOT NULL",
			"champs_caches"	=> "text DEFAULT '' NOT NULL",
			"champs_ajoutes"	=> "text DEFAULT '' NOT NULL",
			"type" => "varchar(25) DEFAULT '' NOT NULL",
			"description"	=> "text DEFAULT '' NOT NULL",
			"statut_auteur"	=> "text DEFAULT '' NOT NULL",
			"statut_auteur_publier" => "text DEFAULT '' NOT NULL",
			"options_complements"	=> "text DEFAULT '' NOT NULL",
			"menu" => "varchar(3) DEFAULT '' NOT NULL",
			"id_auteur"	=> "bigint DEFAULT '0' NOT NULL");

	$spip_diogenes_key = array(
			"PRIMARY KEY"	=> "id_diogene",
			"KEY id_auteur"	=> "id_auteur");

	$tables_principales['spip_diogenes'] =
		array('field' => &$spip_diogenes,'key' => &$spip_diogenes_key);

	return $tables_principales;
}

/**
 * Declaration des tables auxiliaires
 *
 * @param array $tables_auxiliaires
 * @return array
 */
function diogene_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_diogenes_liens = array(
		"id_diogene"	=> "bigint(21) NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL");

	$spip_diogenes_liens_key = array(
		"PRIMARY KEY"		=> "id_diogene,id_objet,objet");

	$tables_auxiliaires['spip_diogenes_liens'] = array(
	'field' => &$spip_diogenes_liens,
	'key' => &$spip_diogenes_liens_key);

	return $tables_auxiliaires;
}
?>