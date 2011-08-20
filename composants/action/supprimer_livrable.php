<?php

/*
 * Plugin Livrables
 * Licence GPL (c) 2011 Cyril Marion
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_livrable_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_livrable = $securiser_action();

	// suppression
	if ($id_livrable = intval($id_livrable)) {
		sql_delete('spip_livrables', 'id_livrable=' . $id_livrable);
		sql_delete('spip_livrables_details', 'id_livrable=' . $id_livrable);
	}

}

?>
