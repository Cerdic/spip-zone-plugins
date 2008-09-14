<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function Agenda_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['evenements']='evenements';
	
	//-- Jointures ----------------------------------------------------
	$interface['tables_jointures']['spip_evenements'][]= 'mots'; // a placer avant la jointure sur articles
	$interface['tables_jointures']['spip_articles'][]= 'evenements';
	$interface['tables_jointures']['spip_evenements'][] = 'articles';
	$interface['tables_jointures']['spip_mots'][]= 'mots_evenements';
	$interface['tables_jointures']['spip_evenements'][] = 'mots_evenements';

	$interface['table_des_traitements']['LIEU'][]= 'propre(%s)';
	
	// permet d'utiliser les criteres racine, meme_parent, id_parent
	$interface['exceptions_des_tables']['evenements']['id_parent']='id_evenement_source';
	$interface['exceptions_des_tables']['evenements']['id_rubrique']=array('spip_articles', 'id_rubrique');
		
	$interface['table_date']['evenements'] = 'date_debut';

	return $interface;
}

function Agenda_declarer_tables_principales($tables_principales){
	//-- Table EVENEMENTS ------------------------------------------
	$evenements = array(
			"id_evenement"	=> "bigint(21) NOT NULL",
			"id_article"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"date_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_fin"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"titre"	=> "text NOT NULL",
			"descriptif"	=> "text NOT NULL",
			"lieu"	=> "text NOT NULL",
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
		array('field' => &$evenements, 'key' => &$evenements_key, 'join'=>array('id_article'=>'id_article'));


	return $tables_principales;
}

function Agenda_declarer_tables_auxiliaires($tables_auxiliaires){
	
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
		
	global $exceptions_des_tables;
	$exceptions_des_tables['evenements']['id_rubrique']=array('spip_articles', 'id_rubrique');
	
	global $table_date;
	$table_date['evenements'] = 'date_debut';
	// si on declare les tables dans $table_des_tables, il faut mettre le prefixe


	return $tables_auxiliaires;
}

?>