<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

@define('_JQ_SCHEDULED',1);
@define('_JQ_PENDING',0);

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
	if(defined(_JOB_QUEUE_SYNCHRONOUS)) {
		execute_job('Now', $file, $function, $arguments, $description);
		return 0;
	} else {
	include_spip('base/abstract_sql');

	// cas pourri de ecrire/action/editer_site avec l'option reload=oui
	if (defined('_GENIE_SYNDIC_NOW'))
		$arguments['id_syndic'] = _GENIE_SYNDIC_NOW;

	// serialiser les arguments
	$arguments = serialize($arguments);
	$md5args = md5($arguments);

	// si option ne pas dupliquer, regarder si la fonction existe deja
	// avec les memes args et file
	if (
			$no_duplicate
		AND
			sql_countsel('spip_jobs',
				'status='.intval(_JQ_SCHEDULED).' AND fonction='.sql_quote($function)
				.(($no_duplicate==='function_only')?'':
				 ' AND md5args='.sql_quote($md5args).' AND inclure='.sql_quote($file)))
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
			'md5args'=>$md5args,
			'inclure'=>$file,
			'priorite'=>max(-10,min(10,intval($priority))),
			'date'=>$date,
			'status'=>_JQ_SCHEDULED,
		));

	// une option de debug pour verifier que les arguments en base sont bons
	// ie cas d'un char non acceptables sur certains type de champs
	// qui coupe la valeur
	if (defined('_JQ_INSERT_CHECK_ARGS') AND $id_job) {
		$args = sql_getfetsel('args', 'spip_jobs', 'id_job='.intval($id_job));
		if ($args!==$arguments) {
			spip_log('arguments job errones / longueur '.strlen($args)." vs ".strlen($arguments).' / valeur : '.var_export($arguments,true),'queue');
		}
	}

	if ($id_job){
		queue_update_next_job_time($time);
	}

	return $id_job;
	}
}

/**
 * Purge the whole queue
 * and replan cron jobs
 * 
 * @return void
 */
function queue_purger(){
	include_spip('base/abstract_sql');
	sql_delete('spip_jobs');
	sql_delete("spip_jobs_liens","id_job NOT IN (".sql_get_select("id_job","spip_jobs").")");
	include_spip('inc/genie');
	genie_queue_watch_dist();
}

/**
 * Remove a job from the queue.
 * @param int $id_job
 *  id of jonb to delete
 * @return bool
 */
function queue_remove_job($id_job){
	include_spip('base/abstract_sql');

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
	include_spip('base/abstract_sql');

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
 * @param $row array
 *	describe the job, with field of table spip_jobs
 * @return mixed
 *	return the result of job
 */
function queue_start_job($row){

	// deserialiser les arguments
	$args = unserialize($row['args']);
	if ($args===false){
		spip_log('arguments job errones '.var_export($row,true),'queue');
		$args = array();
	}

	$fonction = $row['fonction'];
	$id_job = $row['id_job'];
	$inclure = $row['inclure'];
	$descriptif = $row['descriptif'];
	return execute_job($id_job, $inclure, $fonction, $args, $descriptif);
}

/**
 * Execute a job from all the information
 * @param $id_job string
 *  the job id
 * @param $inclure string
 *  a file to be included to be able to invoke the job
 * @param $fonction string
 *  the name of the function containing the job code
 * @param $args array
 *  the paramaters array to be passed to the function
 * @param $descriptif string
 *  a description of the job
 * @return void
 */
function execute_job($id_job, $inclure, $fonction, $args, $descriptif){
	if (strlen($inclure = trim($inclure))){
		if (substr($inclure,-1)=='/'){ // c'est un chemin pour charger_fonction
			$f = charger_fonction($fonction,rtrim($inclure,'/'),false);
			if ($f)
				$fonction = $f;
		}
		else
			include_spip($inclure);
	}

	$formatted_args = var_export($args, true);

	if (!function_exists($fonction)){
		spip_log("fonction $fonction ($inclure) inexistante $formatted_args", 'queue');
		return false;
	}

	spip_log("queue [$id_job]: $fonction() [$descriptif] [$formatted_args] start", 'queue');
	switch (count($args)) {
		case 0:	$res = $fonction(); break;
		case 1:	$res = $fonction($args[0]); break;
		case 2:	$res = $fonction($args[0],$args[1]); break;
		case 3:	$res = $fonction($args[0],$args[1], $args[2]); break;
		case 4:	$res = $fonction($args[0],$args[1], $args[2], $args[3]); break;
		case 5:	$res = $fonction($args[0],$args[1], $args[2], $args[3], $args[4]); break;
		case 6:	$res = $fonction($args[0],$args[1], $args[2], $args[3], $args[4], $args[5]); break;
		case 7:	$res = $fonction($args[0],$args[1], $args[2], $args[3], $args[4], $args[5], $args[6]); break;
		case 8:	$res = $fonction($args[0],$args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]); break;
		case 9:	$res = $fonction($args[0],$args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]); break;
		case 10:$res = $fonction($args[0],$args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]); break;
		default:
			# plus lent mais completement generique
			$res = call_user_func_array($fonction, $args);
	}
	spip_log("queue [$id_job]: $fonction() [$descriptif] end", 'queue');
	return $res;
}

/**
 * Scheduler :
 * takes each waiting job and launch it,
 * according to a limit total time and number of jobs
 *
 * time of next job to execute is updated afrer each job done
 * in order to relaunch scheduler only when needed.
 *
 * @param array $force_jobs
 *   list of id_job to execute when provided
 */
function queue_schedule($force_jobs = null){
	$time = time();
	if (defined('_DEBUG_BLOCK_QUEUE'))
		return;

	// rien a faire si le prochain job est encore dans le futur
	if (queue_sleep_time_to_next_job() AND (!$force_jobs OR !count($force_jobs)))
		return;

	include_spip('base/abstract_sql');

	if (!defined('_JQ_MAX_JOBS_TIME_TO_EXECUTE')){
		$max_time = ini_get('max_execution_time')/2;
		// valeur conservatrice si on a pas reussi a lire le max_execution_time
		if (!$max_time) $max_time=5;
		define('_JQ_MAX_JOBS_TIME_TO_EXECUTE',min($max_time,15)); // une valeur maxi en temps.
	}
	$end_time = $time + _JQ_MAX_JOBS_TIME_TO_EXECUTE;

	#spip_log("JQ schedule $time / $end_time",'jq');

	if (!defined('_JQ_MAX_JOBS_EXECUTE'))
		define('_JQ_MAX_JOBS_EXECUTE',200);
	$nbj=0;
	// attraper les jobs
	// dont la date est passee (echus en attente),
	// par odre :
	//	- de priorite
	//	- de date
	// lorsqu'un job cron n'a pas fini, sa priorite est descendue
	// pour qu'il ne bloque pas les autres jobs en attente
	if (is_array($force_jobs) AND count($force_jobs))
		$cond = "status=".intval(_JQ_SCHEDULED)." AND ".sql_in("id_job", $force_jobs);
	else {
		$now = date('Y-m-d H:i:s',$time);
		$cond = "status=".intval(_JQ_SCHEDULED)." AND date<".sql_quote($now);
	}

	register_shutdown_function('queue_error_handler'); // recuperer les erreurs auant que possible
	$res = sql_allfetsel('*','spip_jobs',$cond,'','priorite DESC,date','0,'.(_JQ_MAX_JOBS_EXECUTE+1));
	do {
		if ($row = array_shift($res)){
			$nbj++;
			// il faut un verrou, a base de sql_delete
			if (sql_delete('spip_jobs',"id_job=".intval($row['id_job'])." AND status=".intval(_JQ_SCHEDULED))){
				#spip_log("JQ schedule job ".$nbj." OK",'jq');
				// on reinsert dans la base aussitot avec un status=_JQ_PENDING
				$row['status'] = _JQ_PENDING;
				$row['date'] = $time;
				sql_insertq('spip_jobs', $row);

				// on a la main sur le job :
				// l'executer
				$result = queue_start_job($row);

				$time = time();
				queue_close_job($row, $time, $result);
			}
		}
		#spip_log("JQ schedule job end time ".$time,'jq');
	} while ($nbj<_JQ_MAX_JOBS_EXECUTE AND $row AND $time<$end_time);
	#spip_log("JQ schedule end time ".time(),'jq');

	if ($row = array_shift($res)){
		queue_update_next_job_time(0); // on sait qu'il y a encore des jobs a lancer ASAP
		#spip_log("JQ encore !",'jq');
	}
	else
		queue_update_next_job_time();

}

/**
 * Terminer un job au status _JQ_PENDING :
 *  - le reprogrammer si c'est un cron
 *  - supprimer ses liens
 *  - le detruire en dernier
 *
 * @param array $row
 * @param int $time
 * @param int $result
 */
function queue_close_job(&$row,$time,$result=0){
	// est-ce une tache cron qu'il faut relancer ?
	if ($periode = queue_is_cron_job($row['fonction'],$row['inclure'])){
		// relancer avec les nouveaux arguments de temps
		include_spip('inc/genie');
		if ($result<0)
			// relancer tout de suite, mais en baissant la priorite
			queue_genie_replan_job($row['fonction'],$periode,0-$result/*last*/,0/*ASAP*/,$row['priorite']-1);
		else
			// relancer avec la periode prevue
			queue_genie_replan_job($row['fonction'],$periode,$time);
	}
	// purger ses liens eventuels avec des objets
	sql_delete("spip_jobs_liens","id_job=".intval($row['id_job']));
	// supprimer le job fini
	sql_delete('spip_jobs','id_job='.intval($row['id_job']));
}

/**
 * Recuperer des erreurs auant que possible
 * en terminant la gestion de la queue
 */
function queue_error_handler(){
	// se remettre dans le bon dossier, car Apache le change parfois (toujours?)
	chdir(_ROOT_CWD);

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
	static $taches = null;
	if (strncmp($inclure,'genie/',6)==0){
		if (is_null($taches)){
			include_spip('inc/genie');
			$taches = taches_generales();
		}
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
	static $nb_jobs_scheduled = null;
	static $deja_la = false;
	// prendre le min des $next_time que l'on voit passer ici, en cas de reentrance
	static $next = null;
	// queue_close_job peut etre reentrant ici
	if ($deja_la) return;
	$deja_la = true;

	include_spip('base/abstract_sql');
	$time = time();

	// traiter les jobs morts au combat (_JQ_PENDING depuis plus de 180s)
	// pour cause de timeout ou autre erreur fatale
	$res = sql_allfetsel("*","spip_jobs","status=".intval(_JQ_PENDING)." AND date<".sql_quote(date('Y-m-d H:i:s',$time-180)));
	if (is_array($res)) {
		foreach ($res as $row)
			queue_close_job($row,$time);
	}

	// chercher la date du prochain job si pas connu
	if (is_null($next) OR is_null(queue_sleep_time_to_next_job())){
		$date = sql_getfetsel('date','spip_jobs',"status=".intval(_JQ_SCHEDULED),'','date','0,1');
		$next = strtotime($date);
	}
	if (!is_null($next_time)){
		if (is_null($next) OR $next>$next_time)
			$next = $next_time;
	}

		if ($next){
			if (is_null($nb_jobs_scheduled))
				$nb_jobs_scheduled = sql_countsel('spip_jobs',"status=".intval(_JQ_SCHEDULED)." AND date<".sql_quote(date('Y-m-d H:i:s',$time)));
			elseif ($next<=$time)
				$nb_jobs_scheduled++;
			// si trop de jobs en attente, on force la purge en fin de hit
			// pour assurer le coup
			if ($nb_jobs_scheduled>_JQ_NB_JOBS_OVERFLOW)
				define('_DIRECT_CRON_FORCE',true);
		}

	queue_set_next_job_time($next);
	$deja_la = false;
}


/**
 * Mettre a jour la date de prochain job
 * @param <type> $next
 */
function queue_set_next_job_time($next) {

	// utiliser le temps courant reel plutot que temps de la requete ici
	$time = time();

	// toujours relire la valeur pour comparer, pour tenir compte des maj concourrantes
	// et ne mettre a jour que si il y a un interet a le faire
	// permet ausis d'initialiser le nom de fichier a coup sur
	$curr_next = $_SERVER['REQUEST_TIME'] + queue_sleep_time_to_next_job(true);
	if (
			($curr_next<=$time AND $next>$time) // le prochain job est dans le futur mais pas la date planifiee actuelle
			OR $curr_next>$next // le prochain job est plus tot que la date planifiee actuelle
		) {
		if (include_spip('inc/memoization') AND defined('_MEMOIZE_MEMORY') AND _MEMOIZE_MEMORY) {
			cache_set(_JQ_NEXT_JOB_TIME_FILENAME,intval($next));
		}
		else {
			ecrire_fichier(_JQ_NEXT_JOB_TIME_FILENAME,intval($next));
		}
		queue_sleep_time_to_next_job($next);
	}

	return queue_sleep_time_to_next_job();
}
?>
