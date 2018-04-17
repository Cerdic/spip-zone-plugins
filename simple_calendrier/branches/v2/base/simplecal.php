<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3
 * Licence GNU/GPL
 * 2010-2017
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function simplecal_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['evenements'] = 'evenements';

	$interfaces['table_des_traitements']['LIEU'][]= _TRAITEMENT_RACCOURCIS;
	$interfaces['table_des_traitements']['ADRESSE'][]= _TRAITEMENT_RACCOURCIS;

	
	// ---------------------------------------------------------------------------
	// Champs de type 'date' pour la gestion des criteres age, age_relatif, etc.
	// ---------------------------------------------------------------------------
	// Note : provoque l'enregistrement de la date de publication (lors de sa modif) dans date
	//$interface['table_date']['evenements'] = 'date'; 
	
	return $interfaces;
}


function simplecal_declarer_tables_objets_sql($tables) {
	
	// Champs de la table spip_evenements
	$fields = array(
		"id_evenement"      => "bigint(21) NOT NULL auto_increment",
		"id_secteur"        => "bigint(21) NOT NULL DEFAULT '0'",
		"id_rubrique"       => "bigint(21) NOT NULL DEFAULT '0'",
		"id_trad"           => "bigint(21) NOT NULL DEFAULT '0'",
		"id_objet"          => "bigint(21) NOT NULL DEFAULT '0'",
		"type"              => "varchar(25) NOT NULL",
		"titre"             => "varchar(255) NOT NULL",
		"date_debut"        => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
		"date_fin"          => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
		"horaire"           => "varchar(3) NOT NULL DEFAULT 'oui'",
		"descriptif"        => "text NOT NULL",
		"texte"             => "text NOT NULL",
		'lieu'				=> "text NOT NULL DEFAULT ''",
		'adresse'			=> "text NOT NULL DEFAULT ''",
		"lien_titre"        => "varchar(255) NOT NULL",
		"lien_url"          => "varchar(255) NOT NULL",
		"date"              => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", // creation ou publication (selon statut) 
		"statut"            => "varchar(20)  DEFAULT '0' NOT NULL",
		"lang"              => "varchar(10) NOT NULL DEFAULT ''",
		"langue_choisie"    => "varchar(3) NULL DEFAULT 'non'", 
		"maj"               => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
	);
	
	// champs qui possedent les cles
	$key = array(
		"PRIMARY KEY"     => "id_evenement",
		"KEY statut"      => "statut", 
		"KEY id_secteur"  => "id_secteur",
		"KEY id_rubrique" => "id_rubrique",
		"KEY id_trad"     => "id_trad",
		"KEY lang"        => "lang"
	);
	
	// champ 'statut'
	$statut = array(
		'champ'=>'statut',
		'publie'=>'publie',
		'previsu'=>'publie,prop',
		'exception'=>'statut'
	);
	
	// titre des statuts
	$statut_titres = array(
		'prepa'=>'simplecal:titre_evenement_redaction',
		'prop' => 'simplecal:titre_evenement_propose',
		'publie' => 'simplecal:titre_evenement_publie',
		'refuse' => 'simplecal:titre_evenement_refuse',
		'poubelle'=>'simplecal:titre_evenement_supprime'
	);
	
	$statut_textes_instituer = array(
		'prepa' => 'texte_statut_en_cours_redaction',
		'prop' => 'texte_statut_propose_evaluation',
		'publie' => 'texte_statut_publie', 
		'refuse' => 'texte_statut_refuse',
		'poubelle' => 'texte_statut_poubelle'
	);
	
	// La Table
	$tables['spip_evenements'] = array(
		'type' => 'evenement',
		'principale' => 'oui',
		'field'=> $fields,
		'key' => $key,
		'titre' => 'titre, lang',
		'date' => 'date', // indique le nom du field pour le formulaires_dater_charger_dist
		'champs_editables' => array('titre', 'date_debut', 'date_fin', 'horaire', 'descriptif', 'texte', 'lieu', 'adresse', 'lien_titre', 'lien_url', 'type', 'id_objet'),
		'champs_versionnes' => array('titre', 'descriptif', 'texte', 'lieu', 'adresse', 'lien_titre', 'lien_url'),
		'rechercher_champs' => array('titre'=>8, 'descriptif'=>4, 'texte'=>2),
		'tables_jointures'  => array(),
		'statut' =>  array($statut),
		'statut_textes_instituer' => $statut_textes_instituer,
		'statut_titres' => $statut_titres,
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'simplecal:evenements',
		'texte_objet' => 'simplecal:evenement',
		'texte_modifier' => 'simplecal:icone_modifier_evenement',
		'texte_creer' => 'simplecal:icone_nouvel_evenement',
		'info_aucun_objet'=> 'simplecal:info_aucun_evenement',
		'info_1_objet' => 'simplecal:info_1_evenement',
		'info_nb_objets' => 'simplecal:info_nb_evenements',
		'texte_logo_objet' => 'simplecal:logo_evenement',
		'texte_langue_objet' => 'simplecal:titre_langue_evenement',
		'texte_changer_statut' => 'simplecal:entree_evenement_publie'
	);
	
	return $tables;
}