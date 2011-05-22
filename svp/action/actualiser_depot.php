<?php
/**
 * Action de mises a jour en base de donnees des plugins du depot
 *
 */
function action_actualiser_depot_dist(){

	// Securisation: aucun argument attendu
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Actualisation des plugins du depot
	// Le depot lui-meme n'est jamais mis a jour via le fichier XML une fois que
	// la premiere insertion a ete effectuee. Pour le depot il faut ensuite passer
	// par le formulaire d'edition
	if ($id_depot = intval($arg)) {
		include_spip('inc/svp_depoter');
		svp_actualiser_depot($id_depot);
		// On consigne l'action
		spip_log("ACTION ACTUALISER DEPOT (manuel) : id_depot = ". $id_depot, 'svp_actions.' . _LOG_INFO);
	}
}

?>