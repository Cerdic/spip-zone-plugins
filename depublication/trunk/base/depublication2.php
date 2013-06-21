<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Dépublication 2
 * @copyright  2013
 * @author     Web
 * @licence    GNU/GPL
 * @package    SPIP\Depublication2\Pipelines
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
function depublication2_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['articles_depublication'] = 'articles_depublication';

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
function depublication2_declarer_tables_objets_sql($tables) {

	$tables['spip_articles_depublication'] = array(
		'type' => 'art_depub',
		'principale' => "oui", 
		'table_objet_surnoms' => array('articlesdepublication', 'art_depub'), // table_objet('art_depub') => 'articles_depublication' 
		'field'=> array(
			"id_art_depub"       => "BIGINT(21) NOT NULL",
			"id_article"         => "bigint(21) NOT NULL DEFAULT 0",
			"depublication"      => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(25) NOT NULL DEFAULT",
			"maj"                => "TIMESTAMP",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_art_depub",
			"KEY statut"         => "statut", 
		),
		'titre' => "'' AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_articles_depublication_liens'),
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
		'texte_changer_statut' => 'art_depub:texte_changer_statut_art_depub', 
		

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
function depublication2_declarer_tables_auxiliaires($tables) {

	$tables['spip_articles_depublication_liens'] = array(
		'field' => array(
			"id_art_depub"       => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_art_depub,id_objet,objet",
			"KEY id_art_depub"   => "id_art_depub"
		)
	);

	return $tables;
}


?>