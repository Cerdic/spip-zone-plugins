<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Déclaration des tables pour Signalement
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function signalement_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['signalements']='signalements';
	return $interfaces;
}

function signalement_declarer_tables_objets_sql($tables){
	$tables['spip_signalements'] = array(
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'signalement:signalements',
		'texte_objet' => 'signalement:signalement',
		'info_aucun_objet'=> 'signalement:info_aucun_signalement',
		'info_1_objet' => 'signalement:info_1_signalement',
		'info_nb_objets' => 'signalement:info_nb_signalements',
		'url_voir'=>'controler_signalement',
		'url_edit'=>'controler_signalement',
		'editable'=>'non',
		'titre' => 'texte',
		'date' => 'maj',
		'principale' => 'oui',
		'champs_editables' => array('texte','motif'),
		'field'=> array(
			"id_signalement"	=> "bigint(21) NOT NULL",
			"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_auteur"	=> "bigint DEFAULT '0' NOT NULL",
			"objet"		=> "VARCHAR (25) DEFAULT '' NOT NULL",
			"texte"		=> "mediumtext DEFAULT '' NOT NULL",
			"motif"		=> "varchar(255) DEFAULT '' NOT NULL",
			"auteur"	=> "text DEFAULT '' NOT NULL",
			"email_auteur"	=> "text DEFAULT '' NOT NULL",
			"ip"		=> "varchar(40) DEFAULT '' NOT NULL",
			"date"      => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"maj"		=> "TIMESTAMP",
			"statut"    => "varchar(20)  DEFAULT '0' NOT NULL",
		),
		'statut_textes_instituer' => array(
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_signalement",
			"KEY id_auteur"	=> "id_auteur",
			"KEY objet" => "objet",
			"KEY id_objet" => "id_objet",
			"KEY motif" => "motif",
		),
		'join' => array(
			"id_signalement"=>"id_signalement",
			"id_objet"=>"id_objet",
			"objet"=>"objet",
			"id_auteur"=>"id_auteur"
		),
		'rechercher_champs' => array(
	    	'texte' => 10, 'motif' => 10, 'auteur' => 2, 'email_auteur' => 2
		),
		'rechercher_jointures' => array(
			'auteur' => array('nom' => 8),
		),
		'champs_versionnes' => array('id_auteur', 'texte', 'objet', 'id_objet', 'auteur','ip','motif')
	);
	
	// jointures sur les signalements pour tous les objets
	$tables[]['tables_jointures'][]= 'signalements';
	
	// recherche jointe sur les diogenes pour tous les objets
	$tables[]['rechercher_jointures']['signalement'] = array('texte' => 1,'motif' => 1);
	
	// versionner les jointures pour tous les objets
	$tables[]['champs_versionnes'][] = 'jointure_signalements';
	
	return $tables;
}
?>