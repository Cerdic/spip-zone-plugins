<?php
/**
 * Plugin Projets - Tickets
 * Licence GPL
 *
 * Declaration des tables du plugin
 *
 */

function projets_tickets_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_tickets'][] = 'projets_liens';

	return $interface;
}
?>