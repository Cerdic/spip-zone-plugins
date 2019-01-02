<?php

/**
* Plugin Bannieres
*
* Copyright (c) 2009
* François de Montlivault - Jeannot
* Mise a jour Inspiree du plugin chats
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

function bannieres_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['bannieres'] = 'bannieres';	
	$interface['table_des_tables']['bannieres_suivi'] = 'bannieres_suivi';	
	return $interface;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * 
 * Déclaration de l'objet supplémentaire bannieres
 * 
 * @param array $tables
 * 	Le tableau de définition de tous les objets
 * @return array $tables
 * 	Le tableau complété avec notre objet supplémentaire
 */
function bannieres_declarer_tables_objets_sql($tables){

	$tables['spip_bannieres'] = array(
		'type' => 'banniere',
		'principale' => "oui",
		'field'=>array(
			"id_banniere" 	=> "bigint(21) NOT NULL auto_increment",
			"nom" 			=> "VARCHAR(100) DEFAULT '' NOT NULL",
			"email" 		=> "VARCHAR(100) DEFAULT '' NOT NULL",
			"site" 			=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"debut"			=> "date DEFAULT '0000-00-00' NOT NULL",
			"fin"			=> "date DEFAULT '0000-00-00' NOT NULL",
			"clics"			=> "int(11) DEFAULT '0' NOT NULL",
			"affichages"	=> "int(11) DEFAULT '0' NOT NULL",
			"commentaires" 	=> "text DEFAULT '' NOT NULL",
			"creation"		=> "date DEFAULT '0000-00-00' NOT NULL",
			"position"		=> "tinyint(2) NOT NULL default '1'",
			"rayon"			=> "VARCHAR(50) DEFAULT 'int' NOT NULL",
			"diffusion"		=> "text DEFAULT '' NOT NULL",
			"maj" 			=> "TIMESTAMP",
		),
		'key' => array(
			"PRIMARY KEY"        => "id_banniere" 
		),
		'titre' => "nom AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('nom', 'email', 'site', 'fin'),
		'champs_versionnes' => array('nom', 'email', 'site', 'debut', 'fin', 'commentaires', 'position', 'rayon', 'diffusion'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('id_banniere' => 'banniere_suivi'),
	);
	
	return $tables;
}
/**
 * Insertion dans le pipeline declarer_tables_principales
 */
function bannieres_declarer_tables_principales($tables_principales){
	
	$tables_principales['spip_bannieres_suivi'] = array(
		'field'=>array(
			"id_banniere"	=> "bigint(21) NOT NULL",
			"id_auteur"		=> "bigint(21) NOT NULL",
			"ip"			=> "VARCHAR(50) NOT NULL",
			"page"			=> "VARCHAR(255) DEFAULT '' NOT NULL",
			"date"			=> "timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL",
		),
		'key'=>array(
			"KEY"	=> "id_banniere"
		),
		'join'=>array('id_banniere')
	);
	return $tables_principales;
}

?>
