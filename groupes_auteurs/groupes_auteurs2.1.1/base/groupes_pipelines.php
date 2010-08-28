<?php
function groupes_declarer_tables_principales($tables_principales){
	//TABLE groupes
	$new_groupes = array(
		"id_groupe" => "BIGINT(21) NOT NULL",
		"nom" => "VARCHAR(100) NOT NULL"
	);

	$new_groupes_cles = array(
		"PRIMARY KEY" => "id_groupe"
	);
	
	$new_groupes_join = array(
		"id_groupe"=>"id_groupe"
	);
	
	$tables_principales['spip_groupes'] = array(
		'field'=>&$new_groupes,
		'key'=>&$new_groupes_cles,
		'join'=>&$new_groupes_join
	);
	
	
	//TABLE GROUPES AUTEURS
	$new_groupes_auteurs = array(
		"id_groupe"=>"BIGINT(21) NOT NULL",
		"id_auteur"=>"BIGINT(21) NOT NULL"
	);
	$new_groupes_auteurs_cle = array(
		'PRIMARY KEY'=>'id_groupe, id_auteur',
		'KEY id_groupe'=> 'id_groupe'
	);
	
	$tables_principales['spip_groupes_auteurs'] = array(
		'field' => &$new_groupes_auteurs,
		'key' => &$new_groupes_auteurs_cle
	);
	
	
	//TABLE GROUPES_ZONES
	$new_groupes_zones = array(
		"id_groupe"=>"BIGINT(21) NOT NULL",
		"id_zone"=>"BIGINT(21) NOT NULL"
	);
	$new_groupes_zones_cle = array(
		'PRIMARY KEY'=>'id_groupe, id_zone',
		'KEY id_groupe'=> 'id_groupe'
	);
	$tables_principales['spip_groupes_zones'] = array(
		'field' => &$new_groupes_zones,
		'key' => &$new_groupes_zones_cle
	);
	
	return $tables_principales;
}


function groupes_declarer_tables_auxiliaires($tables_auxiliaires) {
	
	return $tables_auxiliaires;
}


function groupes_declarer_tables_interfaces($tables_interfaces) {
	$tables_interfaces['table_des_tables']['groupes'] = 'groupes';
	$tables_interfaces['table_des_tables']['groupes_auteurs'] = 'groupes_auteurs';
	$tables_interfaces['table_des_tables']['groupes_zones'] = 'groupes_zones';
	return $tables_interfaces;
}
?>