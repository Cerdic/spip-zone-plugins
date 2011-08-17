<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/create');
include_spip('base/abstract_sql');

spip_log("on charge le fichier de step",'test');

function step_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	spip_log("$nom_meta_base_version,$version_cible",'test');
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/step');
		spip_log('maj step','test');
		spip_log($current_version,'test');
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
			spip_log('maj step','test');
			maj_tables(array('spip_plugins', 'spip_tickets_forum'));
			include_spip('inc/step');
			step_actualiser_plugins_locaux();
			ecrire_meta($nom_meta_base_version,$current_version="0.6");
		}else{
			spip_log('rien','test');
		}
	}
	else{
		spip_log('rien2','test');
	}
}

function step_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_zones_plugins");
	sql_drop_table("spip_plugins");
	effacer_meta($nom_meta_base_version);
}


?>
