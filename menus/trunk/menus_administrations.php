<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function menus_upgrade($nom_meta_version_base, $version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('creer_base'),
		array('ecrire_config', 'menus/entrees_masquees', array('rubriques', 'groupe_mots', 'mapage', 'deconnecter', 'secteurlangue')),
	);
	
	$maj['0.5.0'] = array(
		array('sql_alter', "TABLE spip_menus ADD COLUMN css tinytext DEFAULT '' NOT NULL"),
	);
	
	$maj['0.5.1'] = array(
		array('sql_updateq', 'spip_menus_entrees', array('type_entree'=>'rubriques_completes'), 'type_entree = '.sql_quote('rubriques')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
function menus_vider_tables($nom_meta_version_base){
	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_menus');
	sql_drop_table('spip_menus_entrees');
		
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
	// On efface la config
	effacer_meta('menus');
}

?>
