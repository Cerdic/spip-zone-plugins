<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function medias_deplacer_documents_orphelins_taches_generales_cron($taches) {
	$taches['medias_deplacer_documents_orphelins'] = 2 * 3600; // toutes les 2h
	return $taches;
}

?>