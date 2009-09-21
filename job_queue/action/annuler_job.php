<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

function action_annuler_job_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_job = $securiser_action();

	if ($id_job = intval($id_job)
		AND autoriser('annuler','job',$id_job)
	){
		job_queue_remove($id_job);
	}

}

?>