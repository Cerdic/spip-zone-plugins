<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function spipservice_declarer_tables_objets_sql($tables){
	$tables['spip_spipservice'] = array(
			'principale' => "oui",
			'field'=> array(
					"id_spipservice" 	=> "bigint(21) NOT NULL auto_increment",
					"id"   				=> "bigint(21) NULL",
					"type"    			=> "varchar(25) NULL",
					"id_auteur"    		=> "bigint(21) NULL",
					"action"          	=> "varchar(255) NULL",
					"date"          	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
			),
			'key' => array(
					"PRIMARY KEY"	=> "id_spipservice",
			),
			'date' => "date",
	);
	return $tables;
}

?>
