<?php

/**
 * Pipeline declarer_tables_principales
 * 
 * Declare les nouveaux champs sur les tables principales ou les nouvelles tables
 * principales
 * 
 * @param array $tables_principales
 * @return array $tables_principales (l'array modifie)
 */

function gfc_declarer_tables_principales($tables_principales){
	$tables_principales['spip_auteurs']['field']['gfc_uid'] = "varchar(50) NOT NULL";
	return $tables_principales;
}
?>