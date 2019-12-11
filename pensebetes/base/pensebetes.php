<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function pensebetes_declarer_tables_objets_sql($tables){
	$tables['spip_pensebetes'] = array(
		'principale' => "oui",
		'field'=> array(
			"id_pensebete"   => "bigint(21) NOT NULL",
			"id_donneur" 	 => "bigint(21) DEFAULT '0' NOT NULL",
			"id_receveur" 	 => "bigint(21) DEFAULT '0' NOT NULL",
			"date"           => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"titre"          => "varchar(255) DEFAULT '' NOT NULL",
			"texte"          => "text DEFAULT '' NOT NULL",
			"maj"            => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"      => "id_pensebete",
			"KEY id_donneur"   => "id_donneur",
			"KEY id_receveur"  => "id_receveur",
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",

		'champs_editables' => array(
			"id_donneur", "id_receveur", "date", "titre", "texte"
		),
		'rechercher_champs' => array(
			'titre' => 1, 'texte' => 5
		),
		'tables_jointures' => array(
			'pensebetes_liens'
		),

		
	);
	
	return $tables;
}



function pensebetes_declarer_tables_auxiliaires($tables) {
	$tables['spip_pensebetes_liens'] = array(
		'field' => array(
			"id_pensebete"  => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet" => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"    => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"       => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY" => "id_pensebete,id_objet,objet",
			"KEY id_pensebete" => "id_pensebete"
		)
	);
	return $tables;
}

function pensebetes_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['pensebetes'] = 'pensebetes';
	return $interfaces;
}

