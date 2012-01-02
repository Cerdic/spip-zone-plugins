<?php
/*
 * Plugin Momo pour Spip 2.0
 * Code tiré de Spip Bisous
 * Licence GPL
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function momo_upgrade($nom_meta_version_base, $version_cible){

	$current_version = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		if (version_compare($current_version,'0.0','<=')){
			// Creation des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			
			echo "Installation du plugin Momo !<br/>";
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
		}
	
	}

}

// Desinstallation
function momo_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_momo');
	
	// On efface la version entregistree
	effacer_meta($nom_meta_version_base);

}

?>
