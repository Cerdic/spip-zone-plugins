<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function malettre_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['meslettres']='meslettres';	
	return $interface;
}


function malettre_declarer_tables_objets_sql($tables){

     /* Declaration de la table archive des lettres */
    $tables['spip_meslettres'] = array(
		/* Declarations principales */
        'table_objet' => 'meslettres',
        'table_objet_surnoms' => array('meslettres'),
        'type' => 'meslettres',
        'type_surnoms' => array('meslettres'),

		/* La table */
        'field'=> array(
      			"id_malettre"	=> "bigint(21) NOT NULL",
      			"titre"	=> "text NOT NULL",
      			"lang"	=> "varchar(255) NOT NULL",
      			"url_html"	=> "varchar(255) NULL",
      			"url_txt"	=> "varchar(255) NOT NULL",
      			"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
        ),
        'key' => array(
            "PRIMARY KEY"   => "id_malettre",
        ),
        'principale' => 'oui'

    );

	
	return $tables;
}


?>