<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function bibliocheck_declarer_tables_interfaces($interface){
	
	//-- Jointures ----------------------------------------------------
	$interface['tables_jointures']['spip_zitems'][]= 'tickets';
	$interface['tables_jointures']['spip_tickets'][]= 'zitems';
	
	return $interface;
}

function bibliocheck_declarer_tables_principales($tables_principales){
	$tables_principales['spip_tickets']['field']['id_zitem'] = "varchar(16) DEFAULT '' NOT NULL";
	$tables_principales['spip_tickets']['field']['auteur'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables_principales['spip_tickets']['field']['zitem_json'] = "mediumtext DEFAULT '' NOT NULL";
	
	return $tables_principales;
}




