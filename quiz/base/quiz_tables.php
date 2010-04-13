<?php

/**
 * Plugin Quiz pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function quiz_declarer_tables_principales($tables_principales){

	// REPONSES
	$spip_reponses = array(
		"id_reponse" => "BIGINT(21) NOT NULL auto_increment",
		"id_article" => "BIGINT(21) NOT NULL",
		"texte"	=> "text default NULL"
	);
	$spip_reponses_key = array(
		"PRIMARY KEY" => "id_reponse",
		"KEY id_article" => "id_article"
	);
	$tables_principales['spip_reponses'] = array(
		'field' => &$spip_reponses,
		'key' => &$spip_reponses_key
	);

	// CORRECTIONS
	$spip_corrections = array(
		"id_article" => "BIGINT(21) NOT NULL",
		"id_juste" => "BIGINT(21) NOT NULL",
		"corrige" => "text default NULL"
	);
	$spip_corrections_key = array(
		"PRIMARY KEY" => "id_article",
		"KEY id_juste" => "id_juste"
	);
	$tables_principales['spip_corrections'] = array(
		'field' => &$spip_corrections,
		'key' => &$spip_corrections_key
	);

	return $tables_principales;
	
}

?>