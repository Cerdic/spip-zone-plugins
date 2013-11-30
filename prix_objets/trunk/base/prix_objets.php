<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function prix_objets_declarer_tables_interfaces($tables_interfaces){

    $tables_interfaces['table_des_tables']['prix_objets'] = 'prix_objets';
    
    return $tables_interfaces;
}

function prix_objets_declarer_tables_principales($tables_principales){
	$spip_prix_objets = array(
		"id_prix_objet" 	=> "bigint(21) NOT NULL",
		"id_objet" 	=> "bigint(21) NOT NULL",
		"titre"   => "varchar(255)  DEFAULT '' NOT NULL",
		"reference"   => "varchar(255)  DEFAULT '' NOT NULL",		
		'objet' => 'varchar(25) not null default ""',		
		"code_devise" 	=> "varchar(3) NOT NULL",
		"prix_ht" 		=> "float (38,2) NOT NULL",
        "prix"       => "float (38,2) NOT NULL",	
        "taxe"   => "varchar(10)  DEFAULT '' NOT NULL",        	
		);
	
	$spip_prix_objets_key = array(
		"PRIMARY KEY" 	=> "id_prix_objet",
		"KEY id_objet"	=> "id_objet",
		);
		
	$spip_prix_objets_join = array(
		"id_prix_objet"	=> "id_prix_objet",
		"id_objet"	=> "id_objet",
		"id_objet"	=> "id_article",		
		);

	$tables_principales['spip_prix_objets'] = array(
		'field' => &$spip_prix_objets,
		'key' => &$spip_prix_objets_key,
		'join' => &$spip_prix_objets_join
	);
	
	return $tables_principales;
	
	}


?>
