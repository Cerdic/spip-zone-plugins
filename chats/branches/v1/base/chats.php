<?php

function chats_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['chats'] = 'chats';	
	$interface['table_des_traitements']['RACE']['chats'] = _TRAITEMENT_TYPO; // corrections de francais
	$interface['table_des_traitements']['INFOS']['chats'] = _TRAITEMENT_RACCOURCIS; // + raccourcis spip
	return $interface;
}


function chats_declarer_tables_principales($tables_principales){
	//-- Table CHATS ------------------------------------------
	$chats = array(
			"id_chat"	=> "bigint(21) NOT NULL",
			"race"	=> "tinytext DEFAULT '' NOT NULL",
			"nom"	=> "tinytext DEFAULT '' NOT NULL",
			"annee_naissance"	=> "int(4) DEFAULT '0' NOT NULL",
			"robe"	=> "tinytext DEFAULT '' NOT NULL",
			"infos"	=> "text DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP"
			);
	
	$chats_key = array(
			"PRIMARY KEY"	=> "id_chat",
			);
	
	$tables_principales['spip_chats'] =
		array('field' => &$chats, 'key' => &$chats_key);

	return $tables_principales;
}