<?php
/**
 * Plugin Paniers
 * (c) 2013 Cédric Morin / Les Développements Durables
 * Licence GPL V3
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function paniers_commandes_quantites_decimal_declarer_tables_objets_sql($tables) {

	$tables['spip_commandes_details']['field']['quantite'] = "decimal(9,3) DEFAULT '0' NOT NULL";
	return $tables;
}

/**
 * Déclaration des tables secondaires (liaisons)
 */
function paniers_commandes_quantites_decimal_declarer_tables_auxiliaires($tables){

	$tables['spip_paniers_liens']['quantite'] = "decimal(9,3) DEFAULT '1' NOT NULL";

	return $tables;
}

?>
