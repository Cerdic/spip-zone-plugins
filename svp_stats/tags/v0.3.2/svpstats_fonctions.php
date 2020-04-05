<?php

/**
 * Retourner le nombre de jours entre chaque actualisation des stats
 * si le cron est activé.
 *
 * @return int
 *         nb de jours (sinon 0)
 */
function filtre_svpstats_periode_actualisation_stats() {
	include_spip('genie/svpstats_taches_generales_cron');
	return _SVP_CRON_ACTUALISATION_STATS ? _SVP_PERIODE_ACTUALISATION_STATS : 0;
}
