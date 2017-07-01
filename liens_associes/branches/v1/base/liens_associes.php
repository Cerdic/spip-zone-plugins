<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Liens associés
 * @copyright  2017
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Liens_associes\Pipelines
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
function liens_associes_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['associe_liens'] = 'associe_liens';

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
function liens_associes_declarer_tables_objets_sql($tables) {

	$tables['spip_associe_liens'] = array(
		'type' => 'associe_lien',
		'principale' => 'oui',
		'table_objet_surnoms' => array('associelien'), // table_objet('associe_lien') => 'associe_liens'
		'field'=> array(
			'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
			'id_associe_lien'  	 => 'bigint(21) NOT NULL',
			'lien_interne'       => 'tinytext NOT NULL DEFAULT ""',
			'objet_spip'              => 'varchar(55) NOT NULL DEFAULT ""',
			'id_objet_spip'           => 'bigint(21) NOT NULL DEFAULT 0',
			'url'                => 'varchar(255) NOT NULL DEFAULT ""',
			'descriptif'         => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_associe_lien',
			'KEY statut'         => 'statut,objet_spip,id_objet_spip',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('lien_interne', 'objet_spip', 'titre', 'url', 'descriptif', 'id_objet_spip'),
		'champs_versionnes' => array('lien_interne', 'objet_spip', 'titre', 'url', 'descriptif', 'id_objet_spip'),
		'rechercher_champs' => array("titre" => 10, "url" => 5, "descriptif" => 6),
		'tables_jointures'  => array('spip_associe_liens_liens'),
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
		'texte_changer_statut' => 'associe_lien:texte_changer_statut_associe_lien',


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
function liens_associes_declarer_tables_auxiliaires($tables) {

	$tables['spip_associe_liens_liens'] = array(
		'field' => array(
			'id_associe_lien'    => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_associe_lien,id_objet,objet',
			'KEY id_associe_lien' => 'id_associe_lien',
		)
	);

	return $tables;
}
