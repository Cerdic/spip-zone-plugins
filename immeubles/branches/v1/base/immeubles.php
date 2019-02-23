<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Immeubles
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Immeubles\Pipelines
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
function immeubles_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['immeubles'] = 'immeubles';

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
function immeubles_declarer_tables_objets_sql($tables) {

	$tables['spip_immeubles'] = array(
		'type' => 'immeuble',
		'principale' => 'oui',
		'field'=> array(
			'id_immeuble'        => 'bigint(21) NOT NULL',
			'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
			'descriptif'         => 'text NOT NULL DEFAULT ""',
			'texte'         => 'text NOT NULL DEFAULT ""',
			'chambres'           => 'int(6) NOT NULL DEFAULT 0',
			'lits_simples'       => 'int(6) NOT NULL DEFAULT 0',
			'lits_doubles'       => 'int(6) NOT NULL DEFAULT 0',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'lang'               => 'VARCHAR(10) NOT NULL DEFAULT ""',
			'langue_choisie'     => 'VARCHAR(3) DEFAULT "non"',
			'id_trad'            => 'bigint(21) NOT NULL DEFAULT 0',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_immeuble',
			'KEY lang'           => 'lang',
			'KEY id_trad'        => 'id_trad',
			'KEY statut'         => 'statut',
		),
		'titre' => 'titre AS titre, lang AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'descriptif', 'texte', 'chambres'),
		'champs_versionnes' => array('titre', 'descriptif', 'texte', 'chambres'),
		'rechercher_champs' => array("titre" => 8, "descriptif" => 5, 'texte' => 5),
		'tables_jointures'  => array(),
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
		'texte_changer_statut' => 'immeuble:texte_changer_statut_immeuble',


	);

	return $tables;
}
