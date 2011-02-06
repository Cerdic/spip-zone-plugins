<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//
// Formulaires : Structure
//

function gestion_projets_declarer_tables_principales($tables_principales){
	$spip_projets = array(
		"id_projet" 		=> "int(21) NOT NULL",
		"id_parent" 		=> "int(21) NOT NULL",			
		"nom" 			=> "varchar(255) NOT NULL",
		"statut" 			=> "varchar(20) NOT NULL",
		"active" 			=> "bool NOT NULL",				
		"date_creation" 	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",		
		"maj" 			=> "TIMESTAMP");
	
	$spip_projets_key = array(
		"PRIMARY KEY" 	=> "id_projet",
		"KEY id_parent"	=> "id_parent",	
		);
		
	$spip_projets_join = array(
		"id_projet"	=> "id_projet",	
		);

	$tables_principales['spip_projets'] = array(
		'field' => &$spip_projets,
		'key' => &$spip_projets_key,
		'join' => &$spip_projets_join
	);
	$spip_projets_timetracker = array(
		"id_session"		=> "bigint(21) NOT NULL",
		"id_projet"			=> "bigint(21) NOT NULL",		
		"date_debut"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"duree"				=> "decimal(65,2)",				
		);

	$spip_projets_timetracker_key = array(
		"PRIMARY KEY"			=> "id_session",
		 "KEY id_projet" => "id_projet"
		);
		
	$spip_projets_timetracker_join = array(
		"id_projet"	=> "id_projet",	
		);		
		
	$tables_principales['spip_projets_timetracker'] = array(
		'field' => &$spip_projets_timetracker,
		'join' => &$spip_projets_timetracker_join,		
		'key' => &$spip_projets_timetracker_key,
	);		
	return $tables_principales;
	
	}
?>
