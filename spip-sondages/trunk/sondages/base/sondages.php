<?php
/**
 * Plugin Spip-sondages
 * (c) 2012 Maïeul Rouquette d&#039;après Artego
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function sondages_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['sondages'] = 'sondages';
	$interfaces['table_des_tables']['choix'] = 'choix';
	$interfaces['table_des_tables']['avis'] = 'avis';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function sondages_declarer_tables_objets_sql($tables) {

	$tables['spip_sondages'] = array(
		'type' => 'sondage',
		'principale' => "oui",
		'field'=> array(
			"id_sondage"         => "bigint(21) NOT NULL",
			"id_rubrique"        => "bigint(21) NOT NULL DEFAULT 0", 
			"id_secteur"         => "bigint(21) NOT NULL DEFAULT 0", 
			"titre"              => "TEXT NOT NULL",
			"texte"              => "TEXT NOT NULL",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_sondage",
			"KEY id_rubrique"    => "id_rubrique", 
			"KEY id_secteur"     => "id_secteur", 
			"KEY lang"           => "lang", 
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'texte'),
		'champs_versionnes' => array('titre', 'texte'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_sondages_liens'),
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
		'texte_changer_statut' => 'sondage:texte_changer_statut_sondage', 

	);

	$tables['spip_choix'] = array(
		'type' => 'choix',
		'principale' => "oui", 
		'table_objet_surnoms' => array('choix'), // table_objet('choix') => 'choix' 
		'field'=> array(
			"id_choix"           => "bigint(21) NOT NULL",
			"Ordre"              => "BIGINT(21) NOT NULL DEFAULT '0'",
			"titre"              => "TEXT NOT NULL",
			"id_sondage"         => "BIGINT(21) NOT NULL",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_choix",
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('Ordre', 'titre'),
		'champs_versionnes' => array('Ordre', 'titre'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	$tables['spip_avis'] = array(
		'type' => 'avi',
		'principale' => "oui",
		'field'=> array(
			"id_avi"             => "bigint(21) NOT NULL",
			"id_sondage"         => "BIGINT(21) NOT NULL",
			"id_choix"           => "BIGINT(21) NOT NULL",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_avi",
		),
		'titre' => "'' AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('id_sondage'),
		'champs_versionnes' => array('id_sondage'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 */
function sondages_declarer_tables_auxiliaires($tables) {

	$tables['spip_sondages_liens'] = array(
		'field' => array(
			"id_sondage"         => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_sondage,id_objet,objet",
			"KEY id_sondage"     => "id_sondage"
		)
	);

	return $tables;
}


?>