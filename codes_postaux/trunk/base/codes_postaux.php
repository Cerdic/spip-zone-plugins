<?php



 function codes_postaux_declarer_tables_interfaces($interfaces){

$interfaces['table_des_tables']['codes_postaux'] = 'codes_postaux';
include_spip('inc/plugin');
if(in_array('COG',array_keys(liste_plugin_actifs())))
	$interfaces['tables_jointures']['code_postals'][]= 'cog_communes_liens';

return  $interfaces;
}




	
/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function codes_postaux_declarer_tables_objets_sql($tables){
	
	$tab_champs=array('code','titre');
	$tables['spip_codes_postaux'] = array(
		'type' => 'code_postal',
		'principale' => "oui",
		'page'=>'',
		'table_objet_surnoms' => array('codespostaux', 'code_postal'), // table_objet('code_postal') => 'code_postaux' 
		'field'=> array(
			"id_code_postal"=> "bigint(21) NOT NULL COMMENT 'Identifiant du code Postal'",
			"code"          => "varchar(20)  NOT NULL COMMENT 'Code'",
			"titre"         => "varchar(100) NOT NULL COMMENT 'Titre'",
			"maj"           => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_code_postal",
			"KEY code" 	=> "code"
		),
		'titre' => 'titre, \'\' AS lang',
		 #'date' => "",
		'champs_editables'  => $tab_champs,
		'champs_versionnes' => $tab_champs,
		'rechercher_champs' => array('titre' => 8, 'code' => 5),
		'tables_jointures' => array(),
		
	);


return $tables;

}







?>
