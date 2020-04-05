<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_svp_actualiser_stats_dist ($last) {

	// On recupere en base de donnees les statistiques d'utilisation des plugins
	// L'action n'est lancee que si il existe au moins un depot en base
	if (sql_countsel('spip_depots')) {
		include_spip('inc/svp_statistiquer');
		svp_actualiser_stats();
		spip_log("MODULE STATS - ACTION ACTUALISER STATS (automatique)", 'svp_actions.' . _LOG_INFO);
	}

	return 1;
}
