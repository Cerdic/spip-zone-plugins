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
 * @param string $args  parm de l'action
 * @return void
 */
function action_accelerer_jobs_dist($args=null) {
	if (is_null ($args)) {
		$securiser_action = charger_fonction ('securiser_action', 'inc');
		$args = $securiser_action();
	}
	if (strpos($args, '/') !== false) {
		list ($function, $nb) = explode('/', $args);
		$nb=intval($nb);
	}
	else {
		$function = $args;
		$nb = 5;
	}
	if (!$function or !is_string($function)) {
		spip_log("manque fonction dans action_accelerer_job_dis t: nb=$nb", 'erreur_accelerer_job'._LOG_ERREUR);
		return;
	}
	spip_log("### (function, nb) = ($function, $nb)", 'accelerer_job');

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
