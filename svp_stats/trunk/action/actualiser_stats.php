<?php
/**
 * Action de mises a jour en base de donnees des plugins du depot
 *
 */
function action_actualiser_stats_dist(){

	// Securisation: aucun argument attendu, mais étant donné le bug de la balise
	// #URL_ACTION_AUTEUR il est préférable d'en passer un : on fait donc ça le plus
	// proprement possible en passant l'argument "tout".
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$mode = $securiser_action();

	// Verification des autorisations
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Actualisation des statistiques d'utilisation des plugins en provenance de 
	// stats.spip.org
	// On verife tout de meme qu'il y a au moins un depot
	if (($mode === 'tout') AND sql_countsel('spip_depots')) {
		include_spip('inc/svp_statistiquer');
		svp_actualiser_stats();
		// On consigne l'action
		spip_log("MODULE STATS - ACTION ACTUALISER STATS (manuel)", 'svp_actions.' . _LOG_INFO);
	}
}

?>