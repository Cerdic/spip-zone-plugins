<?php
/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - 2010
 * Auteur Cyril MARION - Ateliers CYM
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function spipmine_declarer_tables_interfaces($interface){

	$interface['tables_jointures']['spip_rubriques'][] = 'spipmine_clients_rubriques';
	$interface['tables_jointures']['spipmine_clients'][] = 'spipmine_clients_rubriques';	
	$interface['tables_jointures']['spipmine_clients_rubriques'][] = 'rubriques';
	$interface['tables_jointures']['spipmine_clients_rubriques'][] = 'spipmine_clients';
	
	return $interface;
}

?>