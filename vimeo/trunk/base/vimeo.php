<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Vimeos
 * @copyright  2017
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Vimeo\Pipelines
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
function vimeo_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['vimeos'] = 'vimeos';

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
function vimeo_declarer_tables_objets_sql($tables) {

	$tables['spip_vimeos'] = array(
		'type' => 'vimeo',
		'principale' => 'oui',
		'field'=> array(
			'id_vimeo'           => 'bigint(21) NOT NULL',
			'url_video'          => 'varchar(255) NOT NULL DEFAULT ""',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'texte'              => 'longtext NOT NULL DEFAULT ""',
			'credits'            => 'longtext NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_vimeo',
			'KEY statut'         => 'statut',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('url_video', 'titre', 'texte', 'credits'),
		'champs_versionnes' => array('url_video', 'titre', 'texte', 'credits'),
		'rechercher_champs' => array("titre" => 2, "texte" => 2),
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
		'texte_changer_statut' => 'vimeo:texte_changer_statut_vimeo',


	);

	return $tables;
}
