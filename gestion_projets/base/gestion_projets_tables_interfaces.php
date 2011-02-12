<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function gestion_projets_declarer_tables_interfaces($tables_interfaces){
	
	$tables_interfaces['table_des_tables']['projets'] = 'projets';
	$tables_interfaces['table_des_tables']['projets_timetracker'] = 'projets_timetracker';
	$tables_interfaces['table_des_tables']['projets_taches'] = 'projets_taches';	

	return $tables_interfaces;
}

?>
