<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

//
// Formulaires : Structure
//
global $tables_principales;
global $tables_auxiliaires;

$spip_smslist_spool = array(
	"id_spool" 	=> "bigint(21) NOT NULL",
	"lot_envoi" 	=> "bigint(21) NOT NULL",
	"tel_to" 	=> "varchar(255) NOT NULL",
	"message" 	=> "varchar(255) NOT NULL",
	"tel_from" 	=> "varchar(255) NOT NULL",
	"compte" 	=> "bigint(21) NOT NULL",
	"date_envoi"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"statut" => "varchar(255) NOT NULL DEFAULT ''",
	"essais" => "bigint(21) NOT NULL",
	"maj" 		=> "TIMESTAMP");

$spip_smslist_spool_key = array(
	"PRIMARY KEY" => "id_spool",
	"UNIQUE destinataire" => "lot_envoi,tel_to" # un seul message envoye par lot par destinataire
	);

$tables_principales['spip_smslist_spool'] = array(
	'field' => &$spip_smslist_spool,
	'key' => &$spip_smslist_spool_key);

//-- Relations ----------------------------------------------------
global $table_des_tables;
$table_des_tables['smslist_spool']='smslist_spool';

?>