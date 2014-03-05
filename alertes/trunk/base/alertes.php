<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 */

function alertes_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['alertes']='alertes'; //Les alertes
	$interface['table_des_tables']['alertes_cron']='alertes_cron'; //La table du CRON si besoin
	return $interface;
}

/**
 * Declaration des tables principales
 *
 * @param array $tables_principales
 * @return array
 */
function alertes_declarer_tables_principales($tables_principales){
	//Table spip_alertes
	$spip_alertes = array(
		"id_alerte"	=> "bigint(21) NOT NULL",
		"id_auteur"	=> "bigint DEFAULT '0' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
	);

	$spip_alertes_key = array(
		"PRIMARY KEY"		=> "id_alerte",
		"KEY auteur_objet"	=> "id_auteur,id_objet,objet",
		"KEY id_auteur"	=> "id_auteur"
	);
	$tables_principales['spip_alertes'] = array('field' => &$spip_alertes, 'key' => &$spip_alertes_key);	
	
	//Table spip_alertes_cron
	$spip_alertes_cron = array(
		"id_alerte_cron"	=> "bigint(21) NOT NULL",
		"id_auteur"	=> "bigint DEFAULT '0' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
		"date_pour_envoi"	=> "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
	);

	$spip_alertes_cron_key = array(
		"PRIMARY KEY"		=> "id_alerte_cron",
		"KEY auteur_objet"	=> "id_auteur,id_objet,objet",
		"KEY id_auteur"	=> "id_auteur"
	);
	$tables_principales['spip_alertes_cron'] =array('field' => &$spip_alertes_cron, 'key' => &$spip_alertes_cron_key);

	return $tables_principales;
}

?>