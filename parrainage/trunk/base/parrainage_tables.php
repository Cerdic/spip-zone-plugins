<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function parrainage_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['filleuls'] = 'filleuls';
	
	$interface['table_date']['filleuls'] = 'date_invitation';
	
	$interface['table_titre']['filleuls'] = 'nom as titre, "" as lang';
	
	return $interface;
}

function parrainage_declarer_tables_principales($tables_principales){
	//-- Table filleuls -----------------------------------------------------------
	$filleuls = array(
		'id_filleul' => 'bigint(21) NOT NULL',
		'email' => 'tinytext not null default ""',
		'nom' => 'text not null default ""',
		'id_parrain' => 'bigint(21) NOT NULL default 0',
		'id_auteur' => 'bigint(21) NOT NULL default 0',
		'statut' => 'varchar(255) not null default ""',
		'date_invitation' => 'datetime not null default "0000-000-00 00:00:00"',
		'code_invitation' => 'varchar(255) not null default ""'
	);
	
	$filleuls_cles = array(
		'PRIMARY KEY' => 'id_filleul',
		'KEY id_parrain' => 'id_parrain',
		'KEY id_auteur' => 'id_auteur'
	);
	
	$tables_principales['spip_filleuls'] = array(
		'field' => &$filleuls,
		'key' => &$filleuls_cles,
		'join'=> array(
			'id_filleul' => 'id_filleul',
			'id_auteur' => 'id_auteur',
			'id_parrain' => 'id_auteur',
		)
	);

	return $tables_principales;
}

function parrainage_rechercher_liste_des_champs($tables){
	$tables['filleul']['nom'] = 4;
	$tables['filleul']['email'] = 3;
	return $tables;
}

?>
