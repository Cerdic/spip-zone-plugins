<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function gestion_projets_declarer_tables_auxiliaires($tables_auxiliaires){
		


	$spip_projets_auteur = array(
		"id_projet"			=> "bigint(21) NOT NULL",
		"id_auteur"			=> "bigint(21) NOT NULL",			
		);

	$spip_projets_auteur_key = array(
		 "KEY id_projet" => "id_projet",
		 "KEY id_auteur" => "id_auteur"		 
		);
		
	$tables_auxiliaires['spip_projets_auteur'] = array(
		'field' => &$spip_projets_auteur,
		'key' => &$spip_projets_auteur_key,
	);	
	return $tables_auxiliaires;
}

?>
