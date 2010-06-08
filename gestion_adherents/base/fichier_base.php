<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;
	
	global $tables_principales;
	global $table_des_tables;  

// table spip_fichiers
	$spip_fichiers = array(
		"id_fichier" => "bigint(21) NOT NULL auto_increment",
		"id_auteur" => "bigint(21) NOT NULL",
		"maj" => "timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP",
		"no_fichier" => "bigint(21) NOT NULL",
		"adresse_postale" => "tinytext NOT NULL",
		"code_postal" => "text NOT NULL",
		"localite" => "tinytext NOT NULL",
		"telephone" => "varchar(100) NOT NULL",
		"gsm" => "varchar(100) NOT NULL",
		"prenom" => "tinytext NOT NULL",
		"nom_ecolo" => "tinytext NOT NULL",
		"sexe" => "varchar(1) NOT NULL",
		"cotisation" => "varchar(4) NOT NULL",
		"statut_ecolo" => "varchar(1) NOT NULL",
	);

	$spip_fichiers_key = array(
		"PRIMARY KEY" => "id_fichier",
		"KEY" => "statut_ecolo",
		"KEY" => "id_auteur",
		"KEY" => "no_fichier"
	);

	$tables_principales['spip_fichiers'] = array(
		'field' => &$spip_fichiers,
		'key' => &$spip_fichiers_key
	);

	$table_des_tables['fichiers'] = 'fichiers';

?>