<?php

/**
 * Pipeline Cron de pj2article
 *
 * Vérifie à intervalle régulier la présence de mails à importer
 *
 * @return L'array des taches complété
 * @param array $taches_generales Un array des tâches du cron de SPIP
 */
function pj2article_taches_generales_cron($taches_generales){
	include_spip('inc/config');
	$taches_generales['pj2article'] = lire_config('pj2article/intervalle_cron', 5*60);
	return $taches_generales;
}

function pj2article_doc2article_preparer_article($flux){
	$flux['data']['statut'] = lire_config('pj2article/statut', 'prepa');
	return $flux;
}
