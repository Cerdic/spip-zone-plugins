<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

function action_forcer_job_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_job = $securiser_action();

	if ($id_job = intval($id_job)
		AND autoriser('forcer','job',$id_job)
	){
		include_spip('inc/queue');
		include_spip('inc/genie');
		queue_schedule(array($id_job));
	}

}

?>