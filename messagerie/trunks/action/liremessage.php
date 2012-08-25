<?php
/*
 * Plugin messagerie / gestion des messages
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


/**
 * Signaler la lecture d'un message (marque comme lu)
 *
 */
function action_liremessage_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_message = $securiser_action();
	include_spip('inc/messages');
	messagerie_marquer_lus($GLOBALS['visiteur_session']['id_auteur'],array(intval($id_message)));
}

?>