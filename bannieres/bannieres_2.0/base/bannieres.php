<?php
	/**
	* Plugin Bannières
	*
	* Copyright (c) 2008
	* François de Montlivault
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

	// Declaration des tables evenements

	include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
	
	global $tables_principales;
	global $tables_auxiliaires;

	//-- Table BANNIERES------------------------------------------
	$spip_bannieres = array(
		"id_banniere" 	=> "bigint(21) NOT NULL auto_increment",
		"nom" 			=> "text NOT NULL",
		"email" 		=> "text NOT NULL",
		"site" 			=> "text NOT NULL",
		"alt" 			=> "text NOT NULL",
		"debut"			=> "date NOT NULL default '0000-00-00'",
		"fin"			=> "date NOT NULL default '0000-00-00'",
		"clics"			=> "int(11) NOT NULL default '0'",
		"commentaires" 	=> "longtext NOT NULL",
		"creation"		=> "date NOT NULL default '0000-00-00'",
		"maj" 			=> "timestamp(14) NOT NULL"
	);

	$spip_bannieres_key = array(
		"PRIMARY KEY" => "id_banniere"
	);	

	$tables_principales['spip_bannieres'] = array(
		'field' => &$spip_bannieres, 
		'key' => &$spip_bannieres_key
	);

?>