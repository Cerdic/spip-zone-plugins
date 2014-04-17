<?php


/**
 * Declaration des tables principales
 *
 * @param array $tables_principales
 * @return array
 */

function cp_declarer_tables_principales($tables_principales){

//////////   Communes   //////////

	$table_cp = array(
		"id_code_postal"=> "INT(10) UNSIGNED NOT NULL COMMENT 'Identifiant du code Postal'",
		"code"          => "VARCHAR( 20 )  NOT NULL COMMENT 'Code'",
		"titre"         => "VARCHAR( 100 ) NOT NULL COMMENT 'Titre'");

	$table_cp_key = array(
		"PRIMARY KEY" 	=> "id_code_postal",
		"KEY code" 	=> "code"
		);

	$table_cp_join = array(
		"id_code_postal"=>'id_code_postal'
	);


	$tables_principales['spip_code_postals'] = array(
		'field' => $table_cp,
		'key' => $table_cp_key,
		'join' => $table_cp_join
		);


return $tables_principales;

}




 function cp_declarer_tables_interfaces($tables_interfaces){

$tables_interfaces['table_des_tables']['code_postals'] = 'code_postals';
include_spip('inc/plugin');
if(in_array('cog',liste_plugin_actifs()))
	$tables_interfaces['tables_jointures']['code_postals'][]= 'cog_communes_liens';

return  $tables_interfaces;
}




?>
