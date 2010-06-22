<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;
	
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;
	global $table_des_tables;  

// table spip_imapdepart
	$spip_imap_departements = array(
		"id_departement" => "bigint(21) NOT NULL auto_increment",
		"num_departement" => "tinytext NOT NULL",
		"nom" => "text NOT NULL",
		"region" => "text NOT NULL",
		"nom_web" => "text NOT NULL",
		"coordonnees" => "text NOT NULL"      
	);

	$spip_imap_departements_key = array(
		"PRIMARY KEY" => "id_departement"
	);

	$tables_principales['spip_imap_departements'] = array(
		'field' => &$spip_imap_departements,
		'key' => &$spip_imap_departements_key
	);

	$table_des_tables['imap_departements'] = 'imap_departements';

?>