<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function medias_nettoyage_taches_generales_cron($taches) {
	$taches['medias_deplacer_orphelins'] = 2 * 3600; // toutes les 2h
	return $taches;
}

?>