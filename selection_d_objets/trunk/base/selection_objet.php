<?php
/**
 * Plugin Selection d&#039;objets
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function selection_objet_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['selection_objets'] = 'selection_objets';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function selection_objet_declarer_tables_objets_sql($tables) {

	$tables['spip_selection_objets'] = array(
		'type' => 'selection_objet',
		'principale' => "oui", 
		'table_objet_surnoms' => array('selectionobjet'), // table_objet('selection_objet') => 'selection_objets' 
		'field'=> array(
			"id_selection_objet" => "bigint(21) NOT NULL",
			"titre"              => "varchar(255) NOT NULL DEFAULT ''",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"url"                => "varchar(255) NOT NULL DEFAULT ''",
			"id_objet"           => "bigint(21) NOT NULL",
			"id_objet_dest"      => "bigint(21) NOT NULL",
			"objet"              => "varchar(100) NOT NULL",
			"objet_dest"         => "varchar(100) NOT NULL",
			"ordre"              => "bigint(21) NOT NULL",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_selection_objet",
			"KEY lang"           => "lang", 
			"KEY statut"         => "statut",
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'descriptif', 'url'),
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
		'texte_changer_statut' => 'selection_objet:texte_changer_statut_objet', 
		

	);

	return $tables;
}



?>