<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function zippeur_upgrade($nom_meta_base_version,$version_cible){
	 $current_version = 0.0;
	 if ( (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
                        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
                include_spip('base/create');
                if (version_compare($current_version,"0.1","<")){
                        creer_base();
                        ecrire_meta($nom_meta_base_version,$current_version="0.1");
                }
                if (version_compare($current_version,"0.2","<")){
                        maj_tables('spip_zippeur');
                        ecrire_meta($nom_meta_base_version,$current_version="0.2");
                }
                if (version_compare($current_version,"0.3","<")){
               		ecrire_config('zippeur/zippeur_cmd', 'PclZip');
                	ecrire_meta($nom_meta_base_version,$current_version="0.3");
                }
                if (version_compare($current_version,"0.4","<")){
               		maj_tables('spip_zippeur');
                	ecrire_meta($nom_meta_base_version,$current_version="0.4");
                }
                if (version_compare($current_version,"0.5","<")){
               		maj_tables('spip_zippeur');
                	ecrire_meta($nom_meta_base_version,$current_version="0.5");
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