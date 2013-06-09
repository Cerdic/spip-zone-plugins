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
		 if (version_compare($current_version,'0.3','<=')){
		 	spip_log('Mise à jour 0.3','rubrique_a_linscription');
		 	$meta = unserialize(lire_meta('rubrique_a_linscription'));
		 	$meta['statut'] = '0minirezo';
		 	ecrire_meta('rubrique_a_linscription',serialize($meta));
		 	ecrire_meta($nom_meta_base_version,$current_version='0.3','non');
		 	ecrire_metas();
		 }
		 if (version_compare($current_version,'0.4','<=')){
		 	spip_log('Mise à jour 0.4','rubrique_a_linscription');
		 	$meta = unserialize(lire_meta('rubrique_a_linscription'));
		 	$meta["rubrique_explicite"]=$meta["argument_explicite"];
			unset($meta["argument_explicite"]);
		 	ecrire_meta('rubrique_a_linscription',serialize($meta));
		 	ecrire_meta($nom_meta_base_version,$current_version='0.4','non');
		 	ecrire_metas();
		 }
				
	}	
	ecrire_metas();
}
function rubrique_a_linscription_vider_tables($nom_meta_base_version){
	sql_alter("TABLE spip_auteurs DROP rubrique_a_linscription");
	effacer_meta('rubrique_a_linscription');
	effacer_meta($nom_meta_base_version);
	ecrire_metas();	
}

?>