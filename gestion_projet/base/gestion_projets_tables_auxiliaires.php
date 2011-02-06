<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function gestion_projets_declarer_tables_auxiliaires($tables_auxiliaires){
		
	$spip_projets_timetracker = array(
		"id_projet"			=> "bigint(21) NOT NULL",
		"date_debut"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",		
		);

	$spip_projets_timetracker_key = array(
		"PRIMARY KEY"			=> "id_projet",
		);
		
	$tables_auxiliaires['spip_projets_timetracker'] = array(
		'field' => &$spip_projets_timetracker,
		'key' => &$spip_projets_timetracker_key,
	);

	
	return $tables_auxiliaires;
}

?>
