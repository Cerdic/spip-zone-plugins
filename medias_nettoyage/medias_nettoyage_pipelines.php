<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * On se greffe au pipeline taches_generales_cron pour lancer nos tâches
 * 
 * @param  array $taches
 * 
 * @return array
 */
function medias_nettoyage_taches_generales_cron($taches) {

	$taches['medias_deplacer_orphelins'] = 6 * 3600; // toutes les 6h
	$taches['medias_deplacer_obsoletes'] = 30 * 24 * 3600; // tous les 30 jours

	return $taches;
}

?>