<?php

/*
 * Plugin Commandes
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_commande_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_commande = $securiser_action();

	// suppression
	if ($id_commande = intval($id_commande)) {
		sql_delete('spip_commandes', 'id_commande=' . $id_commande);
		sql_delete('spip_commandes_details', 'id_commande=' . $id_commande);
	}

}

?>
