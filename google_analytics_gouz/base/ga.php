<?php

function ga_declarer_tables_principales($tables_principales){
	$spip_ga = array(
			"id"	=> "int(11) NOT NULL",
			"objet"	=> "varchar(3) NOT NULL",
			"id_objet" => "int(11) NOT NULL",
			"code" => "varchar(255) NOT NULL",
			"lien" => "varchar(255) NOT NULL"
			);
	
	$spip_ga_key = array(
			"PRIMARY KEY" => "id"
			);
	
	$tables_principales['spip_ga'] = array('field' => &$spip_ga, 'key' => &$spip_ga_key);
	return $tables_principales;
}


function ga_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['ga']='ga';
	return $interface;
}

?>