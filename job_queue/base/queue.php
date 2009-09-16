<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function queue_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['jobs']='jobs';


	return $interface;
}

function queue_declarer_tables_principales($tables_principales){

	$spip_jobs = array(
		"id_job" 	=> "bigint(21) NOT NULL",
		"descriptif"	=> "text DEFAULT '' NOT NULL",
		"fonction" 	=> "varchar(255) NOT NULL", //nom de la fonction
		"args"=> "text DEFAULT '' NOT NULL", // arguments
		"inclure" => "varchar(255) NOT NULL", // fichier a inclure ou path/ pour charger_fonction
		"priorite" 	=> "smallint(6) NOT NULL default 0",
		"date" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // date au plus tot
		);

	$spip_jobs_key = array(
		"PRIMARY KEY" 	=> "id_job",
		"KEY date" => "date",
	);

	$tables_principales['spip_jobs'] = array(
		'field' => &$spip_jobs,
		'key' => &$spip_jobs_key);

	return $tables_principales;
}


function queue_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/abstract_sql');
		if ($current_version==0.0){
			include_spip('base/serial');
			include_spip('base/create');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}

	}
	// replanifier les taches cron quand on passe ici
	include_spip('inc/genie');
	genie_queue_watch_dist();
}

function queue_vider_tables($nom_meta_base_version) {
	effacer_meta('queue_next_job_time');
	effacer_meta($nom_meta_base_version);
	sql_drop_table("spip_jobs");
}


function queue_install($action,$prefix,$version_cible){
	$version_base = $GLOBALS[$prefix."_base_version"];
	switch ($action){
		case 'test':
			$ok = (isset($GLOBALS['meta'][$prefix."_base_version"])
				AND version_compare($GLOBALS['meta'][$prefix."_base_version"],$version_cible,">="));
			if ($ok){
				// replanifier les taches cron quand on passe ici
				include_spip('inc/genie');
				genie_queue_watch_dist();
			}
			return $ok;
			break;
		case 'install':
			queue_upgrade($prefix."_base_version",$version_cible);
			break;
		case 'uninstall':
			queue_vider_tables($prefix."_base_version");
			break;
	}
}

?>