<?php
/**
 * Plugin Agenda pour Spip 3.0
 * Licence GPL
 * 
 *
 */

function agenda_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['evenements']='evenements';
	
	//-- Jointures ----------------------------------------------------

	$interface['tables_jointures']['spip_articles'][]= 'evenements';
	$interface['tables_jointures']['spip_evenements'][] = 'articles';
	$interface['tables_jointures']['spip_evenements'][] = 'evenements_participants';
	$interface['tables_jointures']['spip_auteurs'][] = 'evenements_participants';
	$interface['table_des_traitements']['LIEU'][]= 'typo(%s)';
	
	// permet d'utiliser les criteres racine, meme_parent, id_parent
	$interface['exceptions_des_tables']['evenements']['id_parent']='id_evenement_source';
	$interface['exceptions_des_tables']['evenements']['id_rubrique']=array('spip_articles', 'id_rubrique');
		
	$interface['table_date']['evenements'] = 'date_debut';

	// des titres pour certains jeux d'URL (propre, arborescent...)
	$interface['table_titre']['evenements']  = 'titre, "" AS lang';	
	
	return $interface;
}

function agenda_declarer_tables_principales($tables_principales){
	

	$tables_principales['spip_rubriques']['field']['agenda'] = 'tinyint(1) DEFAULT 0 NOT NULL';

	return $tables_principales;
}

function agenda_declarer_tables_auxiliaires($tables_auxiliaires){

	//-- Table des participants ----------------------
	$spip_evenements_participants = array(
			"id_evenement"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
			"id_auteur"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
			"date" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"reponse" => "char(3) default '?' NOT NULL", // oui, non, ?
			);
	
	$spip_evenements_participants_key = array(
			"PRIMARY KEY"	=> "id_evenement, id_auteur",
			"KEY id_auteur"	=> "id_auteur");
	
	$tables_auxiliaires['spip_evenements_participants'] = array(
		'field' => &$spip_evenements_participants,
		'key' => &$spip_evenements_participants_key);

	return $tables_auxiliaires;
}
function agenda_declarer_tables_objets_sql($tables){
	$tables['spip_evenements'] = array(
		'page'=>'evenement',
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'agenda:info_evenements',
		'texte_objet' => 'agenda:info_evenement',
		'texte_modifier' => 'agenda:icone_modifier_evenement',
		'texte_creer' => 'agenda:titre_cadre_ajouter_evenement',
		'info_aucun_objet'=> 'agenda:info_aucun_evenement',
		'info_1_objet' => 'agenda:info_un_evenement',
		'info_nb_objets' => 'agenda:info_nombre_evenements',
		'titre' => 'titre',
		'date' => 'date_heure',
		'principale' => 'oui',
		'champs_editables' => array('date_debut', 'date_fin', 'titre', 'descriptif','lieu', 'adresse', 'inscription', 'places', 'horaire'),
		'field'=> array(
			"id_evenement"	=> "bigint(21) NOT NULL",
			"id_article"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"date_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_fin"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"titre"	=> "text NOT NULL DEFAULT ''",
			"descriptif"	=> "text NOT NULL DEFAULT ''",
			"lieu"	=> "text NOT NULL DEFAULT ''",
			"adresse"	=> "text NOT NULL DEFAULT ''",
			"inscription" => "tinyint(1) DEFAULT 0 NOT NULL",
			"places" => "int(11) DEFAULT 0 NOT NULL",
			"horaire" => "varchar(3) DEFAULT 'oui' NOT NULL",
			"id_evenement_source"	=> "bigint(21) NOT NULL",
			"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
			"maj"	=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_evenement",
			"KEY date_debut"	=> "date_debut",
			"KEY date_fin"	=> "date_fin",
			"KEY id_article"	=> "id_article"
		),
		'join' => array(
			"id_evenement"=>"id_evenement",
			"id_article"=>"id_article"
		),
		'rechercher_champs' => array(
		  'titre' => 8, 'descriptif' => 5, 'lieu' => 5, 'adresse' => 3
		),
		'rechercher_jointures' => array(
			'document' => array('titre' => 2, 'descriptif' => 1)
		),
		'statut' => array(
			array(
				'champ' => 'statut',
				'publie' => 'publie',
				'previsu' => '!',
				'exception' => array('statut','tout')
			),
		),
		'champs_versionnes' => array('id_article', 'titre', 'descriptif', 'lieu', 'adresse'),
	);

	return $tables;
}

?>