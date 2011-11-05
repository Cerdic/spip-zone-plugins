<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

// Installation et mise à jour
function parrainage_upgrade($nom_meta_version_base, $version_cible){

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
			
			echo "Installation du plugin parrainage<br/>";
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
		
		/*if (version_compare($version_actuelle,'0.5','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			
			// Modification de parrainage
			sql_alter('');
						
			// On change la version
			echo "Mise à jour du plugin parrainage en version 0.5<br/>";
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}*/
	
	}

}

// Désinstallation
function parrainage_vider_tables($nom_meta_version_base){
	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_filleuls');
		
	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);
}

?>
