<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     produits_liens
 * @copyright  2015
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Produits_liens\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	
function produits_liens_declarer_tables_interfaces($tables){
	$tables['table_des_tables']['produits_liens'] = 'produits_liens';  
		
	return $tables;
}	
	

/**
 * Déclaration des tables secondaires (liaisons)
 */
function produits_liens_declarer_tables_auxiliaires($tables){
	
	$tables['spip_produits_liens'] = array(
		'field' => array(
			"id_produit" => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"   => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"      => "VARCHAR(25) DEFAULT '' NOT NULL",
			"rang"       => "INT(11) DEFAULT '0' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_produit,id_objet,objet",
			"KEY"  => "id_produit"
		)
	);
	
	return $tables;
}
