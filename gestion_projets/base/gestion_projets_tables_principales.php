<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//
// Formulaires : Structure
//

function gestion_projets_declarer_tables_principales($tables_principales){
	$spip_projets = array(
		"id_projet" 		=> "int(21) NOT NULL",
		"id_parent" 		=> "int(21) NOT NULL",	
		"id_chef_projet" 	=> "int(21) NOT NULL",	
		"participants"		=> "text NOT NULL",			
		"nom" 				=> "varchar(255) NOT NULL",
		"descriptif"		=> "text NOT NULL",		
		"statut" 			=> "varchar(20) NOT NULL",
		"duree"				=> "int(21) NOT NULL",
		"montant_heure"		=> "decimal(65,2)",			
		"montant_estime"	=> "decimal(65,2)",	
		"montant_reel"		=> "decimal(65,2)",			
		"duree_estimee"	=> "decimal(65,2)",
		"duree_reelle"	=> "decimal(65,2)",	
		"avancement_projet"	=> "int(21) NOT NULL",
		"date_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin_estimee"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin_reel"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",				
		"date_creation" 	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",		
		"maj" 				=> "TIMESTAMP");
	
	$spip_projets_key = array(
		"PRIMARY KEY" 	=> "id_projet",
		);
		
	$spip_projets_join = array(
		"id_projet"	=> "id_projet",	
		);

	$tables_principales['spip_projets'] = array(
		'field' => &$spip_projets,
		'key' => &$spip_projets_key,
		'join' => &$spip_projets_join
	);
	
	$spip_projets_taches = array(
		"id_tache" 		=> "int(21) NOT NULL",
		"id_parent" 		=> "int(21) NOT NULL",	
		"id_projet" 		=> "int(21) NOT NULL",	
		"participants"		=> "text NOT NULL",			
		"nom" 				=> "varchar(255) NOT NULL",
		"descriptif"		=> "text NOT NULL",		
		"statut" 			=> "varchar(20) NOT NULL",
		"duree"				=> "int(21) NOT NULL",
		"montant_heure"		=> "decimal(65,2)",				
		"montant_estime"	=> "decimal(65,2)",	
		"montant_reel"		=> "decimal(65,2)",			
		"duree_estimee"	=> "decimal(65,2)",
		"duree_reelle"	=> "decimal(65,2)",	
		"avancement_tache"	=> "int(21) NOT NULL",
		"date_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin_estimee"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin_reel"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"active" 			=> "bool NOT NULL",				
		"date_creation" 	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",		
		"maj" 				=> "TIMESTAMP");
	
	$spip_projets_taches_key = array(
		"PRIMARY KEY" 	=> "id_tache",
		"KEY id_parent"	=> "id_projet",	
		);
		
	$spip_projets_taches_join = array(
		"id_tache"	=> "id_tache",		
		"id_projet"	=> "id_projet",	
		);

	$tables_principales['spip_projets_taches'] = array(
		'field' => &$spip_projets_taches,
		'key' => &$spip_projets_taches_key,
		'join' => &$spip_projets_taches_join
	);
	$spip_projets_timetracker = array(
		"id_session"		=> "bigint(21) NOT NULL",
		"id_auteur"		=> "bigint(21) NOT NULL",			
		"id_projet"			=> "bigint(21) NOT NULL",		
		"date_debut"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"duree"				=> "decimal(65,2)",				
		);

	$spip_projets_timetracker_key = array(
		"PRIMARY KEY"		=> "id_session",
		"KEY id_projet" 	=> "id_projet",
		"KEY id_auteur"	 	=> "id_auteur",		 
		);
		
	$spip_projets_timetracker_join = array(
		"id_session"		=> "id_session",
		"id_projet"			=> "id_projet",	
		"id_auteur"			=> "id_auteur",			
		);		
		
	$tables_principales['spip_projets_timetracker'] = array(
		'field' => &$spip_projets_timetracker,
		'join' => &$spip_projets_timetracker_join,		
		'key' => &$spip_projets_timetracker_key,
	);		
	return $tables_principales;
	
	}
?>
