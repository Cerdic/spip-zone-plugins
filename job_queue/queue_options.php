<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

#define('_JQ_MAX_JOBS_EXECUTE',200); // pour personaliser le nombre de jobs traitables a chaque hit
#define('_JQ_MAX_JOBS_TIME_TO_EXECUTE',15); // pour personaliser le temps d'excution dispo a chaque hit

@define('_JQ_NB_JOBS_OVERFLOW',10000); // nombre de jobs a partir duquel on force le traitement en fin de hit pour purger

// on inhibe la balise #SPIP_CRON qui ne servira plus a rien
function balise_SPIP_CRON ($p) { $p->code = '\'\''; $p->interdire_scripts = false; 
return $p; }


/**
 * Add a job to the queue. The function added will be called in the order it
 * was added during cron.
 *
 * @param $function
 *   The function name to call.
 * @param $description
 *   A human-readable description of the queued job.
 * @param $arguments
 *   Optional array of arguments to pass to the function.
 * @param $file
 *   Optional file path which needs to be included for $fucntion.
 * @param $no_duplicate
 *   If TRUE, do not add the job to the queue if one with the same function and
 *   arguments already exists.
 * @param $time
 *		time for starting the job. If 0, job will start as soon as possible
 * @param $priority
 *		-10 (low priority) to +10 (high priority), 0 is the default
 * @return int
 *	id of job
 */
function job_queue_add($function, $description, $arguments = array(), $file = '', $no_duplicate = FALSE, $time=0, $priority=0) {
	include_spip('inc/queue');
	return queue_add_job($function, $description, $arguments, $file, $no_duplicate, $time, $priority);
}

/**
 * Remove a job from the queue.
 * @param int $id_job
 *  id of jonb to delete
 * @return bool
 */
function job_queue_remove($id_job){
	include_spip('inc/queue');
	return queue_remove_job($id_job);
}

/**
 * Link a job with SPIP objects
 *
 *
 * @param int $id_job
 *	id of job to link
 * @param array $objets
 *  can be a simple array('objet'=>'article','id_objet'=>23)
 *  or an array of simple array to link multiples objet in one time
 */
function job_queue_link($id_job,$objets){
	include_spip('inc/queue');
	return queue_link_job($id_job,$objets);
}

?>
