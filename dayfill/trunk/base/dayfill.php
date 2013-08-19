<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     DayFill
 * @copyright  2013
 * @author     Cyril Marion
 * @licence    GNU/GPL
 * @package    SPIP\Dayfill\Pipelines
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
function dayfill_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['projets_activites'] = 'projets_activites';

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
function dayfill_declarer_tables_objets_sql($tables) {

	$tables['spip_projets_activites'] = array(
		'type' => 'projets_activite',
		'principale' => "oui",
		'table_objet_surnoms' => array('projetsactivite'), // table_objet('projets_activite') => 'projets_activites'
		'field'=> array(
			"id_projets_activite"  => "bigint(21) NOT NULL",
			"id_projet"            => "int(11) DEFAULT NULL",
			"descriptif"           => "varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL",
			"id_auteur"            => "int(11) NOT NULL DEFAULT '0'",
			"date_debut"           => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"date_fin"             => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"nb_heures_passees"    => "decimal(18,2) DEFAULT NULL",
			"nb_heures_decomptees" => "decimal(18,2) DEFAULT NULL",
			"maj"                  => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_projets_activite",
		),
		'titre' => "descriptif AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('id_projet', 'descriptif', 'id_auteur', 'date_debut', 'date_fin', 'nb_heures_passees', 'nb_heures_decomptees'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array("descriptif" => 10),
		'tables_jointures'  => array(),


	);

	return $tables;
}



?>
