<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */


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
	return queue_remove_job($id_job);
}

?>