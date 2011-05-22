<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function svp_taches_generales_cron($taches_generales) {

	// Ajout de la tache CRON de mise a jour reguliere de tous les depots de la base
	// Par defaut, toutes les 6h
	// Conditionnee a la variable de configuration
	if (_SVP_CRON_ACTUALISATION_DEPOTS)
		$taches_generales['svp_actualiser_depots'] = _SVP_PERIODE_ACTUALISATION_DEPOTS*3600;

	// Ajout de la tache CRON de mise a jour reguliere des statistiques d'utilisation des plugins
	// Par defaut, toutes les 7 jours
	// Conditionnee a la variable de configuration
	if (_SVP_CRON_ACTUALISATION_STATS)
		$taches_generales['svp_actualiser_stats'] = _SVP_PERIODE_ACTUALISATION_STATS*24*3600;

	return $taches_generales;
}

?>
