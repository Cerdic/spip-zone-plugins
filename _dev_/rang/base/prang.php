<?php
function i2_societes_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_auteurs_elargis'][] = 'societes';
	
	//-- Table des tables ----------------------------------------------------
	$interface['table_des_tables']['societes']='societes';

	return $interface;
}

function prang_declarer_tables_principales($tables_principales){

    $tables_principales['spip_rubriques']['field']['rang'] = "BIGINT(21) NOT NULL";
    $tables_principales['spip_articles']['field']['rang'] = "BIGINT(21) NOT NULL";
	
	return $tables_principales;
}
?>
