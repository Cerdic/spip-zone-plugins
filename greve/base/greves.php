<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function greves_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['greves'] = 'greves';	
	$interface['table_des_traitements']['TEXTE']['greve'] = _TRAITEMENT_TYPO;
	return $interface;
}
function greves_declarer_tables_principales($tables_principales){
	//-- Table GREVES ------------------------------------------
	$greves = array(
			"id_greve"	=> "bigint(21) NOT NULL",
			"titre"	=> "tinytext DEFAULT '' NOT NULL",
			"texte"	=> "longtext DEFAULT '' NOT NULL",
			"debut"	=> "datetime",
			"fin"	=> "datetime"
			);
	
	$greves_key = array(
			"PRIMARY KEY"	=> "id_greve",
			);
	
	$tables_principales['spip_greves'] =
		array('field' => &$greves, 'key' => &$greves_key);
	return $tables_principales;
}
?>