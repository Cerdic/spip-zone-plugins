<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function gestion_projets_tables_interfaces($tables_interfaces){
	
	$tables_interfaces['table_des_tables']['projets'] = 'projets';
	$tables_interfaces['table_des_tables']['projets_timetracker'] = 'projets_timetracker';

	return $tables_interfaces;
}

?>
