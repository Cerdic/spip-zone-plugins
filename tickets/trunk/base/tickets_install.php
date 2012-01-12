<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function tickets_declarer_tables_principales($tables_principales){

	//-- Table tickets ------------------------------------------
	$spip_tickets = array(
			"id_ticket"	=> "bigint(21) NOT NULL",
			"titre"	=> "text NOT NULL",
			"texte"	=> "longtext DEFAULT '' NOT NULL",
			"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_modif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"severite"	=> "integer DEFAULT '0' NOT NULL",
			"tracker"	=> "integer DEFAULT '0' NOT NULL",
			"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
			"id_auteur"	=> "bigint(21) NOT NULL",
			"ip"	=> "varchar(16) DEFAULT '' NOT NULL",
			"id_assigne"	=> "bigint(21) NOT NULL",
			"exemple"	=> "varchar(255) DEFAULT '' NOT NULL",
			"projet"	=> "varchar(60) DEFAULT '' NOT NULL",
			"composant"	=> "varchar(40) DEFAULT '' NOT NULL",
			"version"	=> "varchar(30) DEFAULT '' NOT NULL",
			"jalon"	=> "varchar(30) DEFAULT '' NOT NULL",
			"navigateur" => "varchar(60) DEFAULT '' NOT NULL",
			"sticked" 	=> "varchar(3) DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP"
			);

	$spip_tickets_key = array(
			"PRIMARY KEY"	=> "id_ticket",
			"KEY date_modif"	=> "date_modif",
			"KEY id_auteur"	=> "id_auteur",
			"KEY id_assigne"	=> "id_assigne",
			"KEY statut"	=> "statut, date"
			);


	$tables_principales['spip_tickets'] = array(
		'field' => &$spip_tickets,
		'key' => &$spip_tickets_key);

	return $tables_principales;
}

function tickets_declarer_tables_interfaces($interface){

	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['tickets']='tickets';
	$interfaces['tables_jointures']['spip_tickets'][]= 'documents_liens';
	
	$interface['tables_jointures']['spip_tickets'][] = 'forums';

	return $interface;


}


?>
