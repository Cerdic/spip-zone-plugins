<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * © 2007 - 2012
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function abomailmans_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['abomailmans'] = 'abomailmans';
	return $interface;
}

function abomailmans_declarer_tables_objets_sql($tables){
	$tables['spip_abomailmans'] = array(
		'page' => 'abomailman',
		'url_edit' => 'abomailman_edit',
		'editable' => 'oui',
		'texte_retour' => 'abomailmans:icone_retour_abomailman',
		'texte_objet' => 'abomailmans:abomailman',
		'texte_objets' => 'abomailmans:abomailmans',
		'texte_modifier' => 'abomailmans:icone_modifier_abomailman',
		'texte_creer' => 'abomailmans:nouveau_abomailman',
		'info_aucun_objet'=> 'abomailmans:info_abomailman_aucun',
		'info_1_objet' => 'abomailmans:info_abomailmans_1',
		'info_nb_objets' => 'abomailmans:info_abomailmans_nb',
		'icone_objet' => 'abomailmans-24',
		'titre' => "titre, '' AS lang",
		'principale' => 'oui',
		'champs_editables' => array('titre','descriptif','abo_type','email','email_sympa','email_subscribe','email_unsubscribe','modele_defaut','periodicite','lang','desactive'),
		'field'=> array(
			"id_abomailman" => "bigint(21) NOT NULL",
			"titre" 	=> "varchar(255) NOT NULL",
			"descriptif" 	=> "text",
			"abo_type" => "varchar(255) DEFAULT 'news' NOT NULL",
			"email"		=> "varchar(255)",
			"email_sympa"   => "varchar(255) DEFAULT '' NOT NULL",
			"email_subscribe"   => "varchar(255)",
			"email_unsubscribe" => "varchar(255)",
			"modele_defaut" => "varchar(255) DEFAULT '' NOT NULL",
			"periodicite" => "varchar(255) DEFAULT '' NOT NULL",
			"maj" 		=> "TIMESTAMP",
			"date_envoi" 	=> "TIMESTAMP",
			"lang"		=> "VARCHAR(10) DEFAULT '' NOT NULL",
			"desactive"     => "tinyint(4) NOT NULL default '0'"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_abomailman"
		),
		'rechercher_champs' => array(
			'titre' => 8,
			'descriptif' => 8
		),
		'champs_versionnes' => array('titre', 'descriptif', 'email', 'email_sympa', 'email_subscribe', 'email_unsubscribe', 'modele_defaut','periodicite','lang','desactive')
	);
	return $tables;
}

?>