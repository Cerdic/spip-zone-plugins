<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Devis
 * @copyright  2018
 * @author     RastaPopoulos
 * @licence    GNU/GPL
 * @package    SPIP\Devis\Pipelines
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
function devis_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['devis'] = 'devis';

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
function devis_declarer_tables_objets_sql($tables) {

	$tables['spip_devis'] = array(
		'type' => 'devis',
		'principale' => 'oui',
		'field'=> array(
			'id_devis'           => 'bigint(21) NOT NULL',
			'id_rubrique'        => 'bigint(21) NOT NULL DEFAULT 0',
			'id_secteur'         => 'bigint(21) NOT NULL DEFAULT 0',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'reference'          => 'varchar(255) NOT NULL DEFAULT ""',
			'descriptif'         => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_devis',
			'KEY id_rubrique'    => 'id_rubrique',
			'KEY id_secteur'     => 'id_secteur',
			'KEY statut'         => 'statut',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'reference', 'descriptif', 'id_rubrique', 'id_secteur'),
		'champs_versionnes' => array('titre', 'reference', 'descriptif', 'id_rubrique', 'id_secteur'),
		'rechercher_champs' => array("titre" => 10, "reference" => 10, "descriptif" => 8),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'texte_statut_propose_evaluation',
			'accepte'  => 'devis:statut_accepte',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'prepa'    => 'puce-blanche.gif',
			'prop'     => 'puce-orange.gif',
			'accepte'  => 'puce-verte.gif',
			'refuse'   => 'puce-rouge.gif',
			'poubelle' => 'puce-poubelle.gif',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'accepte,prop,refuse',
				'previsu'   => 'accepte,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'devis:texte_changer_statut_devis',


	);

	return $tables;
}
