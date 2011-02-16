<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function gestion_projets_upgrade($nom_meta_base_version,$version_cible){
        $current_version = 0.0;
        if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
               // include_spip('base/gestion_projet_tables_principales');
             //   include_spip('base/gestion_projet_tables_auxiliaires');                
                // cas d'une installation
                if ($version_cible > $GLOBALS['meta'][$nom_meta_base_version]){
                	if($GLOBALS['meta'][$nom_meta_base_version]==''){
                		include_spip('base/create');	
						creer_base();               	
						maj_tables("spip_projets");
						maj_tables("spip_projets_timetracker");	
						maj_tables("spip_projets_taches");			
				}
			else{
				include_spip('base/create');			
				creer_base();		
				maj_tables("spip_projets");
				maj_tables("spip_projets_timetracker");
				maj_tables("spip_projets_taches");							
				}
               }

        }
	ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	ecrire_metas();        
}
function gestion_projets_vider_tables($nom_meta_base_version) {
       sql_drop_table("spip_projets");
       sql_drop_table("spip_projets_timetracker");
       sql_drop_table("spip_projets_taches");        
       effacer_meta($nom_meta_base_version);
}
?>