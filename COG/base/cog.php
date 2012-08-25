<?php



function cog_declarer_tables_principales($tables_principales){

//////////   Communes   //////////

	$table_cog = array(
		"id_cog_commune"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant de la commune'",
		"decoupage_cantons"	=> "TINYINT(1) NOT NULL COMMENT 'Découpage de la commune en cantons'",
		"chef_lieu"=> "TINYINT(1) NOT NULL DEFAULT '0'	COMMENT 'Chef-lieu de canton, d\'arrondissement, de département, de région'",
		"region"=> "TINYINT(2) UNSIGNED NOT NULL	COMMENT 'Code région'",
		"departement"=> "VARCHAR( 3 ) NOT NULL COMMENT 'Code département'",
		"code"=> "SMALLINT( 3 ) UNSIGNED ZEROFILL NOT NULL	COMMENT 'Code commune'",
		"arrondissement"=> "TINYINT( 1 ) UNSIGNED NOT NULL	COMMENT 'Code arrondissement'",
		"canton"=> "TINYINT( 2 ) UNSIGNED NOT NULL	COMMENT 'Code canton'",
		"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
		"article_majuscule"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (majuscules)'",
		"nom_majuscule"=> "text NOT NULL	COMMENT 'Nom en clair (majuscules)'",
		"article"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (typographie riche)'",
		"nom"=> "text NOT NULL COMMENT 'Nom en clair (typographie riche)'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "id_cog_commune",
		"KEY region" 	=> "region",
		"KEY departement" 	=> "departement",
		"KEY insee" 	=> "departement,code"
		);

	$table_cog_join = array(
		"id_cog_commune"=>'id_cog_commune'
	);


	$tables_principales['spip_cog_communes'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key,
		'join' => $table_cog_join
		);


//////////   Cantons   //////////

	$table_cog = array(


		"id_cog_canton"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant du canton'",
		"region"=> "TINYINT(2) UNSIGNED NOT NULL	COMMENT 'Code région'",
		"departement"=> "VARCHAR( 3 ) NOT NULL COMMENT 'Code département'",
		"arrondissement"=> "TINYINT( 1 ) UNSIGNED NOT NULL	COMMENT 'Code arrondissement'",
		"code"=> "TINYINT( 2 ) UNSIGNED ZEROFILL NOT NULL	COMMENT 'Code canton'",
		"type_canton"=> "TINYINT( 2 ) UNSIGNED NOT NULL	COMMENT 'Composition communale du canton'",
		"chef_lieu"=> "SMALLINT(5) NOT NULL DEFAULT '0'	COMMENT 'Chef-lieu de canton'",
		"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
		"article_majuscule"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (majuscules)'",
		"nom_majuscule"=> "text NOT NULL	COMMENT 'Nom en clair (majuscules)'",
		"article"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (typographie riche)'",
		"nom"=> "text NOT NULL COMMENT 'Nom en clair (typographie riche)'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "id_cog_canton",
		"KEY region" 	=> "region",
		"KEY departement" 	=> "departement",
		);


	$tables_principales['spip_cog_cantons'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key
		);






//////////   Arrondissements   //////////

	$table_cog = array(
		"id_cog_arrondissement"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant de l\'arrondissement'",
		"region"=> "TINYINT( 2 ) UNSIGNED NOT NULL	COMMENT 'Code région'",
		"departement"=> "VARCHAR( 3 )  NOT NULL COMMENT 'Code département'",
		"code"=> "TINYINT( 1 ) UNSIGNED NOT NULL COMMENT 'Code arrondissement'",
		"chef_lieu"=> "VARCHAR(5) NOT NULL  COMMENT 'Code de la commune chef-lieu'",
		"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
		"article_majuscule"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (majuscules)'",
		"nom_majuscule"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Nom en clair (majuscules)'",
		"article"=> "VARCHAR( 5 ) NOT NULL	COMMENT 'Article (typographie riche)'",
		"nom"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Nom en clair (typographie riche)'");


	$table_cog_key = array(
		"PRIMARY KEY" 	=> "id_cog_arrondissement",
		"KEY REGION" 	=> "region",
		"KEY DEP" 	=> "departement",
		"KEY code" 	=> "code"

		);


	$tables_principales['spip_cog_arrondissements'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key);


//////////   Départements   //////////

	$table_cog = array(
		"id_cog_departement"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant du departement'",
		"region"=> "TINYINT( 2 )  UNSIGNED NOT NULL	COMMENT 'Code région'",
		"code"=> "VARCHAR( 3 )  NOT NULL COMMENT 'Code département'",
		"chef_lieu"=> "VARCHAR(5) NOT NULL 	COMMENT 'Code de la commune chef-lieu'",
		"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
		"nom_majuscule"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Nom en clair (majuscules)'",
		"nom"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Nom en clair (typographie riche)'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "id_cog_departement"
		);

	$tables_principales['spip_cog_departements'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key);


//////////   Région   //////////

	$table_cog = array(
		"id_cog_region"	=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant du region'",
		"code"=> "TINYINT ( 2 )  UNSIGNED NOT NULL	COMMENT 'Code région'",
		"chef_lieu"=> "VARCHAR(5) NOT NULL  DEFAULT '0'	COMMENT 'Code de la commune chef-lieu'",
		"type_charniere"=> "TINYINT( 1 ) UNSIGNED NOT NULL		COMMENT 'Type de nom en clair'",
		"nom_majuscule"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Nom en clair (majuscules)'",
		"nom"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Nom en clair (typographie riche)'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "id_cog_region"
		);

	$tables_principales['spip_cog_regions'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key);


//////////   EPCI  //////////

	$table_cog = array(
		"id_cog_epci"	=> "INT(9) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'identifiant de l\'EPCI'",
		"code"		=> "INT(9) UNSIGNED NOT NULL COMMENT 'code de l\'EPCI'",
		"nature"	=> "VARCHAR(3) NOT NULL COMMENT 'nature de l\'EPCI Clé étrangère'",
		"libelle"	=> "VARCHAR(70) NOT NULL COMMENT 'Libellé'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "id_cog_epci"
		);

	$tables_principales['spip_cog_epcis'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key);


	$table_cog = array(
		"code"		=> "VARCHAR( 3 )  NOT NULL	COMMENT 'code des nature des EPCI'",
		"libelle"	=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Libellé'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "code"
		);

	$tables_principales['spip_cog_epcis_natures'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key);

//////////   ZAUER  //////////


	$table_cog = array(

		"code"=> "TINYINT( 3 ) UNSIGNED NOT NULL COMMENT 'Code de l\'espace urbain'",
		"libelle"=> "VARCHAR( 70 ) NOT NULL	COMMENT 'Libellé'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "code"
		);

	$tables_principales['spip_cog_zauers_eus'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key);



	$table_cog = array(
		"id_cog_zauer"=> "INT( 9 ) UNSIGNED NOT NULL AUTO_INCREMENT	COMMENT 'identifiant de l\'ZAUER'",
		"categorie"=> "tinyint(1) UNSIGNED NOT NULL COMMENT 'Catégorie de la ZAUER Clé étrangère'",
		"code"=> "SMALLINT( 3 ) UNSIGNED NOT NULL	 COMMENT 'Code de la ZAUER'",
		"libelle"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Libellé'",
		"espace_urbain"=> "TINYINT( 3 ) UNSIGNED NOT NULL	COMMENT 'Espace urbain'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "id_cog_zauer"
		);

	$tables_principales['spip_cog_zauers'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key);


	$table_cog = array(
		"code"=> "tinyint(1) UNSIGNED NOT NULL COMMENT 'code des nature des ZAUER'",
		"libelle"=> "VARCHAR( 70 ) NOT NULL COMMENT 'Libellé'");

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "code"
		);

	$tables_principales['spip_cog_zauers_categories'] = array(
		'field' => $table_cog,
		'key' => $table_cog_key);



return $tables_principales;

}




function cog_declarer_tables_auxiliaires($tables_auxiliaires){



//////////   Communes liens   //////////

	$table_cog = array(
		"id_cog_commune"=> "INT(10) UNSIGNED NOT NULL COMMENT 'id_cog_commune'",
		"objet"=> "VARCHAR( 25 ) NOT NULL	COMMENT 'Nom du type d\'objets '",
		"id_objet"=> "BIGINT( 21 ) NOT NULL COMMENT 'identifiant de l\'objet'",
		);

	$table_cog_key = array(
		"PRIMARY KEY" 	=> "id_cog_commune, objet, id_objet",
		"KEY id_objet" 	=> "id_cog_commune"
		);



	$tables_auxiliaires['spip_cog_communes_liens'] = array(
		'field' => &$table_cog,
		'key' => &$table_cog_key,
		);

return $tables_auxiliaires;
}




 function cog_declarer_tables_interfaces($tables_interfaces){

$tables_interfaces['table_des_tables']['cog_communes'] = 'cog_communes';

$tables_interfaces['table_des_tables']['cog_communes_liens'] = 'cog_communes_liens';
$tables_interfaces['table_des_tables']['cog_epcis'] = 'cog_epcis';
$tables_interfaces['table_des_tables']['cog_arrondissements'] = 'cog_arrondissements';
$tables_interfaces['table_des_tables']['cog_departements'] = 'cog_departements';
$tables_interfaces['table_des_tables']['cog_regions'] = 'cog_regions';
$tables_interfaces['tables_jointures']['spip_cog_communes'][] = 'cog_communes_liens';
$tables_interfaces['tables_jointures']['spip_cog_communes_liens'][] = 'cog_communes';
$tables_interfaces['tables_jointures']['spip_articles'][] = 'cog_communes_liens';
$tables_interfaces['tables_jointures']['spip_rubriques'][] = 'cog_communes_liens';


 return  $tables_interfaces;
}

?>