<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2019                                                     *
 *  JLuc                                                                   *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
\***************************************************************************/

/**
 * Action pour exécuter tout de suite un certain nombre de jobs de la job_queue,
 * relatifs à une fonction spécifique
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Executer une sélection de jobs immédiatement, sélectionnés par leur 'fonction'
 *
 * @return void
 */
function action_accelerer_jobs_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	list ($function, $nb) = explode('/', $securiser_action());
	spip_log("### (function, nb) = ($function, $nb)", 'accelerer_job');
	if (!$function or !is_string($function)) {
		spip_log("manque fonction dans action_accelerer_job_dis t: nb=$nb", 'accelerer_job');
	}
	if (!intval($nb)) {
		spip_log("manque nb dans action_accelerer_job_dist($function, $nb)", 'accelerer_job');
	}
	$nb=intval($nb);

	include_spip('inc/queue');
	include_spip('inc/genie');

	$jobs = sql_allfetsel('*', 'spip_jobs', "fonction='".addslashes($function)."'", '', 'priorite DESC,date', "0,$nb");
	$id_jobs = array();
	foreach ($jobs as $job) {
		$id_jobs[] = $job['id_job'];
	}
	# spip_log('id_jobs récupérés : '.print_r($id_jobs, 1), 'accelerer_job');
	queue_schedule($id_jobs);
}
