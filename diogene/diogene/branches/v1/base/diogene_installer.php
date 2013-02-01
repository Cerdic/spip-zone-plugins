<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 *
 * Installation de diogène
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function diogene_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/create');
		// cas d'une installation
		if ($current_version==0.0){
			include_spip('base/diogene');
			creer_base();
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
		}else if(version_compare($current_version,'0.2','<')){
			include_spip('base/diogene');
			creer_base();
			ecrire_meta($nom_meta_base_version, $current_version=0.2, 'non');
		}else if(version_compare($current_version,'0.3','<')){
			include_spip('base/diogene');
			maj_tables('spip_diogenes');
			ecrire_meta($nom_meta_base_version, $current_version=0.3, 'non');
		}else if(version_compare($current_version,'0.3.1','<')){
			include_spip('base/diogene');
			maj_tables('spip_diogenes');
			ecrire_meta($nom_meta_base_version, $current_version="0.3.1", 'non');
		}else if(version_compare($current_version,'0.3.2','<')){
			include_spip('base/diogene');
			maj_tables('spip_diogenes');
			ecrire_meta($nom_meta_base_version, $current_version="0.3.2", 'non');
		}
	}
}

function diogene_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	sql_delete('spip_diogenes');
	sql_delete('spip_diogenes_liens');
}
?>