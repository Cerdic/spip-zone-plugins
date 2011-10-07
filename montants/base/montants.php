<?php
/**
* Plugin montants
*
* Copyright (c) 2011
* Anne-lise Martenot elastick.net

* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

function montants_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['montants'] = 'montants';

	return $interfaces;
}

function montants_declarer_tables_principales($tables_principales){

	$spip_montants = array(
			"id_montant" 	=> "int(10) unsigned NOT NULL auto_increment",
			"objet" 	=> "text NOT NULL",
			"ids_objet"	=> "text NOT NULL",
			"le_parent"	=> "int(10) NOT NULL",
			"prix_ht" 	=> "float not null",
			"taxe"		=> "decimal(4,3) default null",
			"descriptif"	=> "text NOT NULL",
			);

	$spip_montants_key = array(
			"PRIMARY KEY" => "id_montant"
			);	

	$tables_principales['spip_montants'] = array(
			'field' => &$spip_montants, 
			'key' => &$spip_montants_key);

	 		
	return $tables_principales;
}

?>
