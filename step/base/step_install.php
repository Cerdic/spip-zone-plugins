<?php

include_spip('base/create');
include_spip('base/abstract_sql');

function step_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if ($current_version==0.0){
			spip_log('Installation des tables','step');
			creer_base();
			// informer des plugins locaux
			include_spip('inc/step');
			spip_log('Installation de la liste des plugins locaux','step');
			step_actualiser_plugins_locaux();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		else if($current_version==0.4){
			/**
			 * On met à jour la liste des plugins locaux avec categorie = ' ' si aucune catégorie
			 * Permet de pouvoir les retrouver dans le formulaire de recherche
			 */
			include_spip('inc/step');
			spip_log('Installation de la liste des plugins locaux','step');
			step_actualiser_plugins_locaux();
			ecrire_meta($nom_meta_base_version,$current_version=0.5,'non');
		}
	}
}

function step_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_zones_plugins");
	sql_drop_table("spip_plugins");
	effacer_meta($nom_meta_base_version);
}


?>
