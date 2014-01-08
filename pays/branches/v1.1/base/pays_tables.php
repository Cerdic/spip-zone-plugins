<?php
/**
 * Plugin Pays pour Spip 2.0
 * Licence GPL
 * Auteur Organisation Internationale de Normalisation http://www.iso.org/iso/fr/country_codes/iso_3166_code_lists.htm
 * Cedric Morin et Collectif SPIP pour version spip_geo_pays
 * Portage sous SPIP par Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function pays_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['pays'] = 'pays';
	$interface['table_des_tables']['geo_pays'] = 'pays'; // en attendant une meilleure collaboration avec 'geographie'
	$interface['table_des_traitements']['NOM'][] = _TRAITEMENT_RACCOURCIS;

	return $interface;
}

function pays_declarer_tables_principales($tables_principales){

	$spip_pays = array(
		"id_pays"		=> "smallint(6) NOT NULL auto_increment",
		"code"			=> "varchar(2) NOT NULL default ''",
		"nom"			=> "text NOT NULL default ''",
		"maj"			=> "TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP"
	);
	$spip_pays_key = array(
		"PRIMARY KEY"	=> "id_pays",
		"UNIQUE KEY code"	=> "code"
	);
	$tables_principales['spip_pays'] = array(
		'field'=>&$spip_pays,
		'key'=>$spip_pays_key
	);
	
	return $tables_principales;
}

?>
