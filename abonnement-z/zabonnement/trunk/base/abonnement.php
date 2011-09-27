<?php
/**
* Plugin abonnement
*
* Copyright (c) 2011
* Anne-lise Martenot elastick.net / BoOz booz@rezo.net 

* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

function abonnement_declarer_tables_interfaces($interfaces){
	// alias
	$interfaces['table_des_tables']['abonnements'] = 'abonnements';
	$interfaces['table_des_tables']['contacts_abonnements'] = 'contacts_abonnements';
	// champs date
	$interfaces['table_date']['contacts_abonnements']='date';
	// jointures ?
	$interfaces['exception_des_jointures']['id_contact']=array('spip_contacts','id_auteur');

	return $interfaces;
}

function abonnement_declarer_tables_principales($tables_principales){

	//-- Table abonnements ------------------------------------------
	$abonnements = array(
		"id_abonnement" => "bigint(21)  NOT NULL auto_increment",
		"titre" 	=> "text NOT NULL",
		"duree" 	=> "text NOT NULL",
		"periode" 	=> "text NOT NULL",
		"exact" 	=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
		'nb_rub'	=> 'bigint(21) not null default 0',
		"ids_zone"	=> "text NOT NULL",	
		"prix"          => 'float not null default 0',
		"descriptif" 	=> "text NOT NULL",
		"maj" 		=> "timestamp(14) NOT NULL"
		);

	$abonnements_key = array(
		"PRIMARY KEY" => "id_abonnement"
		);	

	$tables_principales['spip_abonnements'] = array(
		'field' => &$abonnements, 
		'key' => &$abonnements_key);

	//table contacts_abonnements
	$contacts_abonnements = array(
		'id_contacts_abonnement' => 'bigint(21) not null',
		"id_auteur" 	=> "bigint(21)  NOT NULL",
		"objet" 	=> "tinytext NOT NULL",
		"id_objet" 	=> "bigint(21)  NOT NULL",
		"date" 		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"validite" 	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"prix" 		=> 'float not null default 0',
		"id_commandes_detail"=>"bigint(21)  NOT NULL",
		"statut_abonnement" 	=> "tinytext NOT NULL",
		"stade_relance" => "bigint(21)  NOT NULL",
		'maj' 		=> 'timestamp'
		);

	$contacts_abonnements_key = array(
		'PRIMARY KEY' => 'id_contacts_abonnement',
		"KEY id_auteur" => "id_auteur"
	);	

	$tables_principales['spip_contacts_abonnements'] = array(
		'field' => &$contacts_abonnements, 
		'key' => &$contacts_abonnements_key);

	
	return $tables_principales;
}

?>
