<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function noizetier_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		if (version_compare($version_actuelle,'0.0','=')){
			// Création des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			
			// Installation d'une configuration par défaut
			
			echo _T('noizetier:installation_tables');
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
		
		ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
	
	}

}

// Désinstallation
function noizetier_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_noisettes');
	
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
	
	// On efface la version enregistrée
	effacer_meta('noiZetier');

}

?>
