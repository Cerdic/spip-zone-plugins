<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


/* function cameras_declarer_tables_interfaces($interfaces){
	 $interfaces['table_des_tables']['cameras'] = 'cameras'; 
	return $interfaces;
} */


function cameras_declarer_tables_objets_sql($tables){
	$tables['cameras'] = array(
		'principale' => "oui",
		'field'=> array(
			"id_camera"		=> "bigint(21) NOT NULL",
			"id_osm" 		=> "bigint(21) NOT NULL",
			
			"titre" 		=> "tinytext DEFAULT '' NOT NULL",
			"description"	=> "text DEFAULT '' NOT NULL",
			
			"op_name" 		=> "tinytext DEFAULT '' NOT NULL",
			"op_type" 		=> "varchar(10) DEFAULT '' NOT NULL",
			"surveillance"	=> "tinytext DEFAULT '' NOT NULL",
			"install_date" 	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			
			"apparence"		=> "varchar(10) DEFAULT '' NOT NULL",
			"zoom"			=> "ENUM('yes','no') DEFAULT 'no' NOT NULL",
			"orientable"	=> "ENUM('yes','no') DEFAULT 'no' NOT NULL",
			"lat" 			=> "double NULL NULL",
			"lon" 			=> "double NULL NULL",
			"direction" 	=> "double NULL NULL",
			"angle"			=> "double NULL NULL",
			"height"		=> "double NULL NULL",
			
			"zone"			=> "tinytext DEFAULT '' NOT NULL",
			
			"date" 			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"statut" 		=> "varchar(255) DEFAULT '0' NOT NULL",
			"maj"			=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_camera",
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",
		'champs_editables' => array(
			"titre", "description", "op_name", "op_type", "surveillance", "install_date", "apparence", "zoom", "orientable", "lat", "lon", "direction", "angle", "height", "zone"   
		),
		'champs_versionnes' => array(
            "titre", "description", "op_name", "op_type", "surveillance", "install_date", "apparence", "zoom", "orientable", "lat", "lon", "direction", "angle", "height",
		),
		
		'statut'=> array(
			array(
				'champ' => 'statut',
				'publie' => 'publie',
				'previsu' => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'statut_textes_instituer' => 	array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'prop' => 'texte_statut_propose_evaluation',
			'publie' => 'texte_statut_publie',
			'refuse' => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'texte_changer_statut' => 'camera:texte_changer_statut',
		'texte_creer_associer' => 'camera:texte_creer_associer',
	);
	return $tables;
}



?>