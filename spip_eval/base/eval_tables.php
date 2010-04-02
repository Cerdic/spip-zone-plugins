<?php
/*
 * Plugin pour SPIP 2.0
 * Auteur Cyril MARION
 * (c) 2010 Ateliers CYM - Paris
 * Distribue sous licence GPL
 */

function eval_declarer_tables_principales($tables_principales){

	// Les campagnes d'évaluations
	$spip_eval_campagnes = array(
		"id_evaluation" => "BIGINT(21) NOT NULL auto_increment",
		"id_rubrique" => "BIGINT(21) NOT NULL",
		"id_groupe" => "BIGINT(21) NOT NULL",
		"date_debut" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"maj" => "TIMESTAMP"
	);
	$spip_eval_campagnes_key = array(
		"PRIMARY KEY" => "id_evaluation",
		"KEY id_rubrique" => "id_rubrique",
		"KEY id_groupe" => "id_groupe"
	);
	$tables_principales['spip_eval_campagnes'] = array(
		'field' => &$spip_eval_campagnes,
		'key' => &$spip_eval_campagnes_key
	);

	// création de la table spip_mots_notations
	// en attendant que spip gère la table spip_mots_objets

	return $tables_principales;
}


?>