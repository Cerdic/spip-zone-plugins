<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function projets_tt_declarer_tables_interfaces($tables_interfaces){
	
	$tables_interfaces['table_des_tables']['projets_timetracker'] = 'projets_timetracker';

	return $tables_interfaces;
}

?>
