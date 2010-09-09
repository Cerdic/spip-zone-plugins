<?php

function zippeur_upgrade($nom_meta_base_version,$version_cible){
	 $current_version = 0.0;
	 if ( (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
                        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
                include_spip('zippeur_pipelines');
                if (version_compare($current_version,"0.1","<")){
                        include_spip('base/create');
                        creer_base();
                        ecrire_meta($nom_meta_base_version,$current_version="0.1");
                }
                ecrire_metas();
        }
	 
		
}

function zippeur_vider_tables($nom_meta_version_base){
	include_spip('base/abstract_sql');
	include_spip('inc/flock');
	defined('_DIR_SITE') ? $fichiers = preg_files(_DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES.'/cache-zip') : $fichiers = preg_files(_DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES.'/cache-zip') ;
	foreach($fichiers as $f){
		supprimer_fichier($f);
	}
	
	sql_drop_table("spip_zippeur");
	effacer_meta($nom_meta_version_base);
}


?>