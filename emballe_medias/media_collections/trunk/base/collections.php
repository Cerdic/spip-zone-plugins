<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Licence GNU/GPL
 * 
 * Déclarations relatives à la base de données
 * 
 * @package SPIP\Collections\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 * 
 * @param array $interfaces Le tableau des tables
 * @return array Le tableau $interfaces complété
 */
function collections_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['collections'] = 'collections';

	return $interfaces;
}


/**
 * Déclaration de la table principale spip_collections correspondant 
 * à l'objet éditorial "collection"
 *
 * @param array $tables 
 * 		Le tableau de déclaration des tables
 * @return array $tables
 * 		Le tableau de déclaration des tables complétées
 */
function collections_declarer_tables_objets_sql($tables) {

	$tables['spip_collections'] = array(
		'type' => 'collection',
		'principale' => "oui",
		'field'=> array(
			"id_collection"      => "bigint(21) NOT NULL",
			"id_admin"			 => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL",
			"descriptif"         => "text NOT NULL",
			"genre"				 => "varchar(25) NOT NULL DEFAULT 'mixed'", # photo, musique, video, mixed
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"type_collection"    => "varchar(25) NOT NULL DEFAULT 'perso'",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL",
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'",
			"id_trad"            => "bigint(21) NOT NULL DEFAULT 0",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_collection",
			"KEY lang"           => "lang", 
			"KEY id_trad"        => "id_trad",
			"KEY statut"         => "statut",
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'descriptif', 'date', 'type_collection','genre'),
		'champs_versionnes' => array('titre', 'descriptif', 'type_collection','genre'),
		'rechercher_champs' => array('titre' => 8, 'descriptif' => 2),
		'tables_jointures'  => array(
			#'spip_collections_liens'
		),
		'statut_textes_instituer' => array(
			'publie'   => 'collection:texte_statut_publie',
			'prepa'    => 'texte_statut_en_cours_redaction',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'collection:texte_changer_statut_collection'
	);

	// jointures sur les collections pour tous les objets
	$tables[]['tables_jointures'][]= 'collections_liens';
	$tables[]['tables_jointures'][]= 'collections';

	// cas particulier des auteurs et mots : declarer explicitement auteurs_liens comme jointure privilegiee
	// cf http://core.spip.org/issues/2329
	$tables['spip_collections']['tables_jointures'][]= 'auteurs_liens';
	$tables['spip_collections']['tables_jointures'][]= 'auteurs';
	$tables['spip_collections']['tables_jointures'][]= 'collections_liens';
	$tables['spip_collections']['tables_jointures'][]= 'collections';
	return $tables;
}


/**
 * Déclaration de la table secondaire spip_collections_liens 
 * permettant de lier les medias aux collections
 * 
 * @param array $tables
 * 		Tableau des tables secondaires
 * @return array $tables
 * 		Tableau des tables secondaires complété
 */
function collections_declarer_tables_auxiliaires($tables) {

	$tables['spip_collections_liens'] = array(
		'field' => array(
			"id_collection"      => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"id_auteur"			 => "bigint(21) DEFAULT '0' NOT NULL",
			"rang" 				 => "bigint(21) DEFAULT '0' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_collection,id_objet,objet,rang",
			"KEY id_collection"  => "id_collection"
		)
	);

	return $tables;
}


?>