<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function chats_declarer_tables_objets_sql($tables){
	$tables['spip_chats'] = array(
	
		'principale' => "oui",
		'field'=> array(
			"id_chat"	=> "bigint(21) NOT NULL",
			"id_rubrique" => "bigint(21) NOT NULL DEFAULT 0",
			"nom"	=> "tinytext DEFAULT '' NOT NULL",
			
			"race"	=> "tinytext DEFAULT '' NOT NULL",
			"date"  => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_naissance" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"robe"	=> "tinytext DEFAULT '' NOT NULL",
			"infos"	=> "text DEFAULT '' NOT NULL",
			"lang"  => "VARCHAR(10) DEFAULT '' NOT NULL",
			"langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
			"id_trad" => "bigint(21) DEFAULT '0' NOT NULL",
			"maj"	=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_chat",
			"KEY id_rubrique" => "id_rubriqu",
		),
		'titre' => "nom AS titre, '' AS lang",
		'date' => "date",

		'champs_editables' => array(
			"nom", "race", "robe", "infos", "date_naissance"
		),
		'champs_versionnes' => array(
			"nom",  "race", "robe", "infos",  "date_naissance", 
		)
	);
	
	return $tables;
}



function chats_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['chats'] = 'chats';
	return $interfaces;
}



?>
