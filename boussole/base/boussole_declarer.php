<?php

function boussole_declarer_tables_principales($tables_principales) {

	// Tables des boussoles : spip_boussoles
	$boussoles = array(
		"id_site"		=> "bigint(21) NOT NULL",
		"aka_boussole"	=> "varchar(32) DEFAULT '' NOT NULL",
		"aka_site"		=> "varchar(32) DEFAULT '' NOT NULL",
		"url_site"		=> "varchar(255) DEFAULT '' NOT NULL",
		"aka_groupe"	=> "varchar(32) DEFAULT '' NOT NULL",
		"rang_groupe" 	=> "integer DEFAULT 0 NOT NULL",
		"rang_site" 	=> "integer DEFAULT 0 NOT NULL",
		"affiche"		=> "varchar(3) DEFAULT '' NOT NULL",
		"maj"			=> "timestamp");

	$boussoles_key = array(
		"PRIMARY KEY"	=> "id_site"
	);

	$tables_principales['spip_boussoles'] =
		array('field' => &$boussoles, 'key' => &$boussoles_key);

	return $tables_principales;
}


function boussole_declarer_tables_interfaces($interface) {
	// Les tables
	$interface['table_des_tables']['boussoles'] = 'boussoles';

	// Les traitements
	$interface['table_des_traitements']['URL_SITE']['boussoles']= 'safehtml(vider_url(%s))';

	return $interface;
}

?>
