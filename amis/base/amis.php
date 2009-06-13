<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Declarer les interfaces des tables pour le compilateur de spip
 *
 * @param array $interface
 * @return array
 */
function amis_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['amis'] = 'amis';
	return $interface;
}

/**
 * Declaration des tables principales du plugin
 *
 * @param array $tables_principales
 * @return array
 */
function amis_declarer_tables_principales($tables_principales){
	// la table des amis a une cle primaire id_auteur partagee avec la table auteurs
	$spip_amis = array(
			"id_auteur"	=> "bigint(21) NOT NULL",
			"id_ami"	=> "bigint(21) NOT NULL",
			"statut"	=> "varchar(10) DEFAULT '' NOT NULL",
			"date" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
	
	$spip_amis_key = array(
			"PRIMARY KEY"	=> "id_auteur, id_ami",
			"KEY id_mot"	=> "id_ami");
	
	$tables_principales['spip_amis'] = array('field'=>&$spip_amis,'key'=>$spip_amis_key);
	return $tables_principales;
}


?>