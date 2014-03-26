<?php

/**
 * Déclarations relatives à la base de données
 * 
 * @package SPIP\Dictionnaires\Pipelines
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Déclarer les interfaces des tables dictionnaires et definitions pour le compilateur
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function dictionnaires_declarer_tables_interfaces($interfaces){
	// 'spip_' dans l'index de $tables_principales
	$interfaces['table_des_tables']['dictionnaires'] = 'dictionnaires';
	$interfaces['table_des_tables']['definitions']   = 'definitions';
	return $interfaces;
}


/**
 * Déclarer les objets éditoriaux des dictionnaires et définitions
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function dictionnaires_declarer_tables_objets_sql($tables) {

	//-- Table dictionnaires
	$tables['spip_dictionnaires'] = array(
		'type' => 'dictionnaire',

		'titre' => "titre, '' AS lang",
		'date' => '',
		'principale' => 'oui',
		
		'field' => array(
			'id_dictionnaire' => 'bigint(21) not null',
			'titre' => 'text not null default ""',
			'descriptif' => 'text not null default ""',
			'type_defaut' => 'varchar(255) not null default ""',
			'statut' => "varchar(10) DEFAULT 'inactif' NOT NULL",
			'maj' => 'timestamp',
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_dictionnaire",
		),
		'join' => array(
			"id_dictionnaire" => "id_dictionnaire"
		),
		'champs_editables' => array(
			'titre', 'descriptif',
			'type_defaut','statut'
		),
		'rechercher_champs' => array(
		  'titre' => 8, 'descriptif' => 5,
		),
		
		'statut' => array(
			array(
				'champ'=>'statut',
				'publie'=>'actif',
				'previsu'=>'actif,inactif',
				'exception'=> array('statut', 'tout')
			)
		),
		'texte_changer_statut' => 'dictionnaire:changer_statut',
		'statut_titres' => array(
			'actif'   => 'dictionnaire:champ_actif_oui',
			'inactif' => 'dictionnaire:champ_actif_non',
		),
		'statut_textes_instituer' => array(
			'actif'   => 'dictionnaire:champ_actif_oui',
			'inactif' => 'dictionnaire:champ_actif_non',
		),
		'statut_images' => array(
			'actif'   => 'puce-publier-8.png',
			'inactif' => 'puce-proposer-8.png',
		),
		
	);


	//-- Table definitions
	$tables['spip_definitions'] = array(
		'type' => 'definition',

		'titre' => "titre, lang",
		'date' => 'date',
		'principale' => 'oui',

		'field' => array(
			'id_definition' => 'bigint(21) not null',
			'id_dictionnaire' => 'bigint(21) not null default 0',
			'titre' => 'text not null default ""',
			'texte' => 'text not null default ""',
			'termes' => 'text not null default ""',
			'type' => 'varchar(255) not null default ""',
			'casse' => 'tinyint(1) not null default 0',
			'statut' => 'varchar(255) not null default "prop"',
			'lang' => 'varchar(10) not null default ""',
			'date' => 'datetime default "0000-00-00 00:00:00" not null',
			'maj' => 'timestamp',
		),
		'key' => array(
			"PRIMARY KEY"         => "id_definition",
			"KEY id_dictionnaire" => "id_dictionnaire",
		),
		'join' => array(
			"id_definition"   => "id_definition",
			"id_dictionnaire" => "id_dictionnaire"
		),
		'tables_jointures' => array('definitions_liens'),
		'champs_editables' => array(
			'id_dictionnaire',
			'titre', 'texte', 'termes',
			'type', 'casse', 'statut',
			'lang', 'date',
		),
		'rechercher_champs' => array(
		  'titre' => 8, 'texte' => 4, 'termes' => 6,
		),
		
		'statut' => array(
			array(
				'champ'=>'statut',
				'publie'=>'publie',
				'previsu'=>'prepa,publie',
				'exception'=> array('statut', 'tout')
			)
		),
		'texte_changer_statut' => 'definition:changer_statut',
		'statut_titres' => array(
			'prop'=>'info_article_propose',
			'publie'=>'info_article_publie',
			'poubelle'=>'info_article_supprime'
		),
		'statut_textes_instituer' => array(
			'prop' => 'texte_statut_propose_evaluation',
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_poubelle',
		),
		'champs_versionnes' => array(
			'titre', 'texte', 'termes',
		),
	);

	return $tables;
}


/**
 * Déclarer les tables auxiliaires des definitions
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables_auxiliaires
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function dictionnaires_declarer_tables_auxiliaires($tables_auxiliaires){
	//-- Table de relations definitions_liens
	$spip_definitions_liens = array(
		'id_definition' => 'bigint(21) not null default 0',
		'objet' => 'varchar(255) not null default ""',
		'id_objet' => 'bigint(21) not null default 0',
	);
	
	$spip_definitions_liens_cles = array(
		'PRIMARY KEY' => 'id_definition, objet, id_objet',
		'KEY id_definition' => 'id_definition'
	);
	
	$tables_auxiliaires['spip_definitions_liens'] = array(
		'field' => &$spip_definitions_liens,
		'key' => &$spip_definitions_liens_cles
	);
	
	return $tables_auxiliaires;
}

?>
