<?php
/**
 * Déclarations relatives à la base de données.
 *
 * @plugin     Déclinaisons Prix
 * @copyright  2012 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Promotions_commandes\Vase
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function declinaisons_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['declinaisons'] = 'declinaisons';
	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 */
function declinaisons_declarer_tables_objets_sql($tables) {
	$tables['spip_declinaisons'] = array(
		'type' => 'declinaison',
		'principale' => "oui",
		'field' => array(
			"id_declinaison" => "bigint(21) NOT NULL",
			"titre" => "varchar(255)  DEFAULT '' NOT NULL",
			"descriptif" => "text NOT NULL",
			"id_parent" => "bigint(21) NOT NULL",
			"statut" => "varchar(20)  DEFAULT '0' NOT NULL",
			"maj" => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" => "id_declinaison",
			"KEY statut" => "statut"
		),
		'titre' => "titre AS titre, '' AS lang",
		// 'date' => "",
		'champs_editables' => array(
			'titre',
			'descriptif',
			'id_parent'
		),
		'champs_versionnes' => array(
			'titre',
			'descriptif',
			'id_parent'
		),
		'rechercher_champs' => array(
			'titre' => 8,
			'descriptif' => 2
		),
		'tables_jointures' => array(),
		'statut_textes_instituer' => array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'prop' => 'texte_statut_propose_evaluation',
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle'
		),
		'statut' => array(
			array(
				'champ' => 'statut',
				'publie' => 'publie',
				'previsu' => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => array(
					'statut',
					'tout'
				)
			)
		),
		'texte_changer_statut' => 'declinaison:texte_changer_statut_declinaison'

	);

	return $tables;
}
