<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

// Pipeline declarer_tables_principales
function gmap_declarer_tables_principales($tables_principales)
{
	// spip_gmap_points : points géographiques
    $gmap_points = array(
    	"id_point" => "bigint(21) NOT NULL AUTO_INCREMENT",
		"longitude" => "double DEFAULT '0.0' NOT NULL",
		"latitude" => "double DEFAULT '0.0' NOT NULL",
    	"zoom" => "tinyint(4) DEFAULT '0' NOT NULL",
    	"id_type_point" => "bigint(21) DEFAULT '0' NOT NULL"
    	);
    $gmap_points_key = array(
    	"PRIMARY KEY" => "id_point"
    	);
    $gmap_points_join = array(
    	"id_point"=>"id_point",
    	"id_type_point"=>"id_type_point"
    	);
    $tables_principales['spip_gmap_points'] = array(
    	'field' => &$gmap_points,
    	'key' => &$gmap_points_key,
    	'join' => &$gmap_points_join
    	);
	
	return $tables_principales;
}

// Pipeline declarer_tables_auxiliaires
function gmap_declarer_tables_auxiliaires($tables_auxiliaires)
{
	// Lien entre les points et les autres entitiés SPIP
    $spip_gmap_points_liens = array(
    	"id_point" => "bigint(21) DEFAULT '0' NOT NULL",
    	"id_objet" => "bigint(21) DEFAULT '0' NOT NULL",
    	"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL"
		);
    $spip_gmap_points_liens_key = array(
    	"PRIMARY KEY" => "id_point,id_objet,objet",
    	"KEY id_point" => "id_point",
    	"KEY id_objet" => "objet,id_objet"
		);
    $tables_auxiliaires['spip_gmap_points_liens'] = array(
    	'field' => &$spip_gmap_points_liens,
    	'key' => &$spip_gmap_points_liens_key
		);

	// spip_gmap_types : types prédéfinis
    $gmap_types = array(
    	"id_type_point" => "bigint(21) NOT NULL AUTO_INCREMENT",
		"objet" => "varchar(25) DEFAULT '' NOT NULL",
    	"nom" => "varchar(50) NOT NULL",
    	"descriptif" => "text DEFAULT '' NOT NULL",
		"visible" => "varchar(3) DEFAULT 'oui' NOT NULL",
		"priorite" => "tinyint(4) DEFAULT 99 NOT NULL",
    	);
    $gmap_types_key = array(
    	"PRIMARY KEY" => "id_type_point",
		"KEY objet" => "objet"
    	);
    $tables_auxiliaires['spip_gmap_types'] = array(
    	'field' => &$gmap_types,
    	'key' => &$gmap_types_key,
    	);
	
	// spip_gmap_labels : données custom sur un point
    $gmap_labels = array(
    	"id_point" => "bigint(21) NOT NULL",
    	"descriptif" => "text NOT NULL"
    	);
    $gmap_labels_key = array(
    	"PRIMARY KEY" => "id_point",
    	);
    $tables_auxiliaires['spip_gmap_labels'] = array(
    	'field' => &$gmap_labels,
    	'key' => &$gmap_labels_key,
    	);

	return $tables_auxiliaires;
}

// Pipeline declarer_tables_interfaces
function _declarer_interfaces_directes(&$interface) // accès direct à la table gmap_points
{
    // Nommage de la table
	$interface['table_des_tables']['gmap_points'] = 'gmap_points';
	$interface['table_des_tables']['geopoints'] = 'gmap_points';
	
	// Ajout des possibilités de jointures avec les tables de base
    $interface['tables_jointures']['spip_gmap_points'][]= 'gmap_points_liens';
    $interface['tables_jointures']['spip_gmap_points'][]= 'gmap_types';
    $interface['tables_jointures']['spip_gmap_points'][]= 'gmap_labels';
    $interface['tables_jointures']['spip_rubriques'][]= 'gmap_points_liens';
    $interface['tables_jointures']['spip_articles'][]= 'gmap_points_liens';
    $interface['tables_jointures']['spip_breves'][]= 'gmap_points_liens';
    $interface['tables_jointures']['spip_documents'][]= 'gmap_points_liens';
    $interface['tables_jointures']['spip_mots'][]= 'gmap_points_liens';
    $interface['tables_jointures']['spip_auteurs'][]= 'gmap_points_liens';
	
	// Aliases
	$interface['exceptions_des_tables']['gmap_points']['objet'] = array('gmap_points_liens', 'objet');
	$interface['exceptions_des_tables']['gmap_points']['id_objet'] = array('gmap_points_liens', 'id_objet');
	$interface['exceptions_des_tables']['gmap_points']['type_point'] = array('gmap_types', 'nom');
	$interface['exceptions_des_tables']['gmap_points']['descriptif'] = array('gmap_types', 'descriptif');
	$interface['exceptions_des_tables']['gmap_points']['visible'] = array('gmap_types', 'visible');
	$interface['exceptions_des_tables']['gmap_points']['priorite'] = array('gmap_types', 'priorite');
	$interface['exceptions_des_tables']['geopoints']['objet'] = array('gmap_points_liens', 'objet');
	$interface['exceptions_des_tables']['geopoints']['id_objet'] = array('gmap_points_liens', 'id_objet');
	$interface['exceptions_des_tables']['geopoints']['type_point'] = array('gmap_types', 'nom');
	$interface['exceptions_des_tables']['geopoints']['descriptif'] = array('gmap_types', 'descriptif');
	$interface['exceptions_des_tables']['geopoints']['visible'] = array('gmap_types', 'visible');
	$interface['exceptions_des_tables']['geopoints']['priorite'] = array('gmap_types', 'priorite');
}
function _declarer_interfaces_liens(&$interface) // accès direct à la table gmap_points_liens
{
    // Nommage de la table
	// ATTENTION : l'utilisation de ce tableau est à l'inverse de ce 
	// qui est décrit dans doc.spip.org. On donne d'abord l'alias puis
	// à quoi il correspond.
	$interface['table_des_tables']['geotest'] = 'gmap_points_liens';
	// Ce lien sert à ce que la boucle GEOTEST accepte les critères habituels tels que id_article
}
function gmap_declarer_tables_interfaces($interface)
{
	// Définitions pour l'accès direct à la table gmap_points
	_declarer_interfaces_directes($interface);
	
	// Définitions pour l'accès direct à la table gmap_points_liens
	_declarer_interfaces_liens($interface);
	
	return $interface;
}

?>