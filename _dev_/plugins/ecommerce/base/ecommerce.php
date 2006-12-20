<?php
/*
 * Boutique
 * version plug-in de spip_boutique
 *
 * Auteur : RIEFFEL Laurent
 * 
 * 
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

//
// Boutique
//


$spip_ecommerce_sessions = array(
	"id_session" 			=> "bigint(21) NOT NULL",
	"code_session"			=> "bigint (21)",
	"categorie"				=> "bigint (21)",
	"nom"					=> "VARCHAR(50)",
	"prenom"				=> "VARCHAR(50)",
	"email"				=> "VARCHAR(100)",
	"news"				=> "bigint (21)",
	"adresse_livraison"		=> "text",
	"code_postal_livraison"		=> "VARCHAR(50)",
	"ville_livraison"			=> "VARCHAR(50)",
	"pays_livraison"			=> "VARCHAR(50)",	
	"zone"				=> "varchar (50)",
	"telephone"				=> "varchar (25)",
	"adresse_facturation"		=> "text",
	"code_postal_facturation"	=> "VARCHAR(50)",
	"ville_facturation"		=> "VARCHAR(50)",
	"pays_facturation"		=> "VARCHAR(50)",	
	"statut" 				=> "varchar(50)",
	"maj" 				=> "TIMESTAMP",
	"transaction" 			=> "varchar(50)");

$spip_ecommerce_sessions_key = array(
	"PRIMARY KEY" => "id_session");

$spip_ecommerce_paniers = array(
	"id_panier"		=> "bigint(21) NOT NULL",
	"id_session" 	=> "bigint(21)",
	"id_article" 	=> "bigint(21)",
	"pointure"	 	=> "bigint(21)",
	"quantite" 		=> "bigint(21)");

$spip_ecommerce_paniers_key = array(
	"PRIMARY KEY" => "id_panier");


global $tables_principales;

$tables_principales['spip_ecommerce_paniers'] = array(
	'field' => &$spip_ecommerce_paniers,
	'key' => &$spip_ecommerce_paniers_key);

$tables_principales['spip_ecommerce_sessions'] = array(
	'field' => &$spip_ecommerce_sessions,
	'key' => &$spip_ecommerce_sessions_key);

global $table_des_tables;
$table_des_tables['ecommerce']='ecommerce';
$table_des_tables['ecommerce_sessions'] = 'ecommerce_sessions';
$table_des_tables['ecommerce_paniers'] = 'ecommerce_paniers';

?>
