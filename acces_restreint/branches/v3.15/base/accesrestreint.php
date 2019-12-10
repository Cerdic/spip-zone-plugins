<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function accesrestreint_declarer_tables_interfaces($interface) {
	$interface['tables_jointures']['spip_auteurs'][] = 'zones_liens';
	$interface['tables_jointures']['spip_zones'][] = 'zones_liens';
	$interface['tables_jointures']['spip_rubriques'][] = 'zones_liens';

	//-- Table des tables ----------------------------------------------------

	$interface['table_des_tables']['zones']='zones';

	return $interface;
}

function accesrestreint_declarer_tables_objets_sql($tables) {
	$tables['spip_zones'] = array(
		'texte_modifier' => 'accesrestreint:modifier_zone',
		'texte_creer' => 'accesrestreint:creer_zone',
		'texte_objets' => 'accesrestreint:titre_zones_acces',
		'texte_objet' => 'accesrestreint:titre_zone_acces',
		'texte_ajouter' => 'accesrestreint:texte_ajouter_zone',
		'texte_creer_associer' => 'accesrestreint:texte_creer_associer_zone',
		'info_aucun_objet'=> 'accesrestreint:info_aucune_zone',
		'info_1_objet' => 'accesrestreint:info_1_zone',
		'info_nb_objets' => 'accesrestreint:info_nb_zones',
		'url_voir' => 'zone_edit',
		'url_edit' => 'zone_edit',
		'page' => false,

		'principale' => 'oui',
		'champs_editables' => array('titre', 'descriptif', 'publique', 'privee', 'autoriser_si_connexion'),
		'field'=> array(
			'id_zone' 	=> 'bigint(21) NOT NULL',
			'titre' 	=> "varchar(255) DEFAULT '' NOT NULL",
			'descriptif' 	=> "text DEFAULT '' NOT NULL",
			'publique' 	=> "char(3) DEFAULT 'oui' NOT NULL",
			'privee' 	=> "char(3) DEFAULT 'non' NOT NULL",
			'autoriser_si_connexion' => "char(3) DEFAULT 'non' NOT NULL",
			'maj' 		=> 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'	=> 'id_zone',
		),
		'titre' => "titre AS titre, '' AS lang",
	);

	return $tables;
}

function accesrestreint_declarer_tables_auxiliaires($tables_auxiliaires) {

	$spip_zones_liens = array(
		'id_zone'	=> "bigint(21) DEFAULT '0' NOT NULL",
		'id_objet'	=> "bigint(21) DEFAULT '0' NOT NULL",
		'objet'	=> "VARCHAR (25) DEFAULT '' NOT NULL"
	);

	$spip_zones_liens_key = array(
		'PRIMARY KEY'		=> 'id_zone,id_objet,objet',
		'KEY id_zone'	=> 'id_zone'
	);

	$tables_auxiliaires['spip_zones_liens'] = array(
		'field' => &$spip_zones_liens,
		'key' => &$spip_zones_liens_key
	);

	return $tables_auxiliaires;
}
