<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/create');
/*
function sclp_upgrade($nom_meta_base_version,$version_cible){
        $current_version = 0.0;
        if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			
                include_spip('base/sclp');
                // cas d'une installation
                if ($version_cible > $GLOBALS['meta'][$nom_meta_base_version]){
                	if($GLOBALS['meta'][$nom_meta_base_version]==''){
						
				include_spip('base/create');
				creer_base();
				maj_tables('spip_listes');	
				maj_tables('spip_auteurs_listes');									
				maj_tables('spip_auteurs');	 
				maj_tables('spip_listes_syncro');	 
				sql_updateq('spip_listes',array('statut'=>'active'),"statut=''");	
				sql_updateq('spip_listes',array('statut'=>'desactive'),"statut='inact'");	
										
				}
			else{
				include_spip('base/create');
				creer_base();
				maj_tables('spip_listes');	
				maj_tables('spip_auteurs_listes');									
				maj_tables('spip_auteurs');	
				maj_tables('spip_listes_syncro');	 				
				}
               }
            ecrire_meta($nom_meta_base_version, $current_version=$version_cible);    
        }
	    */
	    
function sclp_upgrade($nom_meta_base_version, $version_cible){
	
        $current_version = 0.0;
        if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
                creer_base();
                maj_tables('spip_auteurs');	 
                maj_tables('spip_listes_syncro');	 
                maj_tables('spip_listes');	 
                maj_tables('spip_auteurs_listes');	              
                sql_updateq('spip_listes',array('statut'=>'prive'),"statut=''");	
				sql_updateq('spip_listes',array('statut'=>'poubelle'),"statut='inact'");
			
				ecrire_meta($nom_meta_base_version, $current_version=$version_cible);	
        }
        else{
                // ajout du champ "robe" et "infos"
               //creer_base();
                maj_tables('spip_auteurs');	 
                maj_tables('spip_listes_syncro');	 
                maj_tables('spip_listes');	 
                maj_tables('spip_auteurs_listes');	 
                
                ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
        }
}	    
?>
