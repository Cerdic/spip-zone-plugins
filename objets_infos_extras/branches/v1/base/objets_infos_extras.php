<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Infos extras pour objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Objets_infos_extras\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function objets_infos_extras_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['objets_informations'] = 'objets_informations';

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
function objets_infos_extras_declarer_tables_objets_sql($tables) {

	$tables['spip_objets_informations'] = array(
		'type' => 'objets_information',
		'principale' => 'oui',
		'table_objet_surnoms' => array('objetsinformation'), // table_objet('objets_information') => 'objets_informations'
		'field'=> array(
			'id_objets_information' => 'bigint(21) NOT NULL',
			'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
			'descriptif'         => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_objets_information',
			'KEY statut'         => 'statut',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'descriptif'),
		'champs_versionnes' => array('titre', 'descriptif'),
		'rechercher_champs' => array("titre" => 8, "descriptif" => 5),
		'tables_jointures'  => array('spip_objets_informations_liens'),
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
		'texte_changer_statut' => 'objets_information:texte_changer_statut_objets_information',


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
function objets_infos_extras_declarer_tables_auxiliaires($tables) {

	$tables['spip_objets_informations_liens'] = array(
		'field' => array(
			'id_objets_information' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'quantite'           => 'int(11) NOT NULL DEFAULT "0"',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
			'rang_lien'          => 'int(4) NOT NULL DEFAULT "0"',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_objets_information,id_objet,objet',
			'KEY id_objets_information' => 'id_objets_information',
		)
	);
	return $tables;
}
