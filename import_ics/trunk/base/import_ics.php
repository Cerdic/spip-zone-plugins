<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Import_ics
 * @copyright  2013
 * @author     Amaury
 * @licence    GNU/GPL
 * @package    SPIP\Import_ics\Pipelines
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
function import_ics_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['almanachs'] = 'almanachs';

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
function import_ics_declarer_tables_objets_sql($tables) {

	$tables['spip_almanachs'] = array(
		'type' => 'almanach',
		'principale' => "oui",
		'field'=> array(
			"id_almanach"        => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL DEFAULT ''",
			"url"                => "text NOT NULL DEFAULT ''",
			"id_article"         => "bigint(21) NOT NULL DEFAULT 0",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_almanach",
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'url', 'id_article'),
		'champs_versionnes' => array('titre', 'url', 'id_article'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_almanachs_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
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
		'texte_changer_statut' => 'almanach:texte_changer_statut_almanach', 
		

	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function import_ics_declarer_tables_auxiliaires($tables) {

	$tables['spip_almanachs_liens'] = array(
		'field' => array(
			"id_almanach"        => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_almanach,id_objet,objet",
			"KEY id_almanach"    => "id_almanach"
		)
	);

	return $tables;
}


?>