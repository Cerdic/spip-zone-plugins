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

	$interface['tables_jointures']['spip_jobs'][] = 'jobs_liens';

	return $interface;
}

function queue_declarer_tables_principales($tables_principales){

	$spip_jobs = array(
		"id_job" 	=> "bigint(21) NOT NULL",
		"descriptif"	=> "text DEFAULT '' NOT NULL",
		"fonction" 	=> "varchar(255) NOT NULL", //nom de la fonction
		"args"=> "longblob DEFAULT '' NOT NULL", // arguments
		"md5args"=> "char(32) NOT NULL default ''", // signature des arguments
		"inclure" => "varchar(255) NOT NULL", // fichier a inclure ou path/ pour charger_fonction
		"priorite" 	=> "smallint(6) NOT NULL default 0",
		"date" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // date au plus tot
		"status" => "tinyint NOT NULL default 1",
		);

	$spip_jobs_key = array(
		"PRIMARY KEY" 	=> "id_job",
		"KEY date" => "date",
		"KEY status" => "status",
	);

	$tables_principales['spip_jobs'] = array(
		'field' => &$spip_jobs,
		'key' => &$spip_jobs_key);

	return $tables_principales;
}


function queue_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_jobs_liens = array(
		"id_job"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
	);

$spip_documents_liens_key = array(
		"PRIMARY KEY"		=> "id_job,id_objet,objet",
		"KEY id_job"	=> "id_job");

	$tables_auxiliaires['spip_jobs_liens'] = array(
		'field' => &$spip_jobs_liens,
		'key' => &$spip_documents_liens_key);
	return $tables_auxiliaires;
}

function queue_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/abstract_sql');
		if (version_compare($current_version,"0.2.0",'<')){
			include_spip('base/serial');
			include_spip('base/auxiliaires');
			include_spip('base/create');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,"0.3.0",'<')){
			include_spip('base/serial');
			include_spip('base/auxiliaires');
			include_spip('base/create');
			maj_tables(array('spip_jobs'));
			// mettre a jour les md5args
			$res = sql_select("id_job,args", "spip_jobs", "md5args=''");
			while ($row = sql_fetch($res)){
				sql_updateq('spip_jobs', array('md5args'=>md5($row['args'])),"id_job=".intval($row['id_job']));
			}
			ecrire_meta($nom_meta_base_version,$current_version="0.3.0",'non');
		}
		if (version_compare($current_version,"0.3.1",'<')){
			sql_alter("table spip_jobs change md5args md5args char(32) NOT NULL default ''");
			ecrire_meta($nom_meta_base_version,$current_version="0.3.1",'non');
		}
		if (version_compare($current_version,"0.3.2",'<')){
			sql_alter("table spip_jobs change args args longblob DEFAULT '' NOT NULL");
			sql_alter("table spip_jobs add index status (status)");
			ecrire_meta($nom_meta_base_version,$current_version="0.3.2",'non');
		}
		if (version_compare($current_version,"0.3.3",'<')){
			include_spip('inc/queue'); // define _JQ_PENDING
			// virer d'abord l'index existant
			sql_alter("table spip_jobs drop index status");
			// ne pas refaire un update si la migration a deja eu lieu
			if (!sql_countsel('spip_jobs', 'status=1')){
				// renommer le champ le temps de la migration
				sql_alter("table spip_jobs change status status_old varchar(15) NOT NULL default 'scheduled'");
				// ajouter le nouveau status avec un nom provisoire
				sql_alter("table spip_jobs add status_new tinyint NOT NULL default 1");
				// synchroniser les deux status
				sql_update('spip_jobs',array('status_new'=>intval(_JQ_PENDING)),"status_old='pending'");
			}
			// renommer le status
			sql_alter("table spip_jobs change status_new status tinyint NOT NULL default 1");
			// placer un index dessus
			sql_alter("table spip_jobs add index status (status)");
			// virer le vieux status
			sql_alter("table spip_jobs drop status_old");
			ecrire_meta($nom_meta_base_version,$current_version="0.3.3",'non');
		}
		if (version_compare($current_version,"0.3.4",'<')){
			// on n'utilise plus les meta, la queue se reinitialisera automatiquement
			effacer_meta('queue_next_job_time');
			ecrire_meta($nom_meta_base_version,$current_version="0.3.4",'non');
		}

	}
	// replanifier les taches cron quand on passe ici
	include_spip('inc/genie');
	genie_queue_watch_dist();
}

function queue_vider_tables($nom_meta_base_version) {
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