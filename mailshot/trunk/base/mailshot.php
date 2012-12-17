<?php
/**
 * Plugin mailshots
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function mailshot_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['mailshot'] = 'mailshot';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function mailshot_declarer_tables_objets_sql($tables) {

	$tables['spip_mailshot'] = array(
		'type' => 'mailshot',
		'table_objet_surnoms'=>array('mailshot'),
		'url_voir' => false,  // pas de vue
		'url_edit' => false,  // pas d'edition
		'principale' => "oui",  // auto-increment
		'field'=> array(
			"id_mailshot"   => "bigint(21) NOT NULL",
			"id" => "varchar(32)  DEFAULT '' NOT NULL",
			"sujet" => "text NOT NULL",
			"html" => "longtext NOT NULL DEFAULT ''",
			"texte" => "longtext NOT NULL DEFAULT ''",
			"listes" => "text NOT NULL DEFAULT ''",
			"total"   => "bigint(21) NOT NULL",
			"current" => "bigint(21) NOT NULL",
			"failed" => "bigint(21) NOT NULL",
			"next" => "float(12) NOT NULL",
			"date" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"date_start" => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut" => "varchar(20)  DEFAULT 'processing' NOT NULL",
		),
		'key' => array(
			"PRIMARY KEY"        => "id_mailshot",
			"KEY statut"         => "statut",
		),
		'titre' => "sujet AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('email', 'nom', 'listes', 'lang'),
		'champs_versionnes' => array('email', 'nom', 'listes', 'lang'),
		'rechercher_champs' => array("email" => 1, "nom" => 1),
		'tables_jointures'  => array(),
		'statut_images' => array(
			'pause'=>'puce-preparer-8.png',
			'processing'=>'puce-proposer-8.png',
			'end'=>'puce-publier-8.png',
			'cancel'=>'puce-refuser-8.png',
			'poubelle'=>'puce-supprimer-8.png',
		),
		'statut_titres' => array(
			'pause'=>'mailshot:info_statut_prepa',
			'processing'=>'mailshot:info_statut_prop',
			'end'=>'mailshot:info_statut_valide',
			'cancel'=>'mailshot:info_statut_refuse',
			'poubelle'=>'mailshot:info_statut_poubelle',
		),
		'texte_changer_statut' => 'mailshot:texte_changer_statut_mailshot',

	);

	return $tables;
}



?>