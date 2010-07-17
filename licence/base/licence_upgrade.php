<?php
/**
 * Plugin Licence
 *
 * (c) 2007-2010 fanouch
 * Distribue sous licence GPL
 *
 * Modification des tables
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function licence_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			maj_tables('spip_articles');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/create');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.2);
		}
	}
}


function licence_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}

?>