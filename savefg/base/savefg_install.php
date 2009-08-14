<?php
	/**
	 * SaveFG
	 *
	 * Copyright (c) 2009
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
if (!defined("_ECRIRE_INC_VERSION")) return;
function savefg_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['savefg']='savefg';
	return $interface;
}
function savefg_declarer_tables_principales($tables_principales){
	$spip_savefg = array(
		"id_savefg" 	=> "INT(10) NOT NULL AUTO_INCREMENT",
		"fond" 	=> "text NOT NULL",
		"valeur" 	=> "text NOT NULL",
		"commentaire" 	=> "text NOT NULL",
		"version" => "VARCHAR(100) NOT NULL DEFAULT '1'",
		"date"	=> "DATETIME");
	
	$spip_savefg_key = array(
		"PRIMARY KEY" => "id_savefg");
	
	$tables_principales['spip_savefg'] = array(
		'field' => &$spip_savefg,
		'key' => &$spip_savefg_key);
		
	return $tables_principales;
}
?>