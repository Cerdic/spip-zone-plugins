<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function menus_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		if (version_compare($version_actuelle,'0.0.0','=')){
			// Création des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
		
		if (version_compare($version_actuelle,'0.5.0','<')){
			include_spip('base/abstract_sql');
			
			// AJout de personalisation CSS sur un menu
			sql_alter("TABLE spip_menus ADD COLUMN css tinytext DEFAULT '' NOT NULL");
		}
		
		// On change la version
		ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
	
	}

}

// Désinstallation
function menus_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_menus');
	sql_drop_table('spip_menus_entrees');
		
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);

}

?>
