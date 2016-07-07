<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function mailsubscribers_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['mailsubscribers'] = 'mailsubscribers';
	$interfaces['table_des_tables']['mailsubscribinglists'] = 'mailsubscribinglists';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 */
function mailsubscribers_declarer_tables_objets_sql($tables) {

	$tables['spip_mailsubscribers'] = array(
		'type' => 'mailsubscriber',
		'page'=>'',
		'principale' => "oui",
		'field'=> array(
			"id_mailsubscriber"   => "bigint(21) NOT NULL",
			"email"              => "varchar(255) NOT NULL DEFAULT ''",
			"nom"                => "text NOT NULL DEFAULT ''",
			"listes"             => "text NOT NULL DEFAULT ''",
			"optin"              => "text NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(20)  DEFAULT 'prepa' NOT NULL",
			"jeton"             => "char(25)  DEFAULT '' NOT NULL",
			"lang"               => "VARCHAR(10) NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_mailsubscriber",
			"UNIQUE email"           => "email(255)",
			"KEY lang"           => "lang",
			"KEY statut"         => "statut",
		),
		'titre' => "email AS titre, lang AS lang",
		'date' => "date",
		'champs_editables'  => array('email', 'nom', 'listes', 'lang'),
		'champs_versionnes' => array('email', 'nom', 'listes', 'lang'),
		'rechercher_champs' => array("email" => 1, "nom" => 1),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'prepa'    => 'mailsubscriber:texte_statut_pas_encore_inscrit',
			'prop'    => 'mailsubscriber:texte_statut_en_attente_confirmation',
			'valide'   => 'mailsubscriber:texte_statut_valide',
			'refuse'   => 'mailsubscriber:texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'prepa'=>'puce-preparer-8.png',
			'prop'=>'puce-proposer-8.png',
			'valide'=>'puce-publier-8.png',
			'refuse'=>'puce-refuser-8.png',
			'poubelle'=>'puce-supprimer-8.png',
		),
		'statut_titres' => array(
			'prepa'=>'mailsubscriber:info_statut_prepa',
			'prop'=>'mailsubscriber:info_statut_prop',
			'valide'=>'mailsubscriber:info_statut_valide',
			'refuse'=>'mailsubscriber:info_statut_refuse',
			'poubelle'=>'mailsubscriber:info_statut_poubelle',
		),

		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'valide',
				'previsu'   => 'valide,prop,prepa',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'mailsubscriber:texte_changer_statut_mailsubscriber',

	);

	$tables['spip_mailsubscribinglists'] = array(
		'type' => 'mailsubscribinglist',
		'page'=>'',
		'principale' => "oui",
		'field'=> array(
			"id_mailsubscribinglist" => "bigint(21) NOT NULL",
			"identifiant"            => "varchar(255) NOT NULL DEFAULT ''",
			"titre"                  => "text NOT NULL DEFAULT ''",
			"descriptif"             => "text DEFAULT '' NOT NULL",
			"date"                   => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"                 => "varchar(20)  DEFAULT 'prepa' NOT NULL",
			"maj"                    => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"            => "id_mailsubscribinglist",
			"UNIQUE identifiant"     => "identifiant(255)",
			"KEY statut"             => "statut",
		),
		'titre' => "titre",
		'date' => "date",
		'champs_editables'  => array('identifiant', 'titre', 'descriptif', 'date', 'statut'),
		'champs_versionnes' => array('identifiant', 'titre', 'descriptif'),
		'rechercher_champs' => array('identifiant' => 1, 'titre' => 2, 'descriptif'=>1),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'ouverte'    => 'mailsubscribinglist:texte_statut_ouverte',
			'fermee'    => 'mailsubscribinglist:texte_statut_fermee',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'ouverte'=>'puce-publier-8.png',
			'fermee'=>'puce-refuser-8.png',
			'poubelle'=>'puce-supprimer-8.png',
		),
		'statut_titres' => array(
			'ouverte'=>'mailsubscribinglist:info_statut_ouverte',
			'fermee'=>'mailsubscribinglist:info_statut_fermee',
			'poubelle'=>'mailsubscribinglist:info_statut_poubelle',
		),

		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'ouverte',
				'previsu'   => 'ouverte,fermee',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'mailsubscribinglist:texte_changer_statut_mailsubscribinglist',

	);

	return $tables;
}



/**
 * Déclaration des tables secondaires (liaisons)
 * @param array $tables
 * @return array
 */
function mailsubscribers_declarer_tables_auxiliaires($tables) {

	$tables['spip_mailsubscriptions'] = array(
		'field' => array(
			"id_mailsubscriber"           => "bigint(21) DEFAULT '0' NOT NULL",
			"id_mailsubscribinglist"      => "bigint(21) DEFAULT '0' NOT NULL",
			"statut"                      => "varchar(20)  DEFAULT 'prop' NOT NULL",
			"maj"                         => "TIMESTAMP",
		),
		'key' => array(
			"PRIMARY KEY"                 => "id_mailsubscriber,id_mailsubscribinglist",
			"KEY id_mailsubscriber"       => "id_mailsubscriber",
			"KEY id_mailsubscribinglist"  => "id_mailsubscribinglist",
			"KEY statut"                  => "statut"
		)
	);

	return $tables;
}

