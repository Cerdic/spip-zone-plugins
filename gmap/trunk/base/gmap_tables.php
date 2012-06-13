<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

// Déclaration de la table principale (utilisée dans declarer_tables_objets_sql et
// declarar_tables_principales)
function _gmap_table_geopoints() {
	return array(
		'principale' 			=> 'oui',
		
		'table_objet'		 	=> 'geopoints',
		'type' 					=> 'geopoint',
		
		'field'					=> array(
			"id_geopoint" 			=> "bigint(21) NOT NULL AUTO_INCREMENT",
			"id_parent"				=> "bigint(21) DEFAULT '0' NOT NULL",
			"nom"					=> "text NOT NULL",
			"descriptif"			=> "text NOT NULL",
			"date"					=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"longitude" 			=> "double DEFAULT '0.0' NOT NULL",
			"latitude" 				=> "double DEFAULT '0.0' NOT NULL",
			"zoom" 					=> "tinyint(4) DEFAULT '0' NOT NULL",
			"id_type_geopoint"		=> "bigint(21) DEFAULT '0' NOT NULL",
			"tile"					=> "char(20) DEFAULT '' NOT NULL"
		),
		'key'					 => array(
			"PRIMARY KEY"			=> "id_geopoint",
			"KEY id_parent"			=> "id_parent",
			"KEY tile"				=> "tile",
		),
		'join' 					=> array(
			"id_geopoint"			=> "id_geopoint",
			"id_type_geopoint"		=> "id_type_geopoint"
		),
		'tables_jointures'		=> array(
			"id_geopoint"			=> "geopoints_liens",
			"id_type_geopoint"		=> "types_geopoints",
		),

		'titre'					=> "nom AS titre, '' AS lang",
		'date' 					=> 'date',
		'champs_editables' 		=> array('nom', 'descriptif', 'date', 'longitude', 'latitude', 'zoom'),
		
		'rechercher_champs' 	=> array('nom' => 8, 'descriptif' => 5),

		'url_voir' 				=> 'geopoint',
		'url_edit' 				=> 'geopoint_edit',
		'page'					=> '',
		
		'icone_objet'			=> 'geopoint',
		'texte_retour'			=> 'icone_retour',
		'texte_objets' 			=> 'gmap:geopoints',
		'texte_objet' 			=> 'gmap:geopoint',
		'texte_modifier'		=> 'gmap:icone_modifier_geopoint',
		'texte_creer' 			=> 'gmap:icone_creer_geopoint',
		'texte_ajouter' 		=> 'gmap:titre_ajouter_un_point',
		'texte_creer_associer'	=> 'gmap:creer_et_associer_un_point',
		'info_aucun_objet'		=> 'gmap:info_aucun_geopoint',
		'info_1_objet' 			=> 'gmap:info_1_geopoint',
		'info_nb_objets' 		=> 'gmap:info_nb_geopoints',
		'texte_logo_objet'		=> 'gmap:logo_geopoint',
		
	);
}


// Pipeline declarer_tables_objets_sql pour déclarer toutes les tables en SPIP 3 
// Cf. http://www.spip.net/fr_article5525.html
// Cf. code SPIP : ecrire/base/objets.php
function gmap_declarer_tables_objets_sql($tables) {

	//// La table principale des points
	
	$tables['spip_geopoints'] = _gmap_table_geopoints();


	// Les points peuvent se mettre sur tous les objets, donc tout le monde
	// peu faire une jointure sur geopoints_liens
    $tables[]['tables_jointures'][]= 'geopoints_liens';
	
	return $tables;
}

// Pipeline declarer_tables_principales
// On ne devrait plus en avoir besoin, mais il semble qu'il réclame tout de même...
function gmap_declarer_tables_principales($tables_principales) {
	$tables_principales['spip_geopoints'] = _gmap_table_geopoints();
	return $tables_principales;
}

// Pipeline declarer_tables_auxiliaires
// Normalement, en SPIP 3, on pourrait passer la déclaration de toutes les
// tables dans 
function gmap_declarer_tables_auxiliaires($tables_auxiliaires) {

	//// La table de liaison, également utilisée seule dans la boucle GEOTEST
	
	$tables_auxiliaires['spip_geopoints_liens'] = array(
		'principale' 			=> 'non',
		
		'table_objet'		 	=> 'geopoints_liens',
		'table_objet_surnom' 	=> 'geotest',
		'type' 					=> 'geotest',
		
		'field'					=> array(
			"id_geopoint"			=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"				=> "bigint(21) DEFAULT '0' NOT NULL",
			"objet"					=> "VARCHAR (25) DEFAULT '' NOT NULL"
		),
		'key'					 => array(
			"PRIMARY KEY" 			=> "id_geopoint,id_objet,objet",
			"KEY id_geopoint" 		=> "id_geopoint",
			"KEY id_objet" 			=> "objet,id_objet"
		),
		'join' 					=> array(
			"id_geopoint"			=> "id_geopoint",
		),
		'tables_jointures'		=> array(
			"id_geopoint"			=> "geopoints",
		),
		
	);
	
	
	//// La table des types de pointeurs

	$tables_auxiliaires['spip_types_geopoints'] = array(
		'principale' 			=> 'non',
		
		'table_objet'		 	=> 'types_geopoints',
		'type' 					=> 'type_geopoint',
		
		'field'					=> array(
			"id_type_geopoint"		=> "bigint(21) NOT NULL AUTO_INCREMENT",
			"objet"					=> "varchar(25) DEFAULT '' NOT NULL",
			"nom"					=> "varchar(50) NOT NULL",
			"descriptif"			=> "text DEFAULT '' NOT NULL",
			"visible"				=> "varchar(3) DEFAULT 'oui' NOT NULL",
			"priorite"				=> "tinyint(4) DEFAULT 99 NOT NULL",
		),
		'key'					=> array(
			"PRIMARY KEY"			=> "id_type_geopoint",
			"KEY objet"				=> "objet"
		),
		
	);
	
	return $tables_auxiliaires;
}

// Pipeline declarer_tables_interfaces
// Cf. code SPIP :  ecrire/public/interfaces.php
function gmap_declarer_tables_interfaces($interface) {

    // Nommage de la table
	$interface['table_des_tables']['geopoints'] = 'geopoints';
	$interface['table_des_tables']['geotest'] = 'geopoints_liens';
	$interface['table_des_tables']['types_geopoints'] = 'types_geopoints';
	
	// Aliases des champs obtenus par jointure
	$interface['exceptions_des_tables']['geopoints']['objet'] = array('geopoints_liens', 'objet');
	$interface['exceptions_des_tables']['geopoints']['id_objet'] = array('geopoints_liens', 'id_objet');
	$interface['exceptions_des_tables']['geopoints']['type_point'] = array('types_geopoints', 'nom');
	$interface['exceptions_des_tables']['geopoints']['descriptif_type'] = array('types_geopoints', 'descriptif');
	$interface['exceptions_des_tables']['geopoints']['visible'] = array('types_geopoints', 'visible');
	$interface['exceptions_des_tables']['geopoints']['priorite'] = array('types_geopoints', 'priorite');
	
	return $interface;
}

// Pipeline declarer_tables_objets_surnoms pour bien singulariser les noms composés
function gmap_declarer_tables_objets_surnoms($surnoms) {
	$surnoms['type_geopoint'] = "types_geopoints"; 
	$surnoms['geopoint_lien'] = "geopoints_liens"; 
	$surnoms['geotest'] = "geopoints_liens"; 
	return $surnoms;
}

// Initialisation de la configuration
function gmap_initialize_configuration() {
	include_spip('inc/gmap_config_utils');
	
	// API utilisée
	if ($initGis = charger_fonction("initialiser", "formulaires/configurer_gmap_gis", true))
		call_user_func($initGis);
	else
		gmap_init_config('gmap_api', 'api', 'gma3');
		
	// Paramétrage par défaut de l'API
	if ($iniAPI = charger_fonction("initialiser", "formulaires/configurer_gmap_api", true))
		call_user_func($initAPI);
	
	// Initialiser les zones autorisées
	$iniRUB = charger_fonction('init_rubgeo', 'configuration', true);
	if ($iniRUB)
		$iniRUB();
		
	// Initialiser l'interface dans toutes les APIs
	$iniUI = charger_fonction('init_map_defaults', 'configuration', true);
	if ($iniUI)
		$iniUI();
	$iniMarkersUI = charger_fonction('init_markers_behavior', 'configuration', true);
	if ($iniMarkersUI)
		$iniMarkersUI();

    // Réécrire tous les paramètres
    ecrire_metas();

}

// Ajout des types de documents KML/KMZ s'il n'y sont pas déjà
function gmap_verif_types_documents() {
	include_spip('base/abstract_sql');

    $rowset = sql_select("extension", "spip_types_documents", "extension='kml'");
    if (!$row = sql_fetch($rowset))
		sql_insertq("spip_types_documents", array(
				'titre' => 'Google Earth Placemark',
				'descriptif' => '',
				'extension' => 'kml',
				'mime_type' => 'application/vnd.google-earth.kml+xml',
				'inclus' => 'non',
				'upload' => 'oui',
				'maj' => 'NOW()'));
	sql_free($rowset);
	
    $rowset = sql_select("extension", "spip_types_documents", "extension='kmz'");
    if (!$row = sql_fetch($rowset))
		sql_insertq("spip_types_documents", array(
				'titre' => 'Google Earth Placemark',
				'descriptif' => '',
				'extension' => 'kmz',
				'mime_type' => 'application/vnd.google-earth.kmz',
				'inclus' => 'non',
				'upload' => 'oui',
				'maj' => 'NOW()'));
	sql_free($rowset);
	
}

// Création des types de pointeur par défaut
function gmap_cree_types_defaut() {
	include_spip('inc/gmap_db_utils');
	
	gmap_cree_type("defaut", _T('gmap:marker_def_defaut'), "", "oui", 1);
	gmap_cree_type("centre", _T('gmap:marker_def_centre'), "", "non", 99);
	gmap_cree_type("etape", _T('gmap:marker_def_article_etape'), "article", "oui", 4);
	gmap_cree_type("prise", _T('gmap:marker_def_document_prise'), "document", "oui", 2);
	gmap_cree_type("visee", _T('gmap:marker_def_document_visee'), "document", "oui", 4);
	
}


?>