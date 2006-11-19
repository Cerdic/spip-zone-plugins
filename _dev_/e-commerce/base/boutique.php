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

$spip_boutiques = array(
	"id_boutique" 			=> "bigint(21) NOT NULL",
	"code_boutique"			=> "bigint (21)",
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
	"adresse_facturation"		=> "text",
	"code_postal_facturation"	=> "VARCHAR(50)",
	"ville_facturation"		=> "VARCHAR(50)",
	"pays_facturation"		=> "VARCHAR(50)",	
	"statut" 				=> "varchar(50)",
	"maj" 				=> "TIMESTAMP",
	"transaction" 			=> "varchar(50)");

$spip_boutiques_key = array(
	"PRIMARY KEY" => "id_boutique");

$spip_paniers = array(
	"id_panier"		=> "bigint(21) NOT NULL",
	"id_boutique" 	=> "bigint(21)",
	"id_article" 	=> "bigint(21)",
	"pointure"	 	=> "bigint(21)",
	"quantite" 		=> "bigint(21)");

$spip_paniers_key = array(
	"PRIMARY KEY" => "id_panier");


global $tables_principales;

$tables_principales['spip_paniers'] = array(
	'field' => &$spip_paniers,
	'key' => &$spip_paniers_key);

$tables_principales['spip_boutiques'] = array(
	'field' => &$spip_boutiques,
	'key' => &$spip_boutiques_key);



//-- Relations ----------------------------------------------------
/*
global $table_des_tables;
$table_des_tables['paniers']='paniers';
$table_des_tables['boutiques']='boutiques';

$table_des_tables['paniers']='spip_paniers';
$table_des_tables['boutiques']='spip_boutiques';
*/
?>
