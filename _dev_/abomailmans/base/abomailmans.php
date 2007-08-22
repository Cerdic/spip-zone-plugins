<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
*/

$spip_abomailmans = array(
	"id_abomailman" 		=> "bigint(21) NOT NULL",
	"titre" 		=> "varchar(255) NOT NULL",
	"descriptif" 	=> "text",
	"email"			=> "varchar(255)",
	"maj" 			=> "TIMESTAMP");

$spip_abomailmans_key = array(
	"PRIMARY KEY" => "id_abomailman");

global $tables_principales;
$tables_principales['spip_abomailmans'] = array(
	'field' => &$spip_abomailmans,
	'key' => &$spip_abomailmans_key);



global $table_des_tables;
$table_des_tables['abomailmans']='abomailmans';


//
// <BOUCLE(ABONNEMENTSMAILMANS)>
//

function boucle_ABOMAILMANS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_abomailmans";
	$email_liste = $id_table .'.email';
	$boucle->where[]= array("'IS NOT'", "'$email_liste'", "'NULL'");

	return calculer_boucle($id_boucle, $boucles); 
}

?>