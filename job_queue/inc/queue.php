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
 *	 If 'function_only' test of existence is only on function name (for cron job)
 * @param $time
 *		time for starting the job. If 0, job will start as soon as possible
 * @param $priority
 *		-10 (low priority) to +10 (high priority), 0 is the default
 * @return int
 *	id of job
 */
function queue_add_job($function, $description, $arguments = array(), $file = '', $no_duplicate = false, $time=0, $priority=0){

	// serialiser les arguments
	$arguments = serialize($arguments);

	// si option ne pas dupliquer, regarder si la fonction existe deja
	// avec les memes args et file
	if (
			$no_duplicate
		AND
			sql_countsel('spip_jobs',
				'fonction='.sql_quote($function)
				.(($no_duplicate==='function_only')?'':
				 ' AND args='.sql_quote($arguments).' AND file='.sql_quote($file)))
		)
		return false;

	// si pas de date programee, des que possible
	if (!$time)
		$time = time();
	$date = date('Y-m-d H:i:s',$time);

	$id_job = sql_insertq('spip_jobs',array(
			'fonction'=>$function,
			'descriptif'=>$description,
			'args'=>$arguments,
			'inclure'=>$file,
			'priorite'=>max(-10,min(10,intval($priority))),
			'date'=>$date
		));

	if ($id_job){
		queue_update_next_job_time($time);
	}

	return $id_job;

}


/**
 * Remove a job from the queue.
 * @param int $id_job
 *  id of jonb to delete
 * @return bool
 */
function queue_remove_job($id_job){
	if ($row = sql_fetsel('fonction,inclure,date','spip_jobs','id_job='.intval($id_job))
	 AND $res = sql_delete('spip_jobs','id_job='.intval($id_job))){
		queue_unlink_job($id_job);
		// est-ce une tache cron qu'il faut relancer ?
		if ($periode = queue_is_cron_job($row['fonction'],$row['inclure'])){
			// relancer avec les nouveaux arguments de temps
			include_spip('inc/genie');
			// relancer avec la periode prevue
			queue_genie_replan_job($row['fonction'],$periode,strtotime($row['date']));
		}
		queue_update_next_job_time();
	}
	return $res;
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
function queue_link_job($id_job,$objets){
	if (is_array($objets) AND count($objets)){
		if (is_array(reset($objets))){
			foreach($objets as $k=>$o){
				$objets[$k]['id_job'] = $id_job;
			}
			sql_insertq_multi('spip_jobs_liens',$objets);
		}
		else
			sql_insertq('spip_jobs_liens',array_merge(array('id_job'=>$id_job),$objets));
	}
}

/**
 * Unlink job with SPIP objects
 *
 * @param int $id_job
 *	id of job to unlink ibject with
 * @return int/bool
 *	result of sql_delete
 */
function queue_unlink_job($id_job){
	return sql_delete("spip_jobs_liens","id_job=".intval($id_job));
}

/**
 * Start a job described by array $row
 * @param array $row
 *	describe the job, with field of table spip_jobs
 * @return mixed
 *	return the result of job
 */
function queue_start_job($row){

// deserialiser les arguments
	$arguments = unserialize($row['args']);
	if ($arguments===false){
		spip_log('arguments job errones '.var_export($row,true),'queue');
		$arguments = array();
	}

	$fonction = $row['fonction'];
	if (strlen($inclure = trim($row['inclure']))){
		if (substr($inclure,-1)=='/'){ // c'est un chemin pour charger_fonction
			$f = charger_fonction($fonction,rtrim($inclure,'/'),false);
			if ($f)
				$fonction = $f;
		}
		else
			include_spip($inclure);
	}

	if (!function_exists($fonction)){
		spip_log("fonction $fonction ($inclure) inexistante ".var_export($row,true),'queue');
		return false;
	}

	return call_user_func_array($fonction, $arguments);

}

/**
 * Ordonanceur
 * Evite les requetes sql a chaque appel
 * en memorisant en meta la date du prochain job
 */
function queue_schedule(){
	$start = time();

	// rien a faire si le prochain job est encore dans le futur
	if ($GLOBALS['meta']['queue_next_job_time']>$start)
		return;

	$max_time = ini_get('max_execution_time')/2;
	// valeur conservatrice si on a pas reussi a lire le max_execution_time
	if (!$max_time) $max_time=5;
	$max_time = min($max_time,15); // une valeur maxi en temps.

	// attraper les jobs
	// dont la date est passee (echus en attente),
	// par odre :
	//	- de priorite
	//	- de date
	// lorsqu'un job cron n'a pas fini, sa priorite est descendue
	// pour qu'il ne bloque pas les autres jobs en attente
	$now = date('Y-m-d H:i:s',$start);
	$res = sql_select('*','spip_jobs','date<'.sql_quote($now),'','priorite DESC,date','0,10');
	do {
		if ($row = sql_fetch($res)){
			// il faut un verrou, a base de sql_delete ?
			if (sql_delete('spip_jobs','id_job='.intval($row['id_job']))){
				// on a la main sur le job :
				// l'executer
				$result = queue_start_job($row);

				// purger ses liens eventuels avec des objets
				sql_delete("spip_jobs_liens","id_job=".intval($row['id_job']));

				// est-ce une tache cron qu'il faut relancer ?
				if ($periode = queue_is_cron_job($row['fonction'],$row['inclure'])){
					// relancer avec les nouveaux arguments de temps
					include_spip('inc/genie');
					if ($result<0)
						// relancer tout de suite, mais en baissant la priorite
						queue_genie_replan_job($row['fonction'],$periode,0-$result/*last*/,0/*ASAP*/,$row['priorite']-1);
					else
						// relancer avec la periode prevue
						queue_genie_replan_job($row['fonction'],$periode,time());
				}
			}
		}
	} while ($row AND time()<$start+$max_time);
	
	if ($row = sql_fetch($res))
		queue_update_next_job_time(0); // on sait qu'il y a encore des jobs a lancer ASAP
	else
		queue_update_next_job_time();

}

/**
 * Test if a job in queue is periodic cron
 *
 * @param <type> $function
 * @param <type> $inclure
 * @return <type>
 */
function queue_is_cron_job($function,$inclure){
	if ($inclure=='genie/'){
		include_spip('inc/genie');
		$taches = taches_generales();
		if (isset($taches[$function]))
			return $taches[$function];
	}
	return false;
}

/**
 * Mettre a jour la date du prochain job a lancer
 * Si une date est fournie (au format time unix)
 * on fait simplement un min entre la date deja connue et celle fournie
 * (cas de l'ajout simple
 * ou cas $next_time=0 car l'on sait qu'il faut revenir ASAP)
 *
 * @param int $next_time
 *	temps de la tache ajoutee ou 0 pour ASAP
 */
function queue_update_next_job_time($next_time=null){
	if (is_null($next_time) OR !isset($GLOBALS['meta']['queue_next_job_time'])){
		$date = sql_getfetsel('date','spip_jobs','','','date','0,1');
		$next_time = strtotime($date);
	}
	else {
		$next_time = min($GLOBALS['meta']['queue_next_job_time'],$next_time);
	}
	include_spip('inc/meta');
	ecrire_meta('queue_next_job_time',$next_time);
}
?>