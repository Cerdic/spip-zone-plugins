<?php

/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

$GLOBALS['spipmine_base_version'] = 0.10;

function spipmine_verifier_base(){
	$version_base = $GLOBALS['spipmine_base_version'];
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta']['spipmine_base_version']) )
			|| (($current_version = $GLOBALS['meta']['spipmine_base_version'])!=$version_base)){
			
		// Cas d'une install toute neuve : la version
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// if (file_exists('base/spipmine.sql')) {
				$sql = trim(@file_get_contents('base/spipmine.sql'));
				sql_query($sql);
			// } 
			ecrire_meta('spipmine_base_version',$current_version=$version_base,'non');
		}
		
		if ($current_version>0.0){
			ecrire_meta('spipmine_base_version',$current_version=0.11,'non');
		}
	}
}

function spipmine_vider_tables() {
	include_spip('base/spipmine');
	include_spip('base/abstract_sql');
	sql_drop_table('spipmine_actions');
	sql_drop_table('spipmine_clients');
	sql_drop_table('spipmine_clients_rubriques');
	sql_drop_table('spipmine_contacts');
	sql_drop_table('spipmine_factures');
	sql_drop_table('spipmine_lignes_facture');
	sql_drop_table('spipmine_projets');
	sql_drop_table('spipmine_reglements');
	sql_drop_table('spipmine_types_actions');
	sql_drop_table('spipmine_types_documents');
	sql_drop_table('spipmine_types_facturation');
	sql_drop_table('spipmine_types_livrables');
	sql_drop_table('spipmine_types_prestations');
	sql_drop_table('spipmine_types_status');
	sql_drop_table('spipmine_users');
	effacer_meta('spipmine_base_version');
}

function spipmine_install($action){
	$version_base = $GLOBALS['spipmine_base_version'];
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['spipmine_base_version']) AND ($GLOBALS['meta']['spipmine_base_version']>=$version_base));
			break;
		case 'install':
			spipmine_verifier_base();
			break;
		case 'uninstall':
			spipmine_vider_tables();
			break;
	}
}
?>