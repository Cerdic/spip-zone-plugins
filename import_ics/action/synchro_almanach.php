<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2013                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// honteusement pompé sur http://doc.spip.org/@action_editer_site_dist
function action_synchro_almanach_dist($id_almanach=null) {

	if (is_null($id_almanach)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_alamanach = $securiser_action();
	}


	$id_job = job_queue_add('synchro_a_jour','synchro_a_jour',array($id_synchro),'genie/synchro',true);
	// l'executer immediatement si possible
	if ($id_job) {
		include_spip('inc/queue');
		queue_schedule(array($id_job));
	}
	else {
		spip_log("Erreur insertion synchro_a_jour($id_synchro) dans la file des travaux",_LOG_ERREUR);
	}

}

?>