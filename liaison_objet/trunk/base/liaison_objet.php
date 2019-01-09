<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Liaison_objets
 * @copyright  2012 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Liaison_objets\Pipelines
 */


if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function liaison_objet_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['liaison_objets'] = 'liaison_objets';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 */
function liaison_objet_declarer_tables_objets_sql($tables) {

	$tables['spip_liaison_objets'] = array(
		'type' => 'liaison_objet',
		'principale' => "oui",
		'table_objet_surnoms' => array('liaisonobjet'),
		'field'=> array(
			"id_liaison_objet"   => "bigint(21) NOT NULL",
			"titre"              => "varchar(255) NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"url"                => "varchar(255) NOT NULL DEFAULT ''",
			"id_objet"           => "bigint(21) NOT NULL",
			"id_objet_dest"      => "bigint(21) NOT NULL",
			"objet"              => "varchar(100) NOT NULL",
			"objet_dest"         => "varchar(100) NOT NULL",
			"ordre"              => "bigint(21) NOT NULL",
			"ordre_objet"         => "bigint(21) NOT NULL",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL",
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
						"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'",
			"type_lien"          => "VARCHAR(100) NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_liaison_objet",
						"KEY id_objet"       => "id_objet",
						"KEY id_objet_dest"  => "id_objet_dest",
						"KEY objet"          => "objet",
						"KEY objet_dest"     => "objet_dest",
			"KEY lang"           => "lang",
			"KEY statut"         => "statut",

		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'descriptif', 'url','type_lien','id_objet_dest','objet_dest','objet','id_objet','lang'),
		'champs_versionnes' => array('titre', 'descriptif', 'url'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'liaison_objet:texte_changer_statut_objet',
	);

	return $tables;
}
