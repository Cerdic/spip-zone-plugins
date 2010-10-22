<?php
/**
 * Action de suppression en base de donnees du depot et de ses plugins
 *
 */
function action_supprimer_depot_dist(){

	// Securisation: aucun argument attendu
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Suppression du depot et de ses plugins
	if ($id_depot = intval($arg)) {
		include_spip('inc/svp_depoter');
		svp_supprimer_depot($id_depot);
		if (_SVP_LOG_ACTIONS)
			spip_log("ACTION SUPPRIMER DEPOT (manuel) : id_depot = ". $id_depot, 'svp');
	}
}

?>