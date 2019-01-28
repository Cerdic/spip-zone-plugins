<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Mise a jour automatique des depots (CRON)
// - Flag de declenchement
if (!defined('_SVP_CRON_ACTUALISATION_STATS')) {
	define('_SVP_CRON_ACTUALISATION_STATS', true);
}

// - Periode d'actualisation en nombre de jours
if (!defined('_SVP_PERIODE_ACTUALISATION_STATS')) {
	define('_SVP_PERIODE_ACTUALISATION_STATS', 7);
}

function svpstats_taches_generales_cron($taches_generales) {

	// Ajout de la tache CRON de mise a jour reguliere des statistiques d'utilisation des plugins
	// Par defaut, toutes les 7 jours
	// Conditionnee a la variable de configuration
	if (_SVP_CRON_ACTUALISATION_STATS)
		$taches_generales['svp_actualiser_stats'] = _SVP_PERIODE_ACTUALISATION_STATS*24*3600;

	return $taches_generales;
}
