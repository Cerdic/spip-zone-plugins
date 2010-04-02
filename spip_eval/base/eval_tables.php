<?php
/*
 * Plugin pour SPIP 2.0
 * Auteur Cyril MARION
 * (c) 2010 Ateliers CYM - Paris
 * Distribue sous licence GPL
 */

function eval_declarer_tables_principales($tables_principales){

	// Les campagnes d'evaluations
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

	// modification de la table spip_notations
	$table['spip_notations']['champ'] = 'id_mot';
	$table['spip_notations']['champ'] = 'commentaire';
	
	// modification de la table spip_notations_objets
	$table['spip_notations_objets']['champ'] = 'id_mot';
		
	// creation de la table spip_mots_notations
	// en attendant que SPIP gere la table spip_mots_objets
	$spip_mots_notations = array(
		"id_mot" => "BIGINT(21) NOT NULL",
		"id_notation" => "BIGINT(21) NOT NULL"
	);
	$spip_mots_notations_key = array(
		"PRIMARY KEY" => "id_mot, id_notation",
		"KEY id_mot" => "id_mot",
		"KEY id_notation" => "id_notation"
	);
	$tables_principales['spip_mots_notations'] = array(
		'field' => &$spip_mots_notations,
		'key' => &$spip_mots_notations_key
	);
	
	return $tables_principales;
}


?>