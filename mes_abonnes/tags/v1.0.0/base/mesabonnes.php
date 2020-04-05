<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function mesabonnes_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['mesabonnes']='mesabonnes';
	return $interface;
}


function mesabonnes_declarer_tables_objets_sql($tables){

	/* Declaration de la table archive des abonnes */
	$tables['spip_mesabonnes'] = array(
		/* Declarations principales */
		'table_objet' => 'mesabonnes',
		'table_objet_surnoms' => array('mesabonnes'),
		'type' => 'mesabonnes',
		'type_surnoms' => array('mesabonnes'),

		/* La table */
		'field'=> array(
				"id_abonne" 	=> "bigint(21) NOT NULL auto_increment",
				"nom"	=> "text DEFAULT '' NOT NULL",
				"email"	=> "text DEFAULT '' NOT NULL",
				"lang"	=> "tinytext DEFAULT '' NOT NULL",
				"date_modif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
				"liste"	=> "text DEFAULT '' NOT NULL", // pas utilise pour l'instant, gestion multi-liste ?
				"statut"	=> "varchar(6)  DEFAULT '0' NOT NULL"),
		'key' => array(
			"PRIMARY KEY" => "id_abonne",
		),
		'principale' => 'oui'

	);


	return $tables;
}

