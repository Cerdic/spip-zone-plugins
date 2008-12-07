<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Yohann Prigent (potter64) repris des travaux de Bernard Blazin (http://www.plugandspip.com )
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


if (!defined("_ECRIRE_INC_VERSION")) return;

function guestbook_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['guestbook']='guestbook';
	$interface['table_des_tables']['guestbook_reponses']='guestbook_reponses';
	return $interface;
}

function guestbook_declarer_tables_principales($tables_principales){
	$spip_guestbook = array(
		"id_message" 	=> "bigint(21) NOT NULL auto_increment",
		"message" 	=> "text NOT NULL",
		"email" 	=> "text NOT NULL",
		"nom" => "VARCHAR(255) NOT NULL DEFAULT '0'",
		"ville" => "text NOT NULL",
		"statut" => "VARCHAR(8) NOT NULL",
		"ip" => "VARCHAR(255) NOT NULL",
		"note" => "VARCHAR(255) NOT NULL",
		"date"	=> "DATETIME");
	
	$spip_guestbook_key = array(
		"PRIMARY KEY" => "id_message");
	
	$tables_principales['spip_guestbook'] = array(
		'field' => &$spip_guestbook,
		'key' => &$spip_guestbook_key);
		
	return $tables_principales;
}

function guestbook_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_guestbook_reponses = array(
		"id_reponse" => "BIGINT(21) NOT NULL auto_increment",
		"id_message" => "BIGINT(21) NOT NULL",
		"id_auteur" => "BIGINT(21) NOT NULL",
		"message" =>  "TEXT",
		"statut" => "VARCHAR(8) NOT NULL",
		"date"	=> "DATETIME");
	
	$spip_guestbook_reponses_key = array(
		"PRIMARY KEY" 	=> "id_reponse, id_message",
		"KEY id_auteur" => "id_auteur");
	
	$tables_auxiliaires['spip_guestbook_reponses'] = array(
		'field' => &$spip_guestbook_reponses,
		'key' => &$spip_guestbook_reponses_key);

	return $tables_auxiliaires;
}

?>