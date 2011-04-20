<?php

	/**
	* Plugin Bannières
	*
	* Copyright (c) 2009
	* François de Montlivault - Jeannot
	* Mise à jour Inspirée du plugin chats
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

function bannieres_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['bannieres'] = 'bannieres';	
	$interface['table_des_tables']['bannieres_suivi'] = 'bannieres_suivi';	
	return $interface;
}


function bannieres_declarer_tables_principales($tables_principales){

	$spip_bannieres = array(
		"id_banniere" 	=> "bigint(21) NOT NULL auto_increment",
		"nom" 			=> "VARCHAR(100) DEFAULT '' NOT NULL",
		"email" 		=> "VARCHAR(100) DEFAULT '' NOT NULL",
		"site" 			=> "VARCHAR(255) DEFAULT '' NOT NULL",
		"debut"			=> "date DEFAULT '0000-00-00' NOT NULL",
		"fin"			=> "date DEFAULT '0000-00-00' NOT NULL",
		"clics"			=> "int(11) DEFAULT '0' NOT NULL",
		"commentaires" 	=> "text DEFAULT '' NOT NULL",
		"creation"		=> "date DEFAULT '0000-00-00' NOT NULL",
		"position"		=> "tinyint(2) NOT NULL default '1'",
		"rayon"			=> "VARCHAR(50) DEFAULT 'int' NOT NULL",
		"diffusion"		=> "text DEFAULT '' NOT NULL",
		"maj" 			=> "TIMESTAMP",
			);
	
	$spip_bannieres_key = array(
			"PRIMARY KEY"	=> "id_banniere"
			);

	$spip_bannieres_suivi = array(
			"id_banniere"	=> "bigint(21) NOT NULL",
			"id_auteur"		=> "bigint(21) NOT NULL",
			"ip"			=> "VARCHAR(50) NOT NULL",
			"page"			=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"date"			=> "timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL",
			);
	
	$spip_bannieres_suivi_key = array(
			"KEY"	=> "id_banniere"
			);
	
	
$tables_principales['spip_bannieres'] =	array('field' => &$spip_bannieres,'key'=>$spip_bannieres_key);

$tables_principales['spip_bannieres_suivi'] =	array('field' => &$spip_bannieres_suivi,'key'=>$spip_bannieres_suivi_key);

	return $tables_principales;
}


?>