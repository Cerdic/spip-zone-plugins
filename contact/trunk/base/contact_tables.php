<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function contact_declarer_tables_principales($tables_principales) {	
	// On vérifie si la table n'a pas déjà été déclarée.
	if(!$tables_principales['spip_messages']) {
		// déclaration de la table spip_messages
		$spip_messages = array(
			"id_message"	=> "bigint(21) NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"texte"	=> "longtext DEFAULT '' NOT NULL",
			"type"	=> "varchar(6) DEFAULT '' NOT NULL",
			"date_heure"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_fin"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"rv"	=> "varchar(3) DEFAULT '' NOT NULL",
			"statut"	=> "varchar(6)  DEFAULT '0' NOT NULL",
			"id_auteur"	=> "bigint(21) NOT NULL",
			"maj"	=> "TIMESTAMP");

		$spip_messages_key = array(
			"PRIMARY KEY"	=> "id_message",
			"KEY id_auteur"	=> "id_auteur");
		
		$tables_principales['spip_messages'] = array(
			'field' => &$spip_messages,
			'key' => &$spip_messages_key);
	}
	return $tables_principales;
}


function contact_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['messages'] = 'messages';
	$interface['tables_jointures']['spip_messages'][] = 'messages';
	return $interface;
}

?>
