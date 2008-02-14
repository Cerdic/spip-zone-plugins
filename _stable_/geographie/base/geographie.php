<?php
include_spip('base/serial');
$spip_geo_pays = array(
		"id_pays"	=> "smallint NOT NULL",
		"nom"	=> "text DEFAULT '' NOT NULL",
);
$spip_geo_pays_key = array(
		"PRIMARY KEY"		=> "id_pays"
);
$spip_geo_regions = array(
		"id_region"	=> "smallint NOT NULL",
		"id_pays"	=> "smallint NOT NULL",
		"nom"	=> "tinytext DEFAULT '' NOT NULL",
);
$spip_geo_regions_key = array(
		"PRIMARY KEY"		=> "id_region"
);
$spip_geo_departements = array(
		"id_departement"	=> "smallint NOT NULL",
		"abbr"	=> "varchar(5) default '' NOT NULL",
		"id_region"	=> "smallint NOT NULL",
		"nom"	=> "tinytext DEFAULT '' NOT NULL",
);
$spip_geo_departements_key = array(
		"PRIMARY KEY"		=> "id_departement"
);

$spip_geo_communes = array(
		"id_commune"	=> "char(6) default '' NOT NULL",
		"id_departement"	=> "smallint NOT NULL",
		"id_pays"	=> "smallint NOT NULL",
		"code_postal"	=> "char(5) default '' NOT NULL",
		"nom"	=> "tinytext DEFAULT '' NOT NULL",
		"longitude"	=> "varchar(15) default '' NOT NULL",
		"latitude"	=> "varchar(15) default '' NOT NULL",
);
$spip_geo_communes_key = array(
		"PRIMARY KEY"		=> "id_commune",
		"INDEX id_pays"		=> "id_pays"
);

global $tables_principales;
$tables_principales['spip_geo_pays'] = array('field'=>&$spip_geo_pays,'key'=>$spip_geo_pays_key);
$tables_principales['spip_geo_regions'] = array('field'=>&$spip_geo_regions,'key'=>$spip_geo_regions_key);
$tables_principales['spip_geo_departements'] = array('field'=>&$spip_geo_departements,'key'=>$spip_geo_departements_key);
$tables_principales['spip_geo_communes'] = array('field'=>&$spip_geo_communes,'key'=>$spip_geo_communes_key);

global $table_des_tables;
$table_des_tables['geo_pays'] = 'geo_pays';
$table_des_tables['geo_regions'] = 'geo_regions';
$table_des_tables['geo_departements'] = 'geo_departements';
$table_des_tables['geo_communes'] = 'geo_communes';


?>