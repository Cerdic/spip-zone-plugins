<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_ticket_forum_extraire_titre($id_ticket){

	$titre = sql_fetsel('titre', 'spip_tickets', "statut IN ('ouvert','resolu','ferme') AND id_ticket = $id_ticket");
	spip_log($titre,'forums');
	return $titre;
}
?>