<?php
/**
 * Plugin Pays pour Spip 3.0
 * Licence GPL
 * Auteur Organisation Internationale de Normalisation http://www.iso.org/iso/fr/country_codes/iso_3166_code_lists.htm
 * Cedric Morin et Collectif SPIP pour version spip_geo_pays
 * Portage sous SPIP par Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function pays_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['pays'] = 'pays';
	$interface['table_des_tables']['geo_pays'] = 'pays'; // en attendant une meilleure collaboration avec 'geographie'
	$interface['table_des_traitements']['NOM'][] = _TRAITEMENT_RACCOURCIS;

	return $interface;
}

function pays_declarer_tables_objets_sql($tables){

$tables['spip_pays'] = array(

		'principale' => "oui",
		'page' => false,
		'field'=> array(
			"id_pays"		=> "smallint(6) NOT NULL auto_increment",
			"code"			=> "varchar(2) NOT NULL default ''",
			"code_alpha3"	=> "varchar(3) NOT NULL default ''",
			"code_num"		=> "int(3) UNSIGNED ZEROFILL NOT NULL default 0",
			"nom"			=> "text NOT NULL default ''",
			"maj"			=> "TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_pays",
			"UNIQUE KEY"	=> "code,code_alpha3,code_num"
		),
		'champs_editables' => array(
			"code", "nom"
		),
		'rechercher_champs'      => array('nom'=>5, 'code'=>3, 'code_alpha3'=>3),
		'titre' => "nom AS titre, '' AS lang",
		'table_objet' => 'pays',
		'table_objet_surnoms' => array('pays'),
		'type' => "pays",
		'type_surnoms' => array("pay"),

		'texte_modifier' => "pays:icone_modifier_pays",
		'texte_creer' => "pays:icone_creer_pays",
		'texte_objet' => "pays:titre_pays",
		'texte_objets' => "pays:titre_pays",
		'info_aucun_objet' => "pays:info_aucun_pays",
		'info_1_objet' => "pays:info_1_pays",
		'info_nb_objets' => "pays:info_nb_pays",
		'texte_logo_objet' => "pays:texte_logo_pays",
		'tables_jointures'  => array('spip_pays_liens')

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
function pays_declarer_tables_auxiliaires($tables) {

	$tables['spip_pays_liens'] = array(
		'field' => array(
			"id_pays"            => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_pays,id_objet,objet",
			"KEY id_pays"        => "id_pays"
		)
	);

	return $tables;
}
