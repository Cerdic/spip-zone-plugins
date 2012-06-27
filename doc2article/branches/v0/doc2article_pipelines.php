<?php

/**
 * Pipeline Cron de doc2article
 *
 * Vérifie la présence à intervalle régulier la présence de documents à importer
 * dans la file d'attente de la table spip_doc2article
 *
 * @return L'array des taches complété
 * @param array $taches_generales Un array des tâches du cron de SPIP
 */
function doc2article_taches_generales_cron($taches_generales){
	$taches_generales['doc2article'] = 3*60; // toutes les 3 minutes
	return $taches_generales;
}

?>