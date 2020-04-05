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

	$tables_principales['spip_visuels'] = array(
		'field' => &$spip_visuels,
		'key' => &$spip_visuels_key,
		
	);

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

	if (!isset($interfaces['tables_jointures']['spip_visuels'])) {
		$interfaces['tables_jointures']['spip_visuels'] = array();
	}
	$interfaces['tables_jointures']['spip_visuels'][] = 'spip_visuels_liens';

	// Avant la 3.1, #FICHIER ne retournait pas par défaut IMG/ lors de l'appel à #FICHIER
	// en dehors de la boucle DOCUMENTS ( https://core.spip.net/issues/3108 )
	if (version_compare($GLOBALS['spip_version_branche'], '3.1-alpha', '<')) { 
		if (!isset($interfaces['table_des_traitements']['FICHIER'])) {
			$interfaces['table_des_traitements']['FICHIER'] = array();
		}
		$interfaces['table_des_traitements']['FICHIER']['visuels'] = 'get_spip_doc(%s)';
	}

	return $interfaces;
}

