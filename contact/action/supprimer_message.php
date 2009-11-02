<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

// Permet de supprimer un message de contact.
function action_supprimer_message() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_message = $securiser_action();

	sql_delete("spip_messages", "id_message=".sql_quote($id_message));
	sql_delete("spip_auteurs_messages", "id_message=".sql_quote($id_message));
}

?>
