<?php

/*
 * Plugin Panier
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_panier_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_panier = $securiser_action();

	// suppression
	if ($id_panier = intval($id_panier)) {
		sql_delete('spip_paniers', 'id_panier=' . $id_panier);
		sql_delete('spip_paniers_liens', 'id_panier=' . $id_panier);
	}

}

?>
