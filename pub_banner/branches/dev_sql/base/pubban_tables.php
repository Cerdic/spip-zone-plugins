<?php
/**
 * @name 		Tables
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function pubban_declarer_tables_interfaces($interface){
	$config = $GLOBALS['_PUBBAN_CONF'];
	$interface['table_des_tables']['spip_pubban_publicites'] = $config['table_pub'];
	$interface['table_des_tables']['spip_pubban_emplacements'] = $config['table_empl'];
	$interface['table_des_tables']['spip_pubban_stats'] = $config['table_stats'];
	$interface['table_des_tables']['spip_pubban_pub_empl'] = $config['table_join'];
	return($interface);
}

function pubban_declarer_tables_principales($tables_principales){
	$config = $GLOBALS['_PUBBAN_CONF'];

	$spip_table_pubban = array(
		"id_pub"			=> "int(21) NOT NULL",
		"statut"			=> "varchar(100) NOT NULL default '1inactif'",
		"url"				=> "varchar(200) NOT NULL default ''",
		"blank"				=> "enum('non','oui') NOT NULL default 'oui'",
		"titre"				=> "varchar(200) NOT NULL default ''",
		"objet" 			=> "text NOT NULL",
		"type"				=> "varchar(60) NOT NULL default 'img'",
		"illimite"			=> "enum('non','oui') NOT NULL default 'non'",
		"affichages"		=> "bigint(20) NOT NULL default '0'",
		"affichages_restant" => "bigint(20) NOT NULL default '0'",
		"clics"				=> "bigint(20) NOT NULL default '0'",
		"clics_restant"		=> "bigint(20) NOT NULL default '0'",
		"date_debut"		=> "varchar(10) NOT NULL default ''",
		"date_fin"			=> "varchar(10) NOT NULL default ''",
		"date_add"			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"maj"				=> "TIMESTAMP",
	);
	$spip_table_pubban_key = array(
		"PRIMARY KEY" 	=> "id_pub",
		"KEY titre" 	=> "titre",
	);
	$spip_table_pubban_join = array(
		"id_pub" 	=> "id_pub",
	);
	$tables_principales[$config['table_pub']] = array(
		'field' 	=> &$spip_table_pubban,
		'key' 		=> &$spip_table_pubban_key,
		'join'		=> &$spip_table_pubban_join
	);
        
	$spip_table_pubban_empl = array(
		"id_empl"		=> "int(21) NOT NULL",
		"statut"		=> "varchar(100) NOT NULL default '1inactif'",
		"titre"			=> "varchar(30) NOT NULL default ''",
		"titre_id"		=> "varchar(30) NOT NULL default ''",
		"width"			=> "bigint(5) NOT NULL default '0'",
		"height"		=> "bigint(5) NOT NULL default '0'",
		"ratio_pages"	=> "int(3) NOT NULL default '0'",
		"maj"			=> "TIMESTAMP",
	);
	$spip_table_pubban_empl_key = array(
		"PRIMARY KEY" 	=> "id_empl",
//		"KEY titre_id" 	=> "titre_id",
	);
	$spip_table_pubban_empl_join = array(
		"id_empl" 	=> "id_empl",
	);
	$tables_principales[$config['table_empl']] = array(
		'field' 	=> &$spip_table_pubban_empl,
		'key' 		=> &$spip_table_pubban_empl_key,
		'join'		=> &$spip_table_pubban_empl_join,
	);

	$spip_table_pubban_stats = array(
		"id_empl"		=> "int(21) NOT NULL",
		"date"			=> "date NOT NULL default '0000-00-00'",
		"jour"			=> "int(3) NOT NULL",
		"clics"			=> "bigint(20) NOT NULL default '0'",
		"affichages"	=> "bigint(20) NOT NULL default '0'",
	);
	$spip_table_pubban_stats_key = array(
		"KEY id_empl" 	=> "id_empl",
	);
	$tables_principales[$config['table_stats']] = array(
		'field' 	=> &$spip_table_pubban_stats,
		'key' 		=> &$spip_table_pubban_stats_key,
	);

	$new_pub_emp = array(
		"id_pub" => "int(21) NOT NULL",
		"id_empl" => "int(21) NOT NULL"
	);
	$new_pub_emp_cles = array(
		"PRIMARY KEY" => "id_pub, id_empl"
	);
   	$tables_principales[$config['table_join']] = array(
		'field' => &$new_pub_emp,
		'key' => &$new_pub_emp_cles
	);

	return($tables_principales);
}
?>