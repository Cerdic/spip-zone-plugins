<?php
/**
 * Plugin Feuille
 * (c) 2012 chankalan
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function feuille_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['feuilles'] = 'feuilles';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function feuille_declarer_tables_objets_sql($tables) {

	$tables['spip_feuilles'] = array(
		'type' => 'feuille',
		'principale' => "oui",
		'field'=> array(
			"id_feuille"         => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL DEFAULT ''",
			"texte"              => "longtext NOT NULL DEFAULT ''",
			"date_publication"   => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'", 
			"id_trad"            => "bigint(21) NOT NULL DEFAULT 0", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_feuille",
			"KEY lang"           => "lang", 
			"KEY id_trad"        => "id_trad", 
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date_publication",
		'champs_editables'  => array('titre', 'texte'),
		'champs_versionnes' => array('titre', 'texte'),
		'rechercher_champs' => array("titre" => 5, "texte" => 5),
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
		'texte_changer_statut' => 'feuille:texte_changer_statut_feuille', 
		

	);

	return $tables;
}



?>