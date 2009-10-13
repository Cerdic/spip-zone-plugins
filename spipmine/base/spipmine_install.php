<?php

/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

// references : http://doc.spip.org/@Plugin-xml

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
function spipmine_install($action) {

	switch ($action) {  
	
		// La base est deja cree ?
		case 'test':
			// Verifier que la table contacts existe
			$desc = sql_showtable("spipmine_contacts", true);
			break;
	
		// Installer la base
		case 'install':
			include_spip('base/create');  // definir la fonction
			include_spip('base/spipmine'); // definir sa structure
			creer_base();
			break;
	
		// Supprimer la base
		case 'uninstall':
			spip_query('DROP TABLE spipmine_%');
			break;
			
		default :
			break;
	}
}
*/

function spipmine_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	// s'il n'y a pas de N° de version
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	// ou si la version actuelle est différente de la version cible
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		// on inclut le script SPIP de création des tables
		include_spip('base/spipmine');
	
		// cas d'une installation
		if ($current_version==0.0){
	
			// on inclut le script SPIP de création des tables		
			include_spip('base/create');
			maj_tables('spipmine_clients, spipmine_contacts');
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
		}
	}
}
function spipmine_vider_tables($nom_meta_base_version) {
	sql_drop_table("spipmine_clients");
	effacer_meta($nom_meta_base_version);
}

?>