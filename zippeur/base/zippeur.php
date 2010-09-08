<?php

function zippeur_upgrade($nom_meta_base_version,$version_cible){
	 $current_version = 0.0;
	 if ( (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
                        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
                include_spip('zippeur_pipelines');
                if (version_compare($current_version,"0.1","<")){
                        include_spip('base/create');
                        creer_base();
                        sous_repertoire(_DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES,'cache-zip');
                        ecrire_meta($nom_meta_base_version,$current_version="0.1");
                }
                ecrire_metas();
        }
	 
		
}


?>