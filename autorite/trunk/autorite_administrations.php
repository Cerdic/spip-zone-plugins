<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip("inc/config");



function autorite_upgrade($nom_meta_base_version,$version_cible){
	$current_version = '0.1';
	if(	(!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		 
		 if (version_compare($current_version,'0.1','<=')){
		 	if (lire_config("autorite/auteur_mod_article"=="on")){
			    ecrire_config("autorite/auteur_modere_forum","on");
			    ecrire_config("autorite/auteur_modere_petition","on");
			}
		 	ecrire_meta($nom_meta_base_version,$current_version='0.1','non');
		 }
				
	}	
	ecrire_metas();
}
function autorite_vider_tables($nom_meta_base_version){
	effacer_meta('autorite');
	effacer_meta($nom_meta_base_version);
	ecrire_metas();	
}

?>