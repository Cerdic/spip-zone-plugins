<?php
/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - 2010
 * Auteur Cyril MARION - Ateliers CYM
 *
 */

include_spip('inc/meta');
include_spip('base/create');

$GLOBALS['spipmine_base_version'] = 0.10;

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

function spipmine_verifier_base(){
	$version_base = $GLOBALS['spipmine_base_version'];
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta']['spipmine_base_version']) )
			|| (($current_version = $GLOBALS['meta']['spipmine_base_version'])!=$version_base)){
			
		// Cas d'une install toute neuve : la version
		if ($current_version==0.0){
			include_spip('base/create');
			creer_base();
			ecrire_meta('spipmine_base_version',$current_version=$version_base,'non');
		}
		
		if ($current_version>0.0){
			ecrire_meta('spipmine_base_version',$current_version=0.11,'non');
		}
	}
}


function spipmine_vider_tables() {
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


?>