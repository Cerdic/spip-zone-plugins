<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function agenda_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['evenements']='evenements';
	
	//-- Jointures ----------------------------------------------------
	$interface['tables_jointures']['spip_evenements'][]= 'mots'; // a placer avant la jointure sur articles
	$interface['tables_jointures']['spip_articles'][]= 'evenements';
	$interface['tables_jointures']['spip_evenements'][] = 'articles';
	$interface['tables_jointures']['spip_mots'][]= 'mots_evenements';
	$interface['tables_jointures']['spip_evenements'][] = 'mots_evenements';
	$interface['tables_jointures']['spip_evenements'][] = 'evenements_participants';
	$interface['tables_jointures']['spip_auteurs'][] = 'evenements_participants';

	$interface['table_des_traitements']['LIEU'][]= 'propre(%s)';
	
	// permet d'utiliser les criteres racine, meme_parent, id_parent
	$interface['exceptions_des_tables']['evenements']['id_parent']='id_evenement_source';
	$interface['exceptions_des_tables']['evenements']['id_rubrique']=array('spip_articles', 'id_rubrique');
		
	$interface['table_date']['evenements'] = 'date_debut';

	// des titres pour certains jeux d'URL (propre, arborescent...)
	$interface['table_titre']['evenements']  = 'titre, "" AS lang';	
	
	return $interface;
}

function agenda_declarer_tables_principales($tables_principales){
	//-- Table EVENEMENTS ------------------------------------------
	$evenements = array(
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
			//"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP"
			);
	
	$evenements_key = array(
			"PRIMARY KEY"	=> "id_evenement",
			"KEY date_debut"	=> "date_debut",
			"KEY date_fin"	=> "date_fin",
			"KEY id_article"	=> "id_article"
			);
	
	$tables_principales['spip_evenements'] =
		array('field' => &$evenements, 'key' => &$evenements_key, 'join'=>array('id_evenement'=>'id_evenement','id_article'=>'id_article'));

	$tables_principales['spip_rubriques']['field']['agenda'] = 'tinyint(1) DEFAULT 0 NOT NULL';

	return $tables_principales;
}

function agenda_declarer_tables_auxiliaires($tables_auxiliaires){
	
	//-- Table de relations MOTS_EVENEMENTS----------------------
	$spip_mots_evenements = array(
			"id_mot"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
			"id_evenement"	=> "BIGINT (21) DEFAULT '0' NOT NULL");
	
	$spip_mots_evenements_key = array(
			"PRIMARY KEY"	=> "id_mot, id_evenement",
			"KEY id_evenement"	=> "id_evenement");
	
	$tables_auxiliaires['spip_mots_evenements'] = array(
		'field' => &$spip_mots_evenements,
		'key' => &$spip_mots_evenements_key);

	
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

?>