<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

#define('_JQ_MAX_JOBS_EXECUTE',200); // pour personaliser le nombre de jobs traitables a chaque hit
#define('_JQ_MAX_JOBS_TIME_TO_EXECUTE',15); // pour personaliser le temps d'excution dispo a chaque hit
#define('_JOB_QUEUE_SYNCHRONOUS', true); // pour rendre l'execution des jobs synchrones (les jobs sont executés immediatement au lieu de passer par la queue)

@define('_CRON_DELAI_GOURMAND',0);
@define('_CRON_DELAI',0);
@define('_JQ_NB_JOBS_OVERFLOW',10000); // nombre de jobs a partir duquel on force le traitement en fin de hit pour purger

// on inhibe la balise #SPIP_CRON qui ne servira plus a rien
function balise_SPIP_CRON ($p) { $p->code = '\'\''; $p->interdire_scripts = false; 
return $p; }


function queue_afficher_cron(){
	include_spip('queue_pipelines');
	return queue_affichage_cron();
}

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


/**
 * Renvoyer le temps de repos restant jusqu'au prochain job
 * 0 si un job est a traiter
 * null si la queue n'est pas encore initialise
 * $force est utilisee par queue_set_next_job_time() pour maj la valeur
 *  - si true, force la relecture depuis le fichier
 *  - si int, affecte la static directement avec la valeur
 *
 * @staticvar int $queue_next_job_time
 * @param int/bool $force_next
 * @return int
 */
function queue_sleep_time_to_next_job($force=null) {
	static $queue_next_job_time = -1;
	if ($force===true)
		$queue_next_job_time = -1;
	elseif ($force)
		$queue_next_job_time = $force;

	if ($queue_next_job_time==-1) {
		define('_JQ_NEXT_JOB_TIME_FILENAME',_DIR_TMP . "job_queue_next.txt");
		// utiliser un cache memoire si dispo
		if (include_spip('inc/memoization') AND defined('_MEMOIZE_MEMORY') AND _MEMOIZE_MEMORY) {
			$queue_next_job_time = cache_get(_JQ_NEXT_JOB_TIME_FILENAME);
		}
		else {
			$queue_next_job_time = null;
			if (lire_fichier(_JQ_NEXT_JOB_TIME_FILENAME, $contenu))
				$queue_next_job_time = intval($contenu);
		}
	}

	if (is_null($queue_next_job_time))
		return null;
	if (!$_SERVER['REQUEST_TIME'])
		$_SERVER['REQUEST_TIME'] = time();
	return max(0,$queue_next_job_time-$_SERVER['REQUEST_TIME']);
}

?>