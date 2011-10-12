<?php
function sitra_select_declarer_tables_principales($tables_principales){
// Table SITRA_OBJETS
$sitra_select_field = array(
	'id_article' => 'bigint(21) NOT NULL',
	'id_selection' => 'varchar(32) NOT NULL',
	'id_categorie' => 'varchar(32) NOT NULL',
	'id_critere' => 'varchar(32) NOT NULL',
	'noisette' => 'varchar(32) NOT NULL',
	'tri' => 'varchar(32) NOT NULL',
	'sens_tri' => 'varchar(1) NOT NULL DEFAULT \'0\'',
	'extra' => 'text NOT NULL'
	);

$sitra_select_key = array(
	'PRIMARY KEY'	=> 'id_article'
	);

$tables_principales['spip_sitra_select_articles'] = array(
	'field' => &$sitra_select_field,
	'key' => &$sitra_select_key
	);

return $tables_principales;

} // fin sitra_declarer_tables_principales

function sitra_select_declarer_tables_interfaces($interface){
	// les noms des tables dans les boucles
	$interface['table_des_tables']['sitra_select_articles'] = 'sitra_select_articles';
	
	return $interface;
}

?>