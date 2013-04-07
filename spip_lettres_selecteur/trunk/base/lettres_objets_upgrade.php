<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

	function lettres_objets_upgrade($nom_meta_base_version,$version_cible){
		include_spip('inc/meta');

		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			
			creer_base();
			
			if (version_compare($current_version,'0.1','<')){
				creer_base();
				maj_tables('spip_lettres_liens');
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
        }
	}
?>
