<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     produits
 * @copyright  2014
 * @author     Arterrien
 * @licence    GNU/GPL
 * @package    SPIP\Produits\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function produits_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['produits'] = 'produits';

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
function produits_declarer_tables_objets_sql($tables) {

	$tables['spip_produits'] = array(
		'type' => 'produit',
		'principale' => "oui",
		'field'=> array(
			"id_produit"         => "bigint(21) NOT NULL",
			"id_rubrique"        => "bigint(21) NOT NULL DEFAULT 0", 
			"id_secteur"         => "bigint(21) NOT NULL DEFAULT 0", 
			"titre"              => "text NOT NULL",
			"reference"          => "tinytext NOT NULL DEFAULT ''",
			"prix_ht"            => "float not null",
			"taxe"               => "decimal(4,3) default null",
			"descriptif"         => "text NOT NULL DEFAULT ''",
			"texte"              => "longtext NOT NULL",
			"date_com"           => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"     => "VARCHAR(3) DEFAULT 'non'", 
			"id_trad"            => "bigint(21) NOT NULL DEFAULT 0", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_produit",
			"KEY id_rubrique"    => "id_rubrique", 
			"KEY id_secteur"     => "id_secteur", 
			"KEY lang"           => "lang", 
			"KEY id_trad"        => "id_trad", 
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'reference', 'prix_ht', 'taxe', 'descriptif'),
		'champs_versionnes' => array('titre', 'descriptif'),
		'rechercher_champs' => array("titre" => 10, "descriptif" => 5),
		'tables_jointures'  => array('spip_produits_liens'),
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
		'texte_changer_statut' => 'produit:texte_changer_statut_produit', 
		

	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function produits_declarer_tables_auxiliaires($tables) {

	$tables['spip_produits_liens'] = array(
		'field' => array(
			"id_produit"         => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_produit,id_objet,objet",
			"KEY id_produit"     => "id_produit"
		)
	);

	return $tables;
}


?>