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

	$interfaces['table_des_tables']['mailshots'] = 'mailshots';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function mailshot_declarer_tables_objets_sql($tables) {

	$tables['spip_mailshots'] = array(
		'editable' => false,  // pas d'edition
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
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		'statut_images' => array(
			'pause'=>'puce-preparer-8.png',
			'processing'=>'puce-proposer-8.png',
			'end'=>'puce-publier-8.png',
			'cancel'=>'puce-refuser-8.png',
			'poubelle'=>'puce-supprimer-8.png',
		),
		'statut_titres' => array(
			'pause'=>'mailshot:info_statut_pause',
			'processing'=>'mailshot:info_statut_processing',
			'end'=>'mailshot:info_statut_end',
			'cancel'=>'mailshot:info_statut_cancel',
			'poubelle'=>'mailshot:info_statut_poubelle',
		),
		'statut_textes_instituer' => array(
			'pause'=>'mailshot:texte_statut_pause',
			'processing'=>'mailshot:texte_statut_processing',
			'end'=>'mailshot:texte_statut_end',
			'cancel'=>'mailshot:texte_statut_cancel',
			'poubelle' => 'texte_statut_poubelle',
		),
		'texte_changer_statut' => 'mailshot:texte_changer_statut_mailshot',

	);

	return $tables;
}



/**
 * Déclaration des tables secondaires (liaisons)
 */
function mailshot_declarer_tables_auxiliaires($tables) {

	$tables['spip_mailshots_destinataires'] = array(
		'field' => array(
			"id_mailshot"      => "bigint(21) DEFAULT '0' NOT NULL",
			"email"            => "varchar(255) NOT NULL DEFAULT ''",
			"date"             => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"           => "char(4)  DEFAULT 'todo' NOT NULL", // todo, sent, fail, [read, [clic]],[spam]
			"try"              => "tinyint NOT NULL DEFAULT 0", // nombre d'essais
		),
		'key' => array(
			"PRIMARY KEY"        => "id_mailshot,email",
			"KEY email"  => "email",
			"KEY statut"  => "statut"
		)
	);

	return $tables;
}

?>