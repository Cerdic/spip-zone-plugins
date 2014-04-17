<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function videos_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if ($$current_version == '0.0'){
			include_spip('inc/config');
			ecrire_config('videos/responsive', 'oui');
		}

		if (version_compare($current_version,'0.1','<')){
			include_spip('base/abstract_sql');

			/* Mettre à jour la table DOCUMENTS pour regrouper les extensions "distantes" (changement de nomenclature) */
			sql_updateq('spip_documents',array('extension'=>'dist_daily'),"extension='dailym'");
			sql_updateq('spip_documents',array('extension'=>'dist_vimeo'),"extension='vimeo'");
			sql_updateq('spip_documents',array('extension'=>'dist_youtu'),"extension='youtube'");

			/* Ajouter les nouvelles extensions en base pour qu'elles soient prises en compte comme des vidéos */
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_daily', 'titre'=>'Dailymotion','inclus'=>'embed', 'upload'=>'oui' ) );
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_vimeo', 'titre'=>'Vimeo','inclus'=>'embed', 'upload'=>'oui' ) );
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_youtu', 'titre'=>'Youtube','inclus'=>'embed', 'upload'=>'oui' ) );

			ecrire_meta($nom_meta_base_version,$current_version="0.1",'non');
		}
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/abstract_sql');

			sql_updateq('spip_documents',array('extension'=>'dist_cubox'),"extension='culturebox'");
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_cubox', 'titre'=>'CultureBox','inclus'=>'embed', 'upload'=>'oui' ) );

			ecrire_meta($nom_meta_base_version,$current_version="0.2",'non');
		}
		if (version_compare($current_version,'0.3','<')){
			/* 0.1 et 0.2 rataient leurs insertions SI le plugin Mediatheque n'avait pas inséré le champ media, on relance donc l'insertion pour ceux qui ont raté le coche */
			include_spip('base/abstract_sql');
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_daily', 'titre'=>'Dailymotion','inclus'=>'embed', 'upload'=>'oui' ) );
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_vimeo', 'titre'=>'Vimeo','inclus'=>'embed', 'upload'=>'oui' ) );
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_youtu', 'titre'=>'Youtube','inclus'=>'embed', 'upload'=>'oui' ) );
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_cubox', 'titre'=>'CultureBox','inclus'=>'embed', 'upload'=>'oui' ) );

			echo "Mise à jour du plugin Vidéo(s) en version base $version_cible<br/>";
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}

	// Si présence du champ MEDIA : on MAJ
	$trouver_table=charger_fonction('trouver_table','base');	
	$desc = $trouver_table('spip_types_documents');
	if(array_key_exists('media',$desc['field'])) sql_updateq('spip_types_documents',array('media'=>'video'),"extension REGEXP '^dist_'");
}


function videos_install($action,$prefix,$version_cible){
	$version_base = $GLOBALS[$prefix."_base_version"];
	switch ($action){
		case 'test':
			// Des trucs
			return (isset($GLOBALS['meta'][$prefix."_base_version"])
				AND version_compare($GLOBALS['meta'][$prefix."_base_version"],$version_cible,">="));
			break;
		case 'install':
			videos_upgrade('videos_base_version',$version_cible);
			break;
		case 'uninstall':
			videos_vider_tables('videos_base_version');
			videos_vider_tables('videos');
			break;
	}
}

function videos_vider_tables($nom_meta_base_version) {
	sql_delete("spip_types_documents", "extension='dist_daily'");
	sql_delete("spip_types_documents", "extension='dist_vimeo'");
	sql_delete("spip_types_documents", "extension='dist_youtu'");
	sql_delete("spip_types_documents", "extension='dist_cubox'");

	effacer_meta($nom_meta_base_version);
}
?>