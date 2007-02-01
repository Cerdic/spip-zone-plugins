<?php

	include_spip('inc/meta');
	
	function checklink_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/checklink');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				include_spip('inc/checklink');
				checklink_reconstruit_table();
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
	
			ecrire_metas();
		}
	}
	
	function checklink_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_liens");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>