<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function spip_visuels_declarer_tables_principales($tables_principales){

	$spip_visuels = array(
			"id_visuel"	=> "bigint(21) NOT NULL",
			"extension"	=> "varchar(10) DEFAULT '' NOT NULL",
			"type"	=> "text DEFAULT '' NOT NULL",
			"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"fichier"	=> "text DEFAULT '' NOT NULL",
			"taille"	=> "bigint(20) NOT NULL",
			"largeur"	=> "int(11) NOT NULL",
			"hauteur" => "int(11) NOT NULL",
			"maj"	=> "TIMESTAMP");

	$spip_visuels_key = array(
			"PRIMARY KEY"	=> "id_visuel"
			);

	$tables_principales['spip_visuels']	=
		array('field' => &$spip_visuels, 'key' => &$spip_visuels_key);

	return $tables_principales;
}

function spip_visuels_declarer_tables_auxiliaires($tables_auxiliaires) {

	$spip_visuels_liens = array(
			"id_visuel"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
			"vu"	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL");

	$spip_visuels_liens_key = array(
			"PRIMARY KEY"		=> "id_visuel,id_objet,objet",
			"KEY id_visuel"	=> "id_visuel",
			"KEY id_objet"	=> "id_objet",
			"KEY objet"	=> "objet",
	);

	$tables_auxiliaires['spip_visuels_liens'] = array(
		'field' => &$spip_visuels_liens,
		'key' => &$spip_visuels_liens_key);

	return $tables_auxiliaires;
}

function spip_visuels_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['visuels'] = 'visuels';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 */
function spip_visuels_declarer_tables_objets_sql($tables) {

	$tables['spip_visuels'] = array(
		'tables_jointures'  => array('spip_visuels_liens'),
	);

	return $tables;
}
