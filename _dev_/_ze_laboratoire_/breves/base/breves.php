<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function breves_declarer_tables_principales($tables_principales){
	$spip_breves = array(
			"id_breve"	=> "bigint(21) NOT NULL",
			"date_heure"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"texte"	=> "longtext DEFAULT '' NOT NULL",
			"lien_titre"	=> "text DEFAULT '' NOT NULL",
			"lien_url"	=> "text DEFAULT '' NOT NULL",
			"statut"	=> "varchar(6)  DEFAULT '0' NOT NULL",
			"id_rubrique"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
			"langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
			"maj"	=> "TIMESTAMP",
	#		"extra"	=> "longtext NULL",
	#		"url_propre" => "VARCHAR(255) DEFAULT '' NOT NULL"
	);

	$spip_breves_key = array(
			"PRIMARY KEY"	=> "id_breve",
			"KEY id_rubrique"	=> "id_rubrique",
	#		"KEY url_propre"	=> "url_propre"
	);
	$spip_breves_join = array(
			"id_breve"=>"id_breve",
			"id_rubrique"=>"id_rubrique");

	$tables_principales['spip_breves'] = array(
		'field' => &$spip_breves, 
		'key' => &$spip_breves_key,
		'join' => &$spip_breves_join
	);
	
	$tables_principales['spip_forum']['field']['id_breve']	= "bigint(21) DEFAULT '0' NOT NULL";
	$tables_principales['spip_forum']['join']['id_breve']	= "id_breve";
	
	return $tables_principales;
}


function breves_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_mots_breves = array(
		"id_mot"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"id_breve"	=> "bigint(21) DEFAULT '0' NOT NULL");

	$spip_mots_breves_key = array(
		"PRIMARY KEY"	=> "id_breve, id_mot",
		"KEY id_mot"	=> "id_mot");
		
	$tables_auxiliaires['spip_mots_breves'] = array(
		'field' => &$spip_mots_breves,
		'key' => &$spip_mots_breves_key);
		
	return $tables_auxiliaires;
}


function breves_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['breves']='breves';
	
	$interfaces['exceptions_des_tables']['breves']['id_secteur']='id_rubrique';
	$interfaces['exceptions_des_tables']['breves']['date']='date_heure';
	$interfaces['exceptions_des_tables']['breves']['nom_site']='lien_titre';
	$interfaces['exceptions_des_tables']['breves']['url_site']='lien_url';	
	
	$interfaces['table_titre']['breves']= 'titre , lang';
	$interfaces['table_date']['breves']='date_heure';

	$interfaces['tables_jointures']['spip_breves'][]= 'mots_breves';
	$interfaces['tables_jointures']['spip_breves'][]= 'documents_liens';
	$interfaces['tables_jointures']['spip_breves'][]= 'mots';
	
	$interfaces['tables_jointures']['spip_mots'][]= 'mots_breves';
	
	return $interfaces;
}


function breves_declarer_tables_objets_surnom($surnoms){
	$surnoms['breve'] = 'breves';
	return $surnoms;
}
?>
