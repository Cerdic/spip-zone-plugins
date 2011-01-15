<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;
	
	global $tables_principales;
	global $table_des_tables;  

// table spip_adherents
	$spip_fichiers = array(
		"id_adherent" => "bigint(21) NOT NULL auto_increment",
		"id_auteur" => "bigint(21) NOT NULL",
		"maj" => "timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP",
		"no_adherent" => "bigint(21) NOT NULL",
		"adresse_postale" => "tinytext NOT NULL",
		"code_postal" => "text NOT NULL",
		"localite" => "tinytext NOT NULL",
		"telephone" => "varchar(100) NOT NULL",
		"gsm" => "varchar(100) NOT NULL",
		"prenom" => "tinytext NOT NULL",
		"nom_adherent" => "tinytext NOT NULL",
		"sexe" => "varchar(1) NOT NULL",
		"cotisation" => "varchar(4) NOT NULL",
		"statut_adherent" => "varchar(1) NOT NULL",
	);

	$spip_fichiers_key = array(
		"PRIMARY KEY" => "id_adherent",
		"KEY" => "statut_adherent",
		"KEY" => "id_auteur",
		"KEY" => "no_adherent"
	);

	$tables_principales['spip_adherents'] = array(
		'field' => &$spip_adherents,
		'key' => &$spip_adherents_key
	);

	$table_des_tables['adherents'] = 'adherents';

?>
