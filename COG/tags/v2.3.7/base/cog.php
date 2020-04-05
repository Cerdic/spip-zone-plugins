<?php



function cog_declarer_tables_objets_sql($tables){

//////////   Communes   //////////

$tab_champs=array('decoupage_cantons','chef_lieu','region','departement','code','arrondissement',
'canton','type_charniere','article_majuscule','nom_majuscule','article','nom');

$tables['spip_cog_communes'] = array(

		'type' => 'cog_commune',
		'principale' => "oui",
		'table_objet_surnoms' => array('cogcommune'), // table_objet('cog_commune') => 'cog_communes' 
		'field'=> array(
			"id_cog_commune"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant de la commune'",
			"decoupage_cantons"	=> "TINYINT(1) NOT NULL COMMENT 'Découpage de la commune en cantons'",
			"chef_lieu"			=> "TINYINT(1) NOT NULL DEFAULT '0'	COMMENT 'Chef-lieu de canton, d\'arrondissement, de département, de région'",
			"region"			=> "TINYINT(2) UNSIGNED NOT NULL	COMMENT 'Code région'",
			"departement"		=> "VARCHAR( 3 ) NOT NULL COMMENT 'Code département'",
			"code"				=> "SMALLINT( 3 ) UNSIGNED ZEROFILL NOT NULL	COMMENT 'Code commune'",
			"arrondissement"	=> "TINYINT( 1 ) UNSIGNED NOT NULL	COMMENT 'Code arrondissement'",
			"canton"			=> "TINYINT( 2 ) UNSIGNED NOT NULL	COMMENT 'Code canton'",
			"type_charniere"	=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
			"article_majuscule"	=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (majuscules)'",
			"nom_majuscule"		=> "text NOT NULL	COMMENT 'Nom en clair (majuscules)'",
			"article"			=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (typographie riche)'",
			"nom"=> "text NOT NULL COMMENT 'Nom en clair (typographie riche)'",
			"maj"                => "TIMESTAMP"		
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_cog_commune",
			"KEY region" 	=> "region",
			"KEY departement" 	=> "departement",
			"KEY insee" 	=> "departement,code"
		),
		'titre' => "nom AS titre, '' AS lang",
'page'=>'',
		#'date' => "",
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array('nom' => 8, 'code' => 5, 'departement' => 1	),
		'tables_jointures' => array('spip_cog_communes_liens')

	);



//////////   Cantons   //////////

$tab_champs=array('region','departement','arrondissement','code','type_canton',
			'chef_lieu','type_charniere','article_majuscule','nom_majuscule',
			'article','nom');

$tables['spip_cog_cantons'] = array(
		'type' => 'cog_canton',
		'principale' => "oui",
		'table_objet_surnoms' => array('cogcanton'), // table_objet('cog_canton') => 'cog_cantons' 
		'field'=> array(
			"id_cog_canton"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant du canton'",
			"region"=> "TINYINT(2) UNSIGNED NOT NULL	COMMENT 'Code région'",
			"departement"=> "VARCHAR( 3 ) NOT NULL COMMENT 'Code département'",
			"arrondissement"=> "TINYINT( 1 ) UNSIGNED NOT NULL	COMMENT 'Code arrondissement'",
			"code"=> "TINYINT( 2 ) UNSIGNED ZEROFILL NOT NULL	COMMENT 'Code canton'",
			"type_canton"=> "TINYINT( 2 ) UNSIGNED NOT NULL	COMMENT 'Composition communale du canton'",
			"chef_lieu"=> "MEDIUMINT(5) NOT NULL DEFAULT '0'	COMMENT 'Chef-lieu de canton'",
			"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
			"article_majuscule"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (majuscules)'",
			"nom_majuscule"=> "text NOT NULL	COMMENT 'Nom en clair (majuscules)'",
			"article"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (typographie riche)'",
			"nom"=> "text NOT NULL COMMENT 'Nom en clair (typographie riche)'",
			"maj"                => "TIMESTAMP"		
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_cog_canton",
			"KEY region" 	=> "region",
			"KEY departement" 	=> "departement"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'nom' => 8, 'code' => 5, 'departement' => 1
		),
		'titre' => "nom AS titre, '' as lang",
		'page'=>''
	);


//////////   Arrondissements   //////////

$tab_champs=array('region','departement','code',
			'chef_lieu','type_charniere','article_majuscule','nom_majuscule',
			'article','nom');

$tables['spip_cog_arrondissements'] = array(
		'type' => 'cog_arrondissement',
		'principale' => "oui",
		'table_objet_surnoms' => array('cogarrondissement'), // table_objet('cog_arrondissement') => 'cog_arrondissements' 
		
		'field'=> array(
			"id_cog_arrondissement"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant de l\'arrondissement'",
			"region"=> "TINYINT( 2 ) UNSIGNED NOT NULL	COMMENT 'Code région'",
			"departement"=> "VARCHAR( 3 )  NOT NULL COMMENT 'Code département'",
			"code"=> "TINYINT( 1 ) UNSIGNED NOT NULL COMMENT 'Code arrondissement'",
			"chef_lieu"=> "VARCHAR(5) NOT NULL  COMMENT 'Code de la commune chef-lieu'",
			"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
			"article_majuscule"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (majuscules)'",
			"nom_majuscule"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Nom en clair (majuscules)'",
			"article"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (typographie riche)'",
			"nom"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Nom en clair (typographie riche)'",
			"maj"                => "TIMESTAMP"		
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_cog_arrondissement",
			"KEY REGION" 	=> "region",
			"KEY DEP" 	=> "departement",
			"KEY code" 	=> "code"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'nom' => 8, 'code' => 5, 'departement' => 1
		),
		'titre' => "nom AS titre, '' as lang",
		'page'=>''
	);


//////////   Départements   //////////

$tab_champs=array('region','code',
			'chef_lieu','type_charniere','nom_majuscule','nom');

$tables['spip_cog_departements'] = array(

		'type' => 'cog_departement',
		'principale' => "oui",
		'table_objet_surnoms' => array('cogdepartement'), // table_objet('cog_departement') => 'cog_departements' 
		'field'=> array(
			"id_cog_departement"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant du departement'",
			"region"=> "TINYINT( 2 )  UNSIGNED NOT NULL	COMMENT 'Code région'",
			"code"=> "VARCHAR( 3 )  NOT NULL COMMENT 'Code département'",
			"chef_lieu"=> "VARCHAR(5) NOT NULL 	COMMENT 'Code de la commune chef-lieu'",
			"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
			"nom_majuscule"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Nom en clair (majuscules)'",
			"nom"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Nom en clair (typographie riche)'",
			"region_ancienne"=> "TINYINT( 2 )  UNSIGNED NOT NULL	COMMENT 'Code région avant 2016'",
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_cog_departement",
			"KEY REGION" 	=> "region",
			"KEY code" 	=> "code"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'nom' => 8, 'code' => 5, 'region' => 1
		),
		'titre' => "nom AS titre, '' as lang",
		'page'=>''
	);


//////////   Régions   //////////



$tab_champs=array('code',
			'chef_lieu','type_charniere','nom_majuscule','nom');

$tables['spip_cog_regions'] = array(

		'type' => 'cog_region',
		'principale' => "oui",
		'table_objet_surnoms' => array('cogregion'), // table_objet('cog_region') => 'cog_regions' 
		'field'=> array(
			"id_cog_region"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant du region'",
			"code"=> "TINYINT ( 2 )  UNSIGNED NOT NULL	COMMENT 'Code région'",
			"chef_lieu"=> "VARCHAR(5) NOT NULL  DEFAULT '0'	COMMENT 'Code de la commune chef-lieu'",
			"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
			"nom_majuscule"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Nom en clair (majuscules)'",
			"nom"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Nom en clair (typographie riche)'",
			"maj"                => "TIMESTAMP"		
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_cog_region",
			"KEY code" 	=> "code"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'nom' => 8, 'code' => 5
		),
		'titre' => "nom AS titre, '' as lang",
		'page'=>''
	);


//////////   Anciennes Régions   //////////
/*

	$tab_champs=array('code',
		'chef_lieu','type_charniere','nom_majuscule','nom');

	$tables['spip_cog_regions_anciennes'] = array(

		'type' => 'cog_region_ancienne',
		'principale' => "oui",
		'table_objet_surnoms' => array('cogregion'), // table_objet('cog_region') => 'cog_regions'
		'field'=> array(
			"id_cog_region_ancienne"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant du region'",
			"code"=> "TINYINT ( 2 )  UNSIGNED NOT NULL	COMMENT 'Code région'",
			"chef_lieu"=> "VARCHAR(5) NOT NULL  DEFAULT '0'	COMMENT 'Code de la commune chef-lieu'",
			"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
			"nom_majuscule"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Nom en clair (majuscules)'",
			"nom"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Nom en clair (typographie riche)'",
			"region2016"=> "TINYINT ( 2 )  UNSIGNED NOT NULL	COMMENT 'Code région 2016'",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_cog_region_ancienne",
			"KEY code" 	=> "code"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'nom' => 8, 'code' => 5
		),
		'titre' => "nom AS titre, '' as lang",
		'page'=>''
	);


*/

//////////   EPCI  //////////



$tab_champs=array('code','nature','libelle');

$tables['spip_cog_epcis'] = array(

		'type' => 'cog_epci',
		'principale' => "oui",
		'table_objet_surnoms' => array('cogepci'), // table_objet('cog_epci') => 'cog_epcis' 
		'field'=> array(
			"id_cog_epci"	=> "INT(9) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'identifiant de l\'EPCI'",
			"code"		=> "INT(9) UNSIGNED NOT NULL COMMENT 'code de l\'EPCI'",
			"nature"	=> "VARCHAR(3) NOT NULL COMMENT 'nature de l\'EPCI Clé étrangère'",
			"libelle"	=> "VARCHAR(70) NOT NULL COMMENT 'Libellé'"
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_cog_epci"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array('libelle' => 8, 'code' => 5),
		'titre' => "libelle AS titre, '' as lang",
		'page'=>'',
	);



$tab_champs=array('code','libelle');

$tables['spip_cog_epcis_natures'] = array(

		'principale' => "oui",
		'field'=> array(
			"code"		=> "VARCHAR( 3 )  NOT NULL	COMMENT 'code des nature des EPCI'",
			"libelle"	=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Libellé'"
		),
		'key' => array(
			"PRIMARY KEY" 	=> "code"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'libelle' => 8, 'code' => 5
		),
		'titre' => "libelle AS titre, '' as lang",
		'page'=>'',
	);


//////////   ZAUER  //////////

/*




$tab_champs=array('categorie','code','libelle','espace_urbain');

$tables['spip_cog_zauers'] = array(

		'principale' => "oui",
		'field'=> array(
			"id_cog_zauer"=> "INT( 9 ) UNSIGNED NOT NULL AUTO_INCREMENT	COMMENT 'identifiant de l\'ZAUER'",
			"categorie"=> "tinyint(1) UNSIGNED NOT NULL COMMENT 'Catégorie de la ZAUER Clé étrangère'",
			"code"=> "SMALLINT( 3 ) UNSIGNED NOT NULL	 COMMENT 'Code de la ZAUER'",
			"libelle"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Libellé'",
			"espace_urbain"=> "TINYINT( 3 ) UNSIGNED NOT NULL	COMMENT 'Espace urbain'"
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_cog_zauer"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'libelle' => 8, 'code' => 5
		),
		'titre' => "libelle AS titre, '' as lang",
		'page'=>'',
	);

$tab_champs=array('code','libelle');

$tables['spip_cog_zauers_eus'] = array(

		'principale' => "oui",
		'field'=> array(
			"code"=> "TINYINT( 3 ) UNSIGNED NOT NULL COMMENT 'Code de l\'espace urbain'",
			"libelle"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Libellé'"
		),
		'key' => array(
			"PRIMARY KEY" 	=> "code"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'libelle' => 8, 'code' => 5
		),
		'titre' => "libelle AS titre, '' as lang",
		'page'=>'',
	);



	$tab_champs=array('code','libelle');




$tables['spip_cog_zauers_categories'] = array(

		'principale' => "oui",
		'field'=> array(
			"code"=> "tinyint(1) UNSIGNED NOT NULL COMMENT 'code des categories des ZAUER'",
			"libelle"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Libellé'"
		),
		'key' => array(
			"PRIMARY KEY" 	=> "code"
		),
		'champs_editables' => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array(
			'libelle' => 8, 'code' => 5
		),
		'titre' => "libelle AS titre, '' as lang",
		'page'=>'',
	);
*/
return $tables;

}




/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function cog_declarer_tables_auxiliaires($tables) {



//////////   Communes liens   //////////

	$tables['spip_cog_communes_liens'] = array(
		'field' => array(
			"id_cog_commune"     => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_cog_commune,id_objet,objet",
			"KEY id_cog_commune" => "id_cog_commune"
		)
	);

	return $tables;
}




 function cog_declarer_tables_interfaces($tables_interfaces){

$tables_interfaces['table_des_tables']['cog_communes'] = 'cog_communes';
$tables_interfaces['table_des_tables']['cog_cantons'] = 'cog_cantons';
$tables_interfaces['table_des_tables']['cog_communes_liens'] = 'cog_communes_liens';
$tables_interfaces['table_des_tables']['cog_epcis'] = 'cog_epcis';
$tables_interfaces['table_des_tables']['cog_arrondissements'] = 'cog_arrondissements';
$tables_interfaces['table_des_tables']['cog_departements'] = 'cog_departements';
$tables_interfaces['table_des_tables']['cog_regions'] = 'cog_regions';
$tables_interfaces['table_des_tables']['cog_regions_anciennes'] = 'cog_regions_anciennes';
$tables_interfaces['tables_jointures']['spip_cog_communes'][] = 'cog_communes_liens';
$tables_interfaces['tables_jointures']['spip_cog_communes_liens'][] = 'cog_communes';
$tables_interfaces['tables_jointures']['spip_articles'][] = 'cog_communes_liens';
$tables_interfaces['tables_jointures']['spip_rubriques'][] = 'cog_communes_liens';


 return  $tables_interfaces;
}

?>
