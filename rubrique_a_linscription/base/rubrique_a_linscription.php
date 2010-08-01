<?php
function rubrique_a_linscription_upgrade($nom_meta_base_version,$version_cible){
	$current_version = '0.1';
	if(	(!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		 
		 if (version_compare($current_version,'0.1','<=')){
		 	spip_log('Mise à jour 0.1','rubrique_a_linscription');
		 	ecrire_meta($nom_meta_base_version,$current_version='0.1','non');
		 }
		 
		 if (version_compare($current_version,'0.2','<=')){
		 	spip_log('Mise à jour 0.2','rubrique_a_linscription');
		 	sql_alter("TABLE spip_auteurs ADD rubrique_a_linscription INT");
		 	ecrire_meta($nom_meta_base_version,$current_version='0.2','non');
		 }
				
	}	
}

?>