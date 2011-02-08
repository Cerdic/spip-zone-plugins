<?php

function videos_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/abstract_sql');
			
			/* Mettre à jour la table DOCUMENTS pour regrouper les extensions "distantes" (changement de nomenclature) */
			sql_updateq('spip_documents',array('extension'=>'dist_daily'),"extension='dailym'");
			sql_updateq('spip_documents',array('extension'=>'dist_vimeo'),"extension='vimeo'");
			sql_updateq('spip_documents',array('extension'=>'dist_youtu'),"extension='youtube'");
			sql_updateq('spip_documents',array('extension'=>'dist_cubox'),"extension='culturebox'");
			
			/* Ajouter les nouvelles extensions en base pour qu'elles soient prises en compte comme des vidéos */
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_daily', 'titre'=>'Dailymotion','inclus'=>'embed', 'upload'=>'oui', 'media'=>'video' ) );
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_vimeo', 'titre'=>'Vimeo','inclus'=>'embed', 'upload'=>'oui', 'media'=>'video' ) );
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_youtu', 'titre'=>'Youtube','inclus'=>'embed', 'upload'=>'oui', 'media'=>'video' ) );
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_cubox', 'titre'=>'CultureBox','inclus'=>'embed', 'upload'=>'oui', 'media'=>'video' ) );
			
			ecrire_meta($nom_meta_base_version,$current_version="0.1",'non');
		}
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/abstract_sql');
			
			sql_updateq('spip_documents',array('extension'=>'dist_cubox'),"extension='culturebox'");
			sql_insertq('spip_types_documents', array( 'extension'=>'dist_cubox', 'titre'=>'CultureBox','inclus'=>'embed', 'upload'=>'oui', 'media'=>'video' ) );
			
			ecrire_meta($nom_meta_base_version,$current_version="0.2",'non');
		}
	}
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
			// Ce qu'on voudra faire
			break;
	}
}
