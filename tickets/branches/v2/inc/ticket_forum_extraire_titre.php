<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2012
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_ticket_forum_extraire_titre_dist($id_ticket){

	$titre = sql_getfetsel('titre', 'spip_tickets', array(
		'id_ticket = ' . sql_quote($id_ticket),
		sql_in('statut', array('ouvert','resolu','ferme'))
	));

	return $titre;
}
?>
