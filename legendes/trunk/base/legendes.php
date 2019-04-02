<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function legendes_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	// pour pouvoir faires des BOUCLES(LEGENDES)
	$interface['table_des_tables']['legendes']='legendes';
	
	return $interface;
}

function legendes_declarer_tables_principales($tables_principales){
	//-- Table LEGENDES ------------------------------------------
	$legendes = array(
		"id_legende"	=> "bigint(21) NOT NULL",
		"id_document"	=> "bigint(21) NOT NULL DEFAULT '0'",
		"id_auteur"	=> "bigint(21) NOT NULL DEFAULT '0'",
		"posx"	=> "bigint(21) NOT NULL DEFAULT '0'",
		"posy"	=> "bigint(21) NOT NULL DEFAULT '0'",
		"width"	=> "bigint(21) NOT NULL DEFAULT '0'",
		"height"=> "bigint(21) NOT NULL DEFAULT '0'",
		"texte"	=> "text NOT NULL",
		"date"	=> "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"
	);
	
	$legendes_key = array(
		"PRIMARY KEY"	=> "id_legende",
		"KEY id_document"=> "id_document",
		"KEY id_auteur"	=> "id_auteur"
	);
	
	$tables_principales['spip_legendes'] = array(
		'field' => &$legendes,
		'key' => &$legendes_key
	);

	return $tables_principales;
}

?>