<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Taxonomie
 * @copyright  2014
 * @author     Eric
 * @licence    GNU/GPL
 * @package    SPIP\Taxonomie\Pipelines
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
function taxonomie_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['taxons'] = 'taxons';

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
function taxonomie_declarer_tables_objets_sql($tables) {

	/**
	 * Déclaration de l'objet taxon utilisé pour matérialiser une arborescence de taxons
	 * du règne au genre. Les taxons à partir de l'espèce ne font pas partie de cette
	 * table.
	 *
	 * Le nom_scientifique est le nom en latin. Il est unique pour un rang taxonomique donné.
	 * Le rang taxonomique est une valeur parmi règne, phylum, classe, ordre, famille et genre.
	 * Le nom_commun est le nom vulgaire, si possible normalisé par une commission officielle. Il peut coincider ou pas
	 * avec le nom vernaculaire.
	 * L'auteur est une information composée d'un ou plusieurs noms complétés par une date (Linneus, 1798).
	 * tsn est l'identifiant numérique unique du taxon dans la base taxonomique ITIS.
	 * tsn_parent permet de créer l'arborescence taxonomique du règne conformément à l'organisation de la base ITIS
	 */
	$tables['spip_taxons'] = array(
		'type' => 'taxon',
		'principale' => "oui",
		'field'=> array(
			"id_taxon"			=> "bigint(21) NOT NULL",
			"nom_scientifique"	=> "varchar(35) DEFAULT '' NOT NULL",
			"rang"				=> "varchar(15) DEFAULT '' NOT NULL",
			"regne"				=> "varchar(10) DEFAULT '' NOT NULL",
			"nom_commun"		=> "text DEFAULT '' NOT NULL",
			"auteur"			=> "varchar(100) DEFAULT '' NOT NULL",
			"descriptif"		=> "text DEFAULT '' NOT NULL",
			"tsn"				=> "bigint(21) NOT NULL",
			"tsn_parent"		=> "bigint(21) NOT NULL",
			"sources"			=> "text DEFAULT '' NOT NULL",
			"edite"				=> "varchar(3) DEFAULT 'non' NOT NULL",
			"maj"				=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_taxon",
		),
		'titre' => "nom_scientifique AS titre, '' AS lang",

		'champs_editables'  => array('nom_commun', 'descriptif'),
		'champs_versionnes' => array('nom_commun', 'descriptif'),
		'rechercher_champs' => array("nom_scientifique" => 10, "nom_commun" => 10, "descriptif" => 5),
		'tables_jointures'  => array(),
	);

	return $tables;
}

?>