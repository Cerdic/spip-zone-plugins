<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Chapitres
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Chapitres\Pipelines
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
function chapitres_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['chapitres'] = 'chapitres';
	$interfaces['table_des_tables']['hierarchie_chapitres'] = 'chapitres';
	
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
function chapitres_declarer_tables_objets_sql($tables) {

	$tables['spip_chapitres'] = array(
		'type' => 'chapitre',
		'principale' => 'oui',
		'field'=> array(
			'id_chapitre'        => 'bigint(21) NOT NULL',
			'objet'              => 'varchar(255) not null default ""',
			'id_objet'           => 'bigint(21) not null default 0',
			'id_parent'          => 'bigint(21) NOT NULL DEFAULT 0',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'chapo'              => 'text NOT NULL DEFAULT ""',
			'texte'              => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'lang'               => 'VARCHAR(10) NOT NULL DEFAULT ""',
			'langue_choisie'     => 'VARCHAR(3) DEFAULT "non"',
			'id_trad'            => 'bigint(21) NOT NULL DEFAULT 0',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_chapitre',
			'KEY lang'           => 'lang',
			'KEY id_trad'        => 'id_trad',
			'KEY statut'         => 'statut',
		),
		'join' => array(
			'id_forum'  => 'id_forum',
			'id_parent' => 'id_parent',
			'id_objet'  => 'id_objet',
			'objet'     => 'objet',
			'id_auteur' => 'id_auteur',
		),
		'titre' => 'titre AS titre, lang AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'texte', 'id_parent', 'chapo'),
		'champs_versionnes' => array('titre', 'texte', 'id_parent', 'chapo'),
		'rechercher_champs' => array("titre" => 10, "texte" => 5, "chapo" => 6),
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
		'texte_changer_statut' => 'chapitre:texte_changer_statut_chapitre',
		'parent' => array(
			array('condition' => 'id_parent=0', 'champ_type' => 'objet', 'champ' => 'id_objet', 'exclus' => array('chapitre')),
			array('condition' => 'id_parent>0', 'type' => 'chapitre', 'champ' => 'id_parent'),
		),
	);

	return $tables;
}
