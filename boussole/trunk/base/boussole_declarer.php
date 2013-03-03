<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function boussole_declarer_tables_principales($tables_principales) {

	// Tables des boussoles : spip_boussoles
	$boussoles = array(
		"id_site"		=> "bigint(21) NOT NULL",
		"id_syndic"		=> "bigint(21) DEFAULT 0 NOT NULL",
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

	// Tables des informations logos et traductions pour les boussoles : spip_boussoles_extras
	$boussoles_extras = array(
		"aka_boussole"	=> "varchar(32) DEFAULT '' NOT NULL",
		"type_objet"	=> "varchar(8) DEFAULT '' NOT NULL",
		"aka_objet"		=> "varchar(32) DEFAULT '' NOT NULL",
		"nom_objet"		=> "text DEFAULT '' NOT NULL",
		"slogan_objet"	=> "text DEFAULT '' NOT NULL",
		"descriptif_objet"	=> "text DEFAULT '' NOT NULL",
		"logo_objet"	=> "varchar(255) DEFAULT '' NOT NULL", // Attention à utiliser avec #CHAMP_SQL{logo_objet}
		"maj"			=> "timestamp");

	$boussoles_extras_key = array(
		"PRIMARY KEY"	=> "aka_boussole, type_objet, aka_objet"
	);

	$tables_principales['spip_boussoles_extras'] =
		array('field' => &$boussoles_extras, 'key' => &$boussoles_extras_key);

	return $tables_principales;
}


function boussole_declarer_tables_interfaces($interface) {
	// Les tables
	$interface['table_des_tables']['boussoles'] = 'boussoles';
	$interface['table_des_tables']['boussoles_extras'] = 'boussoles_extras';

	// Les traitements
	$interface['table_des_traitements']['URL_SITE']['boussoles']= 'safehtml(vider_url(%s))';
	$interface['table_des_traitements']['SLOGAN']['boussoles_extras'] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['DESCRIPTION']['boussoles_extras'] = _TRAITEMENT_RACCOURCIS;

	return $interface;
}

?>
