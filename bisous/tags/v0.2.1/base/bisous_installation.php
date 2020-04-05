<?php
/*
 * Plugin SPIP Bisous pour Spip 2.0
 * Licence GPL
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function bisous_upgrade($nom_meta_version_base, $version_cible){

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
			
			echo "Installation du plugin SPIP Bisous !<br/>";
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
		}
		if (version_compare($current_version,'0.1','<=')){
			// on efface tout et on recommence !
			include_spip('base/create');
			include_spip('base/abstract_sql');
			sql_drop_table('spip_bisous');
			creer_base();
			
			echo "Reinstallation du plugin SPIP Bisous !<br/>";
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');			
		}
	
	}

}

// Desinstallation
function bisous_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_bisous');
	
	// On efface la version entregistree
	effacer_meta($nom_meta_version_base);

}

?>
