<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function changestatut_declarer_tables_objets_sql($tables){
	
	$tables['spip_auteurs']['field']['statut_orig'] = "VARCHAR(20) DEFAULT '' NOT NULL";
	return $tables;

}

?>
