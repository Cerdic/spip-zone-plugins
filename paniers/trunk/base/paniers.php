<?php
/**
 * Plugin Paniers
 * (c) 2013 Cédric Morin / Les Développements Durables
 * Licence GPL V3
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 */
function paniers_declarer_tables_interfaces($interfaces){
	// 'spip_' dans l'index de $tables_principales
	$interfaces['table_des_tables']['paniers'] = 'paniers';
	$interfaces['table_des_tables']['paniers_liens'] = 'paniers_liens';

	//-- Jointures ----------------------------------------------------
	$interfaces['tables_jointures']['spip_auteurs'][]= 'paniers';

	$interfaces['table_date']['paniers'] = 'date';

	return $interfaces;
}

/**
 * Déclaration des tables principales
 */
function paniers_declarer_tables_principales($tables_principales){

	// Un panier peut être "encours", "commande", "paye", "envoye", "retour", "retour_partiel"
	$paniers = array(
		'id_panier'   => "bigint(21) NOT NULL",
		'id_auteur'   => "bigint(21) NOT NULL DEFAULT 0",
		'cookie'      => "varchar(255) NOT NULL DEFAULT ''",
		'statut'      => "varchar(20) NOT NULL DEFAULT 'encours'",
		'date'        => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'maj'         => "TIMESTAMP"
	);

	$paniers_cles = array(
		'PRIMARY KEY' => 'id_panier'
	);

	$tables_principales['spip_paniers'] = array(
		'field'       => &$paniers,
		'key'         => &$paniers_cles,
		'join'=> array(
			'id_panier' => 'id_panier'
		)
	);

	return $tables_principales;
}

/**
 * Déclaration des tables secondaires (liaisons)
 */
function paniers_declarer_tables_auxiliaires($tables){

	$tables['spip_paniers_liens'] = array(
		'field' => array(
			"id_panier"          => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"quantite"           => "int DEFAULT '1' NOT NULL",
		),
		'key' => array(
			"PRIMARY KEY"        => "id_panier,id_objet,objet",
			"KEY id_panier"      => "id_panier"
		)
	);

	return $tables;
}

?>
