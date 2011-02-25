<?php

// TODO : mettre les keys

function step_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['plugins'] = 'plugins';	
	$interface['table_des_tables']['zones_plugins'] = 'zones_plugins';

	$interface['table_des_traitements']['NOM']['plugins'] = _TRAITEMENT_TYPO;	
	$interface['table_des_traitements']['AUTEUR']['plugins'] = _TRAITEMENT_RACCOURCIS;	
	$interface['table_des_traitements']['DESCRIPTION']['plugins'] = _TRAITEMENT_RACCOURCIS;	
	$interface['table_des_traitements']['LIEN']['plugins'] = _TRAITEMENT_RACCOURCIS;	
	return $interface;
}


function step_declarer_tables_principales($tables_principales){

	$zones_plugins = array(
			"id_zone"	=> "bigint(21) NOT NULL",
			"nom"	=> "text DEFAULT '' NOT NULL",
			"descriptif"	=> "text DEFAULT '' NOT NULL",
			"adresse"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"nombre_plugins" => "integer",
			"maj"	=> "TIMESTAMP");

	$zones_plugins_key = array(
			"PRIMARY KEY"	=> "id_zone",
	);

	$tables_principales['spip_zones_plugins']     =
		array('field' => &$zones_plugins, 'key' => &$zones_plugins_key);


	$plugins = array(
			"id_plugin"		=> "bigint(21) NOT NULL",
			"prefixe"		=> "VARCHAR(30) DEFAULT '' NOT NULL",
			"version"		=> "VARCHAR(24) DEFAULT '' NOT NULL",
			"version_base"	=> "VARCHAR(24) DEFAULT '' NOT NULL",
			"nom"			=> "text DEFAULT '' NOT NULL",
			"shortdesc"		=> "text DEFAULT '' NOT NULL",
			"description"	=> "text DEFAULT '' NOT NULL",
			"auteur"		=> "text DEFAULT '' NOT NULL",
			"licence"		=> "text DEFAULT '' NOT NULL",
			"lien"			=> "text DEFAULT '' NOT NULL",
			"etat"			=> "varchar(16) DEFAULT '' NOT NULL",
			"etatnum"		=> "int(1) DEFAULT 0 NOT NULL", // 0 aucune indication - 1 exp - 2 dev - 3 test - 4 stable
			"categorie"		=> "varchar(100) DEFAULT '' NOT NULL",
			"tags"			=> "text DEFAULT '' NOT NULL",
			"dependances"	=> "text DEFAULT '' NOT NULL",
			
			"present"		=> "varchar(3) DEFAULT 'non' NOT NULL", // est present ? oui / non (duplique l'info id_zone un peu)
			"actif"			=> "varchar(3) DEFAULT 'non' NOT NULL", // est actif ? oui / non
			"installe"		=> "varchar(3) DEFAULT 'non' NOT NULL", // est desinstallable ? oui / non
			"recent"		=> "int(2) DEFAULT 0 NOT NULL", // a ete utilise recemment ? > 0 : oui
			
			"maj_version"	=> "VARCHAR(255) DEFAULT '' NOT NULL", // version superieure existante (mise a jour possible)
			"superieur"		=> "varchar(3) DEFAULT 'non' NOT NULL", // superieur : version plus recente disponible (distant) d'un plugin (actif?) existant
			"obsolete"		=> "varchar(3) DEFAULT 'non' NOT NULL", // obsolete : version plus ancienne (locale) disponible d'un plugin local existant

			"logo"			=> "VARCHAR(255) DEFAULT '' NOT NULL", // chemin du logo depuis la racine du plugin
			"constante"		=> "VARCHAR(30) DEFAULT '' NOT NULL", // nom de la constante _DIR_(PLUGINS|EXTENSIONS|PLUGINS_SUPP)
			"dossier"		=> "VARCHAR(255) DEFAULT '' NOT NULL", // chemin du dossier depuis la constante
			"id_zone"		=> "bigint(21) DEFAULT 0 NOT NULL",
			"paquet"		=> "VARCHAR(255) DEFAULT '' NOT NULL", // chemin du zip du paquet, depuis l'adresse de la zone
	);
	
	$plugins_key = array(
			"PRIMARY KEY"	=> "id_plugin",
	);

	$tables_principales['spip_plugins']     =
		array('field' => &$plugins, 'key' => &$plugins_key);

	return $tables_principales;
}


?>
