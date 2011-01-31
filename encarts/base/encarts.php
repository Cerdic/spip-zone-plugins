<?php
/*
 * Plugin Encarts
 * (c) 2011 Camille Lafitte, Cyril Marion
 * Distribue sous licence GPL
 *
 */

include_spip('inc/meta');
include_spip('base/create');

function encarts_declarer_tables_interfaces($interface){
	include_spip('encarts_fonctions');

	$interface['table_des_tables']['encarts'] = 'encarts';
	
	$interface['tables_jointures']['spip_articles'][]= 'encarts';
	$interface['tables_jointures']['spip_articles']['id_article']= 'encarts_liens';
	$interface['tables_jointures']['spip_encarts'][]= 'articles';
	// peut etre faut il plus de déclarations pour lier un encart à des articles ?
	
	// application du filtre pour le style des encarts
	$interface['table_des_traitements']['TEXTE']['articles'] = 
		str_replace('%s', 'style_encarts(%s)', (isset($interface['table_des_traitements']['TEXTE']['articles'] ) ? $interface['table_des_traitements']['TEXTE']['articles'] : $interface['table_des_traitements']['TEXTE'][0] ));

	return $interface;
}

function encarts_declarer_tables_principales($tables_principales){

	//-- Table encarts ------------------------------------------
	$encarts = array(
		"id_encart" 	=> "bigint(21) NOT NULL auto_increment",
		"date" 			=> "datetime NOT NULL default '0000-00-00 00:00:00'",
		"titre" 		=> "tinytext DEFAULT '' NOT NULL",
		"texte" 		=> "text DEFAULT '' NOT NULL",
		"maj"			=> "TIMESTAMP"
		);
	$encarts_key = array(
		"PRIMARY KEY"	=> "id_encart"
		);
	$tables_principales['spip_encarts'] =
		array('field' => &$encarts,'key' => &$encarts_key,'join' => array('id_encart'=>'id_encart'));

	return $tables_principales;

}

function encarts_declarer_tables_auxiliaires($tables_auxiliaires){

	//-- Table encarts_liens ------------------------------------------
	$encarts_liens = array(
		"id_encart" 	=> "bigint(21) NOT NULL auto_increment",
		"id_objet"		=> "bigint(21) NOT NULL default '0'",
		"objet"			=> "varchar(25) NOT NULL default ''",
		"vu"			=> "enum('non','oui') NOT NULL default 'non'"
		);
	$encarts_liens_key = array(
		"PRIMARY KEY"	=> "id_encart, id_objet, objet"
		);
	$tables_auxiliaires['spip_encarts_liens'] =
		array('field' => &$encarts_liens, 'key' => &$encarts_liens_key);
	
	return $tables_auxiliaires;
}

function encarts_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version])) {
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
		spip_log('Version actuelle : '.$current_version,'encarts');
	}
	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
		spip_log('Base de donnees encarts correctement creee','encarts');
	}
}

function encarts_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_encarts");
	sql_drop_table("spip_encarts_liens");

	spip_log('Plugin encarts correctement desinstalle.','encarts');
	effacer_meta($nom_meta_base_version);
}


?>