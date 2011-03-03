<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//
// Formulaires : Structure
//

function gestion_projets_timetracker_declarer_tables_principales($tables_principales){

	$spip_projets_timetracker = array(
		"id_session"		=> "bigint(21) NOT NULL",
		"id_auteur"		=> "bigint(21) NOT NULL",			
		"id_projet"			=> "bigint(21) NOT NULL",		
		"date_debut"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"duree"				=> "decimal(65,2)",				
		);

	$spip_projets_timetracker_key = array(
		"PRIMARY KEY"		=> "id_session",
		"KEY id_projet" 	=> "id_projet",
		"KEY id_auteur"	 	=> "id_auteur",		 
		);
		
	$spip_projets_timetracker_join = array(
		"id_session"		=> "id_session",
		"id_projet"			=> "id_projet",	
		"id_auteur"			=> "id_auteur",			
		);		
		
	$tables_principales['spip_projets_timetracker'] = array(
		'field' => &$spip_projets_timetracker,
		'join' => &$spip_projets_timetracker_join,		
		'key' => &$spip_projets_timetracker_key,
	);		
	return $tables_principales;
	
	}
?>
