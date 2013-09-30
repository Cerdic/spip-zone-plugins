<?php

function svpstats_declarer_tables_principales($tables_principales) {

	// Ajout de champs dans la tables des plugins : spip_plugins
	$tables_principales['spip_plugins']['field']['nbr_sites'] = "integer DEFAULT 0 NOT NULL";
	$tables_principales['spip_plugins']['field']['popularite'] = "double DEFAULT '0' NOT NULL";

	return $tables_principales;
}


function svpstats_declarer_tables_auxiliaires($tables_auxiliaires) {
	// Tables de liens entre plugins et les stats : spip_plugins_stats
	$spip_plugins_stats = array(
		"prefixe"		=> "varchar(30) DEFAULT '' NOT NULL",
		"branche_spip"	=> "varchar(255) DEFAULT '' NOT NULL",
		"nbr_sites" 	=> "integer DEFAULT 0 NOT NULL",
		"popularite"	=> "double DEFAULT '0' NOT NULL",
		"historique"	=> "text DEFAULT '' NOT NULL", // Historique mensuel
		"maj"			=> "TIMESTAMP",
	);

	$spip_plugins_stats_key = array(
		"PRIMARY KEY" 	=> "prefixe, branche_spip"
	);

	$tables_auxiliaires['spip_plugins_stats'] = 
		array('field' => &$spip_plugins_stats, 'key' => &$spip_plugins_stats_key);

	return $tables_auxiliaires;
}


function svpstats_declarer_tables_interfaces($interface) {
	// Les tables
	$interface['table_des_tables']['plugins_stats'] = 'plugins_stats';	

	// Les jointures
	// -- Entre spip_plugins_stats et spip_plugins
	$interface['tables_jointures']['spip_plugins'][] = 'plugins_stats';

	return $interface;
}

?>
