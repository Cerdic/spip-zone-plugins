<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2018                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Action pour exécuter tout de suite un certain nombre d'appels à une fonction spécifique en attente dans la job_queue
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
function action_accelerer_job_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	list ($function, $nb) = explode('/', $securiser_action());
	debug_log ("### (function, nb) = ($function, $nb)", "DEBUG_accelerer_job");
	debug_assert($function and is_string ($function) and (strlen($function)>3), "manque nb dans spip_cron_force : nb=$nb et function=".print_r($function,1));
	debug_assert(intval($nb), "manque nb dans action_accelerer_job_dist($function, $nb)");
	$nb=intval($nb);

	include_spip('inc/queue');
	include_spip('inc/genie');

	$jobs = sql_allfetsel('*', 'spip_jobs', "fonction='".addslashes($function)."'", '', 'priorite DESC,date', "0,$nb");
	debug_assert(is_array($jobs), "Oups pb action_accelerer_job_dist calcule jobs non array : ".print_r($jobs,1).sql_error());
	debug_log ("jobs récupérés : ".print_r($jobs,1), "DEBUG_accelerer_job");
	$id_jobs = array();
	foreach($jobs as $job)
		$id_jobs[] = $job['id_job'];
	debug_log ("id_jobs récupérés : ".print_r($id_jobs,1), "DEBUG_accelerer_job");
	queue_schedule($id_jobs);
}
