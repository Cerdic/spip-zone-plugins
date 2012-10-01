<?php
/**
 * Plugin Annonces
 * (c) 2012 apéro spip
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function spipad_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['ads'] = 'ads';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function spipad_declarer_tables_objets_sql($tables) {

	$tables['spip_ads'] = array(
		'type' => 'ad',
		'principale' => "oui",
		'field'=> array(
			"id_ad"              => "bigint(21) NOT NULL",
			"id_rubrique"        => "bigint(21) NOT NULL DEFAULT 0", 
			"id_secteur"         => "bigint(21) NOT NULL DEFAULT 0", 
			"titre"              => "varchar(255) NOT NULL DEFAULT ''",
			"texte"              => "text NOT NULL DEFAULT ''",
			"PRIX"               => "varchar(255)",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'", 
			"id_trad"            => "bigint(21) NOT NULL DEFAULT 0", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_ad",
			"KEY id_rubrique"    => "id_rubrique", 
			"KEY id_secteur"     => "id_secteur", 
			"KEY lang"           => "lang", 
			"KEY id_trad"        => "id_trad", 
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'texte', 'PRIX'),
		'champs_versionnes' => array('titre', 'texte', 'PRIX'),
		'rechercher_champs' => array("titre" => 5, "texte" => 5),
		'tables_jointures'  => array('spip_ads_liens'),
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
		'texte_changer_statut' => 'ad:texte_changer_statut_ad', 

	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 */
function spipad_declarer_tables_auxiliaires($tables) {

	$tables['spip_ads_liens'] = array(
		'field' => array(
			"id_ad"              => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_ad,id_objet,objet",
			"KEY id_ad"          => "id_ad"
		)
	);

	return $tables;
}


?>