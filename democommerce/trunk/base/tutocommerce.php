<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Tuto-commerce
 * @copyright  2015
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Tuto-commerce\Pipelines
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
function tutocommerce_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['produits_demos'] = 'produits_demos';

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
function tutocommerce_declarer_tables_objets_sql($tables) {

	$tables['spip_produits_demos'] = array(
		'type'               => 'produitdemo',
		'type_surnoms'       => array('produit_demo'),
		'principale'         => "oui", 
		'field'=> array(
			"id_produitdemo" => "bigint(21) NOT NULL",
			"titre"          => "text NOT NULL DEFAULT ''",
			"prix"           => "decimal(4,2) NOT NULL DEFAULT 0.00",
		),
		'key' => array(
			"PRIMARY KEY"    => "id_produitdemo",
		),
		'titre'              => "titre AS titre, '' AS lang",
		'champs_editables'   => array(),
		'champs_versionnes'  => array(),
		'rechercher_champs'  => array(),
		'tables_jointures'   => array(),
	);

	return $tables;
}

?>
