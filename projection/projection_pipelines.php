<?php

/*
 * Plugin Projection
 * (c) 2012 Fil
 *
 */

$GLOBALS['projection_post_edition'] = array(
	'spip_articles' => 'projection_export_article'
);

/**
 * 
 * Declarer la tache cron de projection des vieux articles
 * @param array $taches_generales
 * @return array 
 */
function projection_taches_generales_cron($taches_generales){
	$taches_generales['projection'] = 60 * 60; // toutes les heures
	return $taches_generales;
}

/**
 * Pipeline post-edition
 * envoyer une demande de projection dans la queue
 *
 * @param array $x
 * @return array
 */
function projection_post_edition($x) {
	if (isset($x['args']['table_objet'])) {
		$objet = $x['args']['table_objet'];
		$id_objet = $x['args']['id_objet'];

		if (function_exists('ZZZZZjob_queue_add')) {
			job_queue_add('projection', 'projection '.$objet.' '.$id_objet,
				$arguments = array($objet, $id_objet),
				$file = 'inc/projection',
				$no_duplicate = TRUE,
				$time=0,
				$priority=0
			);
		} else {
			include_spip('inc/projection');
			projection($objet, $id_objet);
		}
	}
	return $x;
}


