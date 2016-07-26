<?php

function refresher_declarer_tables_objets_sql($tables){
	$tables['refresher_cron'] = array(
 
		'principale' => "oui",
		'field'=> array(
			"id"	=> "bigint(21) NOT NULL AUTO_INCREMENT",
			"url"	=> "varchar(255) NOT NULL",
			"frequence"	=> "bigint(21) NOT NULL",
			"last_hit" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		),
		'key' => array(
			"PRIMARY KEY"	=> "id"
		)
	);
	
	$tables['refresher_urls'] = array(
 
		'principale' => "oui",
		'field'=> array(
			"id"	=> "bigint(21) NOT NULL AUTO_INCREMENT",
			"uri"	=> "varchar(255) NOT NULL",
			"squelette"	=> "varchar(100) NOT NULL",
			"id_objet" => "varchar(255) NOT NULL",
			"objet"	=> "varchar(100) NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id"
		)
	);
 
	return $tables;
}

?>