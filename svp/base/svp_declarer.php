<?php

function svp_declarer_tables_principales($tables_principales) {

	// Tables des depots : spip_depots
	$depots = array(
		"id_depot"		=> "bigint(21) NOT NULL",
		"titre"			=> "text DEFAULT '' NOT NULL",
		"descriptif"	=> "text DEFAULT '' NOT NULL",
		"type" 			=> "varchar(10) DEFAULT '' NOT NULL",
		"url_serveur"	=> "varchar(255) DEFAULT '' NOT NULL", // url du serveur svn ou git
		"url_archives"	=> "varchar(255) DEFAULT '' NOT NULL", // url de base des zips
		"xml_paquets"	=> "varchar(255) DEFAULT '' NOT NULL", // chemin complet du fichier xml du depot
		"sha_paquets"	=> "varchar(40) DEFAULT '' NOT NULL",
		"nbr_paquets" 	=> "integer DEFAULT 0 NOT NULL",
		"nbr_plugins" 	=> "integer DEFAULT 0 NOT NULL",
		"nbr_autres" 	=> "integer DEFAULT 0 NOT NULL", // autres contributions, non plugin
		"maj"			=> "timestamp");

	$depots_key = array(
		"PRIMARY KEY"	=> "id_depot"
	);

	$tables_principales['spip_depots'] =
		array('field' => &$depots, 'key' => &$depots_key);

	// Tables des plugins : spip_plugins
	$plugins = array(
		"id_plugin"		=> "bigint(21) NOT NULL",
		"prefixe"		=> "varchar(30) DEFAULT '' NOT NULL",
		"nom"			=> "text DEFAULT '' NOT NULL",
		"slogan"		=> "text DEFAULT '' NOT NULL",
		"categorie"		=> "varchar(100) DEFAULT '' NOT NULL",
		"tags"			=> "text DEFAULT '' NOT NULL",
		"nbr_sites" 	=> "integer DEFAULT 0 NOT NULL",
		"popularite"	=> "double DEFAULT '0' NOT NULL",
		"vmax"			=> "varchar(24) DEFAULT '' NOT NULL", // version la plus elevee des paquets du plugin
		"date_crea"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // la plus ancienne des paquets du plugin
		"date_modif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // la plus recente des paquets du plugin
		"version_spip"	=> "varchar(24) DEFAULT '' NOT NULL", // union des intervalles des paquets du plugin
	);
	
	$plugins_key = array(
		"PRIMARY KEY"	=> "id_plugin",
		"KEY"	=> "prefixe"
	);

	$tables_principales['spip_plugins'] =
		array('field' => &$plugins, 'key' => &$plugins_key);

	// Tables des paquets : spip_paquets
	$paquets = array(
		"id_paquet"		=> "bigint(21) NOT NULL",
		"id_plugin"		=> "bigint(21) NOT NULL",
		"logo"			=> "varchar(255) DEFAULT '' NOT NULL", // chemin du logo depuis la racine du plugin
		"version"		=> "varchar(24) DEFAULT '' NOT NULL",
		"version_base"	=> "varchar(24) DEFAULT '' NOT NULL",
		"version_spip"	=> "varchar(24) DEFAULT '' NOT NULL",
		"description"	=> "text DEFAULT '' NOT NULL",
		"auteur"		=> "text DEFAULT '' NOT NULL",
		"licence"		=> "text DEFAULT '' NOT NULL",
		"lien"			=> "text DEFAULT '' NOT NULL", // lien vers la documentation
		"etat"			=> "varchar(16) DEFAULT '' NOT NULL",
		"etatnum"		=> "int(1) DEFAULT 0 NOT NULL", // 0 aucune indication - 1 exp - 2 dev - 3 test - 4 stable
		"dependances"	=> "text DEFAULT '' NOT NULL",
		"date_crea"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_modif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"id_depot"		=> "bigint(21) DEFAULT 0 NOT NULL",
		"nom_archive"	=> "VARCHAR(255) DEFAULT '' NOT NULL", // nom du zip du paquet, depuis l'adresse de la zone
		"nbo_archive"	=> "integer DEFAULT 0 NOT NULL", // taille de l'archive en octets
		"maj_archive"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // date de mise a jour de l'archive
		"src_archive"	=> "VARCHAR(255) DEFAULT '' NOT NULL", // source de l'archive sur le depot
		"traductions"	=> "text DEFAULT '' NOT NULL" // tableau serialise par module des langues traduites et de leurs traducteurs
	);
	
	$paquets_key = array(
		"PRIMARY KEY"	=> "id_paquet"
	);

	$tables_principales['spip_paquets'] =
		array('field' => &$paquets, 'key' => &$paquets_key);

	return $tables_principales;
}


function svp_declarer_tables_auxiliaires($tables_auxiliaires) {
	// Tables de liens entre plugins et depots : spip_depots_plugins
	$spip_depots_plugins = array(
		"id_depot"	=> "bigint(21) NOT NULL",
		"id_plugin"	=> "bigint(21) NOT NULL"
	);

	$spip_depots_plugins_key = array(
		"PRIMARY KEY" 	=> "id_depot, id_plugin"
	);

	$tables_auxiliaires['spip_depots_plugins'] = 
		array('field' => &$spip_depots_plugins, 'key' => &$spip_depots_plugins_key);

	return $tables_auxiliaires;
}


function svp_declarer_tables_interfaces($interface) {
	// Les tables
	$interface['table_des_tables']['depots'] = 'depots';
	$interface['table_des_tables']['plugins'] = 'plugins';	
	$interface['table_des_tables']['paquets'] = 'paquets';	
	$interface['table_des_tables']['depots_plugins'] = 'depots_plugins';	

	// Les traitements
	// - table spip_plugins
	$interface['table_des_traitements']['SLOGAN']['plugins'] = _TRAITEMENT_RACCOURCIS;	
	// - table spip_paquets
	$interface['table_des_traitements']['DESCRIPTION']['paquets'] = _TRAITEMENT_RACCOURCIS;	
	$interface['table_des_traitements']['LIEN']['paquets'] = _TRAITEMENT_RACCOURCIS;	
	
	// Les jointures
	// -- Entre spip_depots et spip_plugins
	$interface['tables_jointures']['spip_plugins'][] = 'depots_plugins';
	$interface['tables_jointures']['spip_depots'][] = 'depots_plugins';
	// -- Entre spip_paquets et spip_plugins
	$interface['tables_jointures']['spip_plugins'][] = 'paquets';
	$interface['tables_jointures']['spip_paquets'][] = 'plugins';

	// Titre pour url des objets plugin et depot
	$interface['table_titre']['depots'] = "titre, '' AS lang";
	$interface['table_titre']['plugins'] = "nom, '' AS lang";

	return $interface;
}


function svp_rechercher_liste_des_champs($tables) {
	// On déclare les champs de recherche dans les tables plugins et paquets
	// -- Table spip_plugins
	$tables['plugin']['prefixe'] = 8;
	$tables['plugin']['nom'] = 8;
	$tables['plugin']['slogan'] = 4;
	// -- Table spip_paquets
	$tables['paquet']['description'] = 2;
	$tables['paquet']['auteur'] = 1;

	return $tables;
}

function svp_declarer_url_objets($objets){
	// On déclare url d'objet plugin et depot
	$objets[] = 'depot';
	$objets[] = 'plugin';
	return $objets;
}

?>
