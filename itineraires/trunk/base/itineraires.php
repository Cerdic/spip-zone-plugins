<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Itinéraires
 * @copyright  2013
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Itineraires\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function itineraires_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['itineraires'] = 'itineraires';
	$interfaces['table_des_tables']['itineraires_etapes'] = 'itineraires_etapes';
	$interfaces['table_des_tables']['itineraires_locomotions'] = 'itineraires_locomotions';
	$interfaces['table_des_traitements']['LONGUEUR']['itineraires'] = 'floatval(%s)';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function itineraires_declarer_tables_objets_sql($tables) {
	$tables['spip_itineraires'] = array(
		'type' => 'itineraire',
		'principale' => "oui",
		'field'=> array(
			"id_itineraire"      => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL DEFAULT ''",
			"texte"              => "text NOT NULL DEFAULT ''",
			"longueur"           => "float(9,3) not null default 0",
			"denivele"           => "int(11) NOT NULL DEFAULT 0",
			"difficulte"         => "int(11) NOT NULL DEFAULT 0",
			"depart"             => "text NOT NULL DEFAULT ''",
			"balisage"           => "text NOT NULL DEFAULT ''",
			"boucle"             => "int(1) NOT NULL DEFAULT 0",
			"transport"          => "int(1) NOT NULL DEFAULT 0",
			"handicap"           => "int(1) NOT NULL DEFAULT 0",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_itineraire",
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'texte', 'longueur', 'denivele', 'difficulte', 'depart', 'balisage', 'boucle', 'transport', 'handicap'),
		'champs_versionnes' => array('titre', 'texte', 'longueur', 'denivele', 'difficulte', 'depart', 'balisage', 'boucle', 'transport', 'handicap'),
		'rechercher_champs' => array("titre" => 8, "texte" => 5),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'itineraire:texte_changer_statut_itineraire', 
	);
	
	$tables['spip_itineraires_etapes'] = array(
		'type' => 'itineraires_etape',
		'table_objet_surnoms' => array('itinerairesetape'),
		'type_surnoms' => array('itinerairesetape'),
		'principale' => "oui",
		'field'=> array(
			'id_itineraires_etape' => 'bigint(21) NOT NULL',
			'id_itineraire'        => 'bigint(21) NOT NULL default 0',
			'titre'                => 'text NOT NULL DEFAULT ""',
			'texte'                => 'text NOT NULL DEFAULT ""',
			'rang'                 => 'int not null default 0',
			'maj'                  => 'TIMESTAMP',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_itineraires_etape',
		),
		'titre' => "titre AS titre, '' AS lang",
		'champs_editables'  => array('id_itineraire', 'rang', 'titre', 'texte',),
		'champs_versionnes' => array('titre', 'texte',),
		'rechercher_champs' => array('titre' => 8, 'texte' => 5),
		'tables_jointures'  => array(),
	);

	return $tables;
}

/**
 * Déclarer les tables auxiliaires des itinéraires
 *
 * @pipeline declarer_tables_auxiliaires
 * 
 * @param array $tables_auxiliaires
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function itineraires_declarer_tables_auxiliaires($tables_auxiliaires){
	// Choix des infos de locomotions pour un itinéraire
	$tables_auxiliaires['spip_itineraires_locomotions'] = array(
		'field' => array(
			"id_itineraire"       => "bigint(21) not null",
			"type_locomotion"     => "varchar(25) not null default ''",
			"duree"			  	  => "int(11) not null default 0",
		),
		'key' => array(
			"PRIMARY KEY"          => "id_itineraire, type_locomotion",
			"KEY id_itineraire"    => "id_itineraire",
			"KEY type_locomotion"  => "type_locomotion"
		),
	);
	
	/* On verra ça plus tard
	// Liaison entre les étapes et des contenus
	$tables['spip_itineraires_etapes_liens'] = array(
		'field' => array(
			'id_itineraires_etape' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'             => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'                => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                   => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'              => 'id_itineraires_etape,id_objet,objet',
			'KEY id_itineraires_etape' => 'id_itineraires_etape',
		)
	);*/
	
	return $tables_auxiliaires;
}
