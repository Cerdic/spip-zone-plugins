<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin Rezosocios
 * @license GPL (c) 2014
 * @author kent1
 *
 * @package SPIP\Rezosocios\Pipelines
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer les interfaces des tables
 *
 * @pipeline declarer_tables_interfaces
 * 
 * @param array $interface
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function rezosocios_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['rezosocios'] = 'rezosocios';
	$interface['table_des_traitements']['TYPE_REZO'][] = 'rezosocios_nom(%s)';
	return $interface;
}


/**
 * Déclarer les objets éditoriaux 
 *
 * @pipeline declarer_tables_objets_sql
 * 
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function rezosocios_declarer_tables_objets_sql($tables){
	//-- Table annuaires ----------------------------------------
	$tables['spip_rezosocios'] = array(
		// Caractéristiques
		'principale' => 'oui',
		// Les champs et leurs particularités (clés etc)
		'field'=> array(
			'id_rezosocio' 		=> "bigint(21) NOT NULL auto_increment",
			'titre' 			=> "text DEFAULT '' NOT NULL",
			'url_site'		=> 'varchar(255) not null default ""',
			'nom_compte'		=> 'varchar(255) not null default ""',
			'type_rezo'		=> "TEXT DEFAULT '' NOT NULL",
			"lang"		=> "VARCHAR(10) DEFAULT '' NOT NULL",
			"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"maj"	=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"		=> "id_rezosocio",
			"KEY lang"		=> "lang"
		),
		'join' => array(
			"id_rezosocio" 	=> "id_rezosocio",
		),
		'champs_editables' => array('titre', 'url_site', 'nom_compte', 'type_rezo','lang'),
		'champs_versionnes' => array('titre', 'url_site', 'nom_compte', 'type_rezo','lang'),
		'rechercher_champs' => array(
			'titre' => 8, 'url_site' => 8, 'nom_compte' => 8,'type_rezo' => 4,
		),
		// Chaînes de langue explicite
		'texte_objets' => 'rezosocios:rezosocios',
		'texte_objet' => 'rezosocios:rezosocio',
		'texte_modifier' => 'rezosocios:rezosocio_editer',
		'texte_creer' => 'rezosocios:rezosocio_creer',
		'texte_creer_associer' => 'rezosocios:rezosocio_creer_associer',
		'texte_ajouter' => 'rezosocios:rezosocio_ajouter',
		'texte_logo_objet' => 'rezosocios:rezosocio_logo',
		'info_aucun_objet'=> 'rezosocios:rezosocio_aucun',
		'info_1_objet' => 'rezosocios:rezosocio_un',
		'info_nb_objets' => 'rezosocios:rezosocios_nb',
	);

	//-- Jointures ----------------------------------------------------
	$tables[]['tables_jointures'][]= 'rezosocios_liens';
	$tables[]['champs_versionnes'][] = 'jointure_rezosocios';

	return $tables;
}

/**
 * Déclarer les tables auxiliaires
 *
 * @pipeline declarer_tables_auxiliaires
 * 
 * @param array $tables_auxiliaires
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function rezosocios_declarer_tables_auxiliaires($tables_auxiliaires){
	//-- Table organisations_liens -------------------------------------
	$rezosocios_liens = array(
		"id_rezosocio" => "BIGINT(21) NOT NULL",
		"id_objet"        => "BIGINT(21) NOT NULL",
		"objet"           => "VARCHAR(25) NOT NULL",
		"type_liaison"    => "VARCHAR(25) NOT NULL DEFAULT ''",
	);
	$rezosocios_liens_key = array(
		"PRIMARY KEY"         => "id_rezosocio, id_objet, objet, type_liaison",
		"KEY id_rezosocio" => "id_rezosocio",
		"KEY id_objet"        => "id_objet",
		"KEY objet"           => "objet"
	);
	$tables_auxiliaires['spip_rezosocios_liens'] =
		array('field' => &$rezosocios_liens, 'key' => &$rezosocios_liens_key);

	return $tables_auxiliaires;
}

?>
