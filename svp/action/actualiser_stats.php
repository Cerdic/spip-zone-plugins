<?php
/**
 * Action de mises a jour en base de donnees des plugins du depot
 *
 */
function action_actualiser_stats_dist(){

	// Securisation: aucun argument attendu
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Actualisation des statistiques d'utilisation des plugins en provenance de 
	// stats.spip.org
	// On verife tout de meme qu'il y a au moins un depot
	if (sql_countsel('spip_depots')) {
		include_spip('inc/svp_depoter');
		svp_actualiser_stats();
		// On consigne l'action
		spip_log("ACTION ACTUALISER STATS (manuel)", 'svp_actions.' . _LOG_INFO);
	}
}

?>