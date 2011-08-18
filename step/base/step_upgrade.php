<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/create');
include_spip('base/abstract_sql');

function step_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/step');
		if (version_compare($current_version,'0.0','<=')){
			spip_log('Installation des tables','step');
			creer_base();
			// informer des plugins locaux
			spip_log('Installation de la liste des plugins locaux','step');
			include_spip('inc/step');
			step_actualiser_plugins_locaux();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if(version_compare($current_version,'0.5','<')){
			/**
			 * On met à jour la liste des plugins locaux avec categorie = ' ' si aucune catégorie
			 * Permet de pouvoir les retrouver dans le formulaire de recherche
			 */
			spip_log('Installation de la liste des plugins locaux','step');
			include_spip('inc/step');
			step_actualiser_plugins_locaux();
			ecrire_meta($nom_meta_base_version,$current_version="0.5");
		}
		if(version_compare($current_version,'0.6','<')){
			/**
			 * On ajoute le champ slogan dans la base
			 */
			maj_tables(array('spip_plugins', 'spip_tickets_forum'));
			include_spip('inc/step');
			step_actualiser_plugins_locaux();
			ecrire_meta($nom_meta_base_version,$current_version="0.6");
		}
	}
}

function step_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_zones_plugins");
	sql_drop_table("spip_plugins");
	effacer_meta($nom_meta_base_version);
}


?>
